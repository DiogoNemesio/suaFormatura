<?php

namespace Zage\App;

/**
 * Gerenciar as máscaras
 *
 * @package \Zage\App\Mascara
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Mascara {
	
	
	/**
	 * 
	 */
	var $_prefixoMascara	= "ZGMask-";
	
	/**
	 * 
	 * Array de máscara
	 * @var array
	 */
	private $_masks = array();

	/**
	 * 
	 * Array de caracteres fixos
	 * @var array
	 */
	private $_fixos = array(
		"[",
		"$",
		"(",
		")",
		",",
		".",
		":",
		"/",
		" ",
		"-",
		"]"
	);
	
	/**
	 * 
	 * Array de caracteres coringas
	 * @var array
	 */
	private $_stars = array(
		"0"		=> "[0-0]",
		"1"		=> "[0-1]",
		"2"		=> "[0-2]",
		"3"		=> "[0-3]",
		"4"		=> "[0-4]",
		"5"		=> "[0-5]",
		"6"		=> "[0-6]",
		"7"		=> "[0-7]",
		"8"		=> "[0-8]",
		"9"		=> "[0-9]",
		"_"		=> "[0-9]",
		"X"		=> "[A-Z]",
		"x"		=> "[a-zA-Z]",
		"*"		=> "[0-9a-zA-ZçÇáàãâéèêíìóòôõúùü]"
	);
	
	
	/**
     * Construtor
	 */
	public function __construct() {
		global $log;

		$log->debug(__CLASS__.": nova Instância");

		/** 
		 * Resgata as máscaras do banco 
		 */
		$mascaras	= $this->DBGetMascaras();
		foreach ($mascaras as $dados) {
			$this->criaMascara(
				$dados->nome,
				$dados->mascara,
				$dados->valorPadrao,
				($dados->indReversa == 0) ? false : true,
				$dados->funcao,
				($dados->indMesmoTamanho == 0) ? false : true,
				$dados->TIPO_MASCARA
			);
		}

	}
	
	/**
	 * 
	 * Criar uma máscara
	 * @param string $nome
	 * @param string $mascara
	 * @param string $valorPadrao
	 */
	private function criaMascara ($nome, $mascara, $valorPadrao = null, $reversa = false, $funcaoValidacao = false,$mesmoTamanho = true,$tipo = T_STRING_CAST) {
		
		/** Verifica se a máscara já existe **/
		if ($this->_existeMascara($nome)) {
			return;
		}
		
		$this->_masks[$nome] = new \TipoMascara();
		$this->_masks[$nome]->setNome($nome);
		$this->_masks[$nome]->setMascara($mascara);
		$this->_masks[$nome]->setValorPadrao($valorPadrao);
		$this->_masks[$nome]->setReversa($reversa);
		$this->_masks[$nome]->setFuncao($funcaoValidacao);
		$this->_masks[$nome]->setMesmoTamanho($mesmoTamanho);
		$this->_masks[$nome]->setTipo($tipo);
	}
	
	/**
     * 
     * Resgatar a mascara
     * @param string $nome
     * @return string $mascara
     */
    public function getMascara ($nome) {
    	if (!$this->_existeMascara($nome)) die('Mascara: '.$nome.' não existe !!!');
		return $this->_masks[$nome]->getMascara();
    }
	
	/**
	 * Resgata o valor Padrão
     * @param string $nome
     * @return string $valorPadrao
	 */
	public function getValorPadrao($nome) {
    	if (!$this->_existeMascara($nome)) die('Mascara: '.$nome.' não existe !!!');
		return $this->_masks[$nome]->getValorPadrao();
	}

	/**
	 * Resgata o valor do campo Reverso
     * @param string $nome
     * @return boolean $reversa
	 */
	public function getReversa($nome) {
    	if (!$this->_existeMascara($nome)) die('Mascara: '.$nome.' não existe !!!');
		return $this->_masks[$nome]->getReversa();
	}
	
	/**
	 * Resgata o indicador de mesmo tamanho
     * @param string $nome
     * @return boolean $mesmoTamanho
	 */
	public function getIndMesmoTamanho($nome) {
    	if (!$this->_existeMascara($nome)) die('Mascara: '.$nome.' não existe !!!');
		return $this->_masks[$nome]->getMesmoTamanho();
	}
	
	/**
	 * Resgata o tipo da mascara
     * @param string $nome
     * @return string $tipo
	 */
	public function getTipo($nome) {
    	if (!$this->_existeMascara($nome)) die('Mascara: '.$nome.' não existe !!!');
		return $this->_masks[$nome]->getTipo();
	}
	
	/**
	 * Verifica se existe a máscara
	 */
	private function _existeMascara($nome) {
		return (array_key_exists($nome, $this->_masks));
	}
	
	/**
	 * Verifica se um caracter é Fixo
	 * @param boolean $char
	 */
	private function _isFixedChar($char) {
		return (in_array($char, $this->_fixos));
	}
	
	/**
	 * Verifica se um caracter é estrela
	 * @param boolean $char
	 */
	private function _isStarChar($char) {
		return (array_key_exists($char, $this->_stars));
	}
	
	/**
	 * Aplicar mascara em uma string 
	 * 
	 * @param string $mascara
	 * @param string $string
	 */
    public function aplicaMascara ($nomeMascara,$string) {
    	global $system;
    	
    	/** Resgata a máscara **/
    	$mascara	= $this->getMascara($nomeMascara);
    	//$log->debug('NomeMascara: '.$nomeMascara. ' String: '.$string.' Mascara: '.$mascara);
    	
    	/** Verifica se a string foi passada, senão retornar o valor padrão da máscara **/
    	if (!$string || $string == null|| $string == '') {
    		if ($this->getValorPadrao($nomeMascara) != null ) return $this->getValorPadrao($nomeMascara); 
    	}
    	
    	/** Verifica se precisa adequar a string **/
    	if ($this->getTipo($nomeMascara) == T_DOUBLE_CAST) {
    		$string	= number_format($string,2,'.',',');
    		$string = str_replace('.', '', $string);
    		$string = str_replace(',', '', $string);
    		//$log->debug('NomeMascara: '.$nomeMascara. ' String: '.$string.' Numero: '.$string);
    	}
    	
    	/** Transforma a string em um array **/
    	$str	= str_split($string);
    	
    	/** Transforma a mascara em um array **/
    	$mask	= str_split($mascara);

    	/** Verifica se a mascara é reversa **/
    	if ($this->getReversa($nomeMascara) == true) {
    		$str = array_reverse($str);
    		//$log->debug('String Reversa: '.implode('',$str));
    	}
    	
		/** Mapeia os characters fixos e estrelas da mascara **/
    	$numStars	= 0;
    	$return		= array();
    	for ($i = 0; $i < sizeof($mask); $i++) {
    		if ($this->_isStarChar($mask[$i])) {
	  			
	  			/** Faz a validação **/
	   			if ($this->_validaPosicaoMascara($nomeMascara, $str[$numStars], $i)) {
    				$return[]	= $str[$numStars];
    				$numStars++;
	   			}else{
	   				die('Caractere inválido ('.$str[$numStars].') na posição: '.$i.' mascara: '.$mascara);
	   			}
	   			
    		}elseif ($this->_isFixedChar($mask[$i])) {
    			$return[]	= $mask[$i];
    		}
    		if ($numStars == sizeof($str)) break;
    	}
    	
    	/** Verifica se a mascara é reversa **/
    	if ($this->getReversa($nomeMascara) == true) {
    		$return	= array_reverse($return);
    	}
    	$return	= implode("", $return);
    	
    	return ($return);
    }
    
    /**
     * 
     * Resgatar a posição de um caracter estrela em uma máscara
     * @param array $mascara
     * @param integer $index
     */
    private function _getPosStarCharFromMask($nomeMascara,$index) {

    	/** Resgata a máscara **/
    	$mascara	= $this->getMascara($nomeMascara);
    	
    	/** Transforma a mascara em um array **/
    	$mask	= str_split($mascara);

    	/** Cria array de caracteres estrelas **/
    	$stars	= array();
    	for ($i = 0; $i < sizeof($mask); $i++) {
    		if ($this->_isStarChar($mask[$i])) $stars[] = $i;
    	}
    	return $stars[$index];
    	
    }

	/**
	 * Valida se uma posição está valida 
	 * 
	 * @param string $mascara
	 * @param string $char
	 * @param number $pos
	 */
    private function _validaPosicaoMascara ($nomeMascara,$char,$pos) {
    	global $system;
    	
    	/** Resgata a máscara **/
		$mascara	= $this->getMascara($nomeMascara);
		
		/** Converte a mascar em um array **/
		$mask	= str_split($mascara);
		
    	//$log->debug('Validação: nomeMascara:'.$nomeMascara. ' Char: '.$char.' Pos:'.$pos);
    	
    	/** Resgata a expressão regular de validação dessa string **/
    	$regexp	= $this->_stars[$mask[$pos]];
    	
    	return (preg_match("/".$regexp."/", $char));
    }
    
	/**
	 * Valida a mascara de uma string 
	 * 
	 * @param string $mascara
	 * @param string $string
	 */
    public function validaMascara ($nomeMascara,$string) {
    	global $log;
    	
    	//$log->debug('NomeMascara: '.$nomeMascara. ' String: '.$string);
    	
    	/** Resgata a máscara **/
    	$mascara	= $this->getMascara($nomeMascara);
    	
    	/** Transforma a string em um array **/
    	$str	= str_split($string);
    	
    	/** Transforma a mascara em um array **/
    	$mask	= str_split($mascara);
    	
    	/** Verifica se a mascara obriga o campo a ter o mesmo tamanho **/
    	if (($this->getIndMesmoTamanho($nomeMascara) == true) && ((sizeof($str) <> sizeof($mask)))) {
    		return false;
    	}
    	
    	//$log->debug('Mascara: '.$mascara);
    	
    	/** Verifica se a mascara é reversa **/
    	if ($this->getReversa($nomeMascara) == true) {
    		$str = array_reverse($str);
    	}

    	for ($i = 0; $i < sizeof($str); $i++) {
	   		/** Verifica se a posição atual é um caracter fixo **/
    		if ($this->_isFixedChar($mask[$i]) == true) {
    			//$log->debug('Fixed: I = '.$i. ' mask[$i]: '.$mask[$i]. ' str[$i] = '.$str[$i]);
    			if ($mask[$i] !== $str[$i]) return false;
    		}elseif ($this->_isStarChar($mask[$i]) == true) {
    			//$log->debug('Star: I = '.$i. ' mask[$i]: '.$mask[$i]. ' str[$i] = '.$str[$i]." RegExp: $regexp");
    			if ($this->_validaPosicaoMascara($nomeMascara, $mask[$i], $i) == false) return false;
	    	}else{
	    		die('Mascara inválida');
	    	}
	    }
    	return true;
    }

	/**
	 * Retira a mascara de uma string 
	 * 
	 * @param string $mascara
	 * @param string $string
	 */
    public function retiraMascara ($nomeMascara,$string) {
    	global $system;
    	
    	//$log->debug('NomeMascara: '.$nomeMascara. ' String: '.$string);
    	
    	/** Resgata a máscara **/
    	$mascara	= $this->getMascara($nomeMascara);
    	
    	/** Se a string for igual a mascara,retornar null **/
    	if ($mascara == $string) {
    		return null;
    	}
    	
    	/** Se a máscara não for válida, retorna a própria string **/
    	if ($this->validaMascara($nomeMascara, $string) == false) {
    		return $string;
    	}
    	
    	/** Transforma a string em um array **/
    	$str	= str_split($string);
    	
    	/** Transforma a mascara em um array **/
    	$mask	= str_split($mascara);
    	
    	/** Verifica se a mascara é reversa **/
    	if ($this->getReversa($nomeMascara) == true) {
    		$str = array_reverse($str);
    	}
    	
    	/** Retira os caracteres não fixos **/
    	$return = '';
    	for ($i = 0; $i < sizeof($str); $i++) {
    		if ($this->_isFixedChar($mask[$i]) == false) {
    			$return	.= $str[$i];
    		}
    	}

    	/** Verifica se a mascara é reversa **/
    	if ($this->getReversa($nomeMascara) == true) {
    		$ret	= str_split($return);
    		$ret	= array_reverse($ret);
    		$return	= implode("", $ret);
    	}
    	
    	/** Verifica se precisa adequar a string **/
    	if ($this->getTipo($nomeMascara) == T_DOUBLE_CAST) {
    		$return	= number_format($return/100,2,'.','');
    	}
    	
    	return ($return);
    }
    
    
    /**
     * Gerar o código javascript para a configuração do JQuery Meio Mask
     */
    public function geraConfigMeioMask () {
    	/** Gera o código javascript das máscaras **/
    	$config	= '';
    	foreach ($this->_masks as $mask) {
    		$config .= " '".$mask->getNome()."' : { mask : '".$mask->getMascara()."' ";
    		if ($mask->getValorPadrao() != null) {
    			$config .= " , defaultValue : '".$mask->getValorPadrao()."' ";
    		}
    		if ($mask->getReversa() == true) {
    			$config .= " , type : 'reverse' ";
    		}
    		
    		$config .= ' },'.PHP_EOL;
    	}
		return (substr($config,0,-2));
    }

    /**
     * Gerar o código javascript para a os caracteres fixos
     */
    public function geraCaracteresFixos () {
    	$js	= '';
    	foreach ($this->_fixos as $char) {
    		$js .= $char;
    	}
		return ($js);
    }
    
    /**
     * Gerar o código javascript para a os caracteres estrelas
     */
    public function geraCaracteresEstrelas () {
    	$js	= '';
    	foreach ($this->_stars as $char => $value) {
    		$js .= "'".$char."': /".$value."/,".PHP_EOL;
    	}
		return (substr($js,0,-2));
    }
    
    /**
	 * Valida as máscaras de um formulário
	 *
	 * @param string $xml
	 * @return string $campo || true
	 */
	public function validaMascarasForm ($arqXml) {
		global $system;

		/** Seleciona todos os inputs do XML **/
		$inputs = MegaCondominio::getXmlInputs($arqXml);

		foreach ($inputs as $key => $value) {
			
			$valido		= false;
			
			/** Resgata a máscara **/
			preg_match('#'.$this->_prefixoMascara.'(.+)#', $value->className,$mask);
			$mascara	= $this->getMascara($mask[0]);
			
			/** Muda o escopo da variável **/
			$eval	= 'global $'.$value->name.';';
			eval($eval);

			/** Valida a mascara da variável global **/
			$eval1	= '($mascara == $'.$value->name.') ? $valido = true : $valido = $this->validaMascara(\''.$mask[0].'\', $'.$value->name.');';
			//$log->debug("Eval: ".$eval1); 
			eval ($eval1);
			
			/** Se o campo não for válido, retornar o nome dele **/
			if ($valido == false) return ($value->name);
				
		}
		
		/** Se não retornou algum erro, todos os campos foram validados com sucesso **/
		return (true);
		
	}
    
	/**
	 * Retira as máscaras de um formulário
	 *
	 * @param string $xml
	 */
	public function retiraMascarasForm ($arqXml) {
		global $system;
		
		/** Seleciona todos os inputs do XML **/
		$inputs = \Zage\Util::getXmlInputs($arqXml);
		
		foreach ($inputs as $key => $value) {
			
			/** Resgata a máscara **/
			preg_match('#'.$this->_prefixoMascara.'(.+)#', $value->mascara,$mask);
			
			/** Muda o escopo da variável **/
			$eval	= 'global $'.$value->name.';';
			eval($eval);

			/** Retira a mascara da variável global **/
			$eval1	= '$'.$value->name.' = $this->retiraMascara(\''.$mask[0].'\', $'.$value->name.');';
			eval ($eval1);

		}
	}
	
	/**
	 * Aplicar as máscaras em um formulário
	 *
	 * @param string $xml
	 */
	public function aplicaMascarasForm ($arqXml) {
		global $system;
		
		/** Seleciona todos os inputs do XML **/
		$inputs = \Zage\Util::getXmlInputs($arqXml);

		foreach ($inputs as $key => $value) {
			
			/** Resgata a máscara **/
			preg_match('#'.$this->_prefixoMascara.'(.+)#', $value->mascara,$mask);
			
			/** Muda o escopo da variável **/
			$eval	= 'global $'.$value->name.';';
			eval($eval);

			/** Retira a mascara da variável global **/
			$eval1	= '$'.$value->name.' = $this->aplicaMascara(\''.$mask[0].'\', $'.$value->name.');';
			//$log->debug("Eval: ".$eval1); 
			eval ($eval1);

		}
	}
	
	
	/**
     * Valida os campos do tipo CEP
     *
     * @param string $string
     * @return boolean
     */
    public static function validaCep ($string) {
    	$mascara	= "_____-___";
    	return (\Mascara::validaMascara($mascara, $string));
    }
    
    /**
     * 
     * Resgata as máscaras do banco
     * @return array
     */
    public function DBGetMascaras() {
    	global $db; 
    	return($db->extraiTodos("
			SELECT	M.*,TM.descricao TIPO_MASCARA
			FROM	MASCARAS M,
					TIPO_MASCARA TM
			WHERE	M.codTipo			= TM.codTipo
			ORDER	BY M.nome
			"));
    }
    
    

	/**
     * Valida os campos do tipo Fone
     *
     * @param string $string
     * @return boolean
     */
    public static function validaFone ($string) {
    	$mascara	= "(__) ____-____";
    	return (\Mascara::validaMascara($mascara, $string));
    }
    
	/**
     * Valida os campos do tipo Money
     *
     * @param string $string
     * @return boolean
     */
    public static function validaMoney ($mascara,$string) {
    	global $log;
    	if (!$this->_existeMascara($mascara)) {
    		die('Mascara: '.$mascara. ' não existe !!! ');
    	}
    	
    	$log->debug('Mascara: '.$mascara. " String: ".$string);
    }
    
}
<?php

namespace Zage\App\Mascara;

/**
 * Gerenciar os tipos de Mascaras
 *
 * @package \Zage\App\Mascara\Tipo
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
abstract class Tipo {
	
	/**
	 * Tipos de Mascaras
	 */
	const TP_FONE 			= "fone";
	const TP_FONE11 		= "fone11";
	const TP_CEP 			= "cep";
	const TP_CNPJ 			= "cnpj";
	const TP_CPF 			= "cpf";
	const TP_DATA 			= "data";
	const TP_TEMPO			= "tempo";
	const TP_CARTAO			= "cartao";
	const TP_DINHEIRO		= "dinheiro";
	const TP_NUMERO			= "numero";
	const TP_PORCENTAGEM	= "porcentagem";
	const TP_PLACA			= "placa";
	
	
	/**
	 * Array de máscara
	 * @var array
	 */
	private $_masks = array();
	
	/**
	 * Array de caracteres coringas
	 * @var array
	*/
	private $_digitos = array(
		"0"		=> "D0",
		"1"		=> "D1",
		"2"		=> "D2",
		"3"		=> "D3",
		"4"		=> "D4",
		"5"		=> "D5",
		"6"		=> "D6",
		"7"		=> "D7",
		"8"		=> "D8",
		"9"		=> "D9",
		"~"		=> "SIG",
		"#"		=> "DR",
		"A"		=> "A",
		"a"		=> "a",
		"S"		=> "L",
		"@"		=> "LN",
		"*"		=> "T"
	);
	
	
	/**
	 * Tipo da Máscara
	 * @var number
	 */
	protected $tipo;
	
	/**
	 * Mascara
	 * @var string
	 */
	protected $mascara;
	
	/**
	 * Indica se a máscara tem tamanho Fixo
	 * @var boolean
	 */
	protected $indTamanhoFixo;
	
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
	}
	
	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param number $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 * @return the $mascara
	 */
	public function getMascara() {
		return $this->mascara;
	}

	/**
	 * @param string $mascara
	 */
	public function setMascara($mascara) {
		$this->mascara = $mascara;
	}

	/**
	 * @return the $indTamanhoFixo
	 */
	public function getIndTamanhoFixo() {
		return $this->indTamanhoFixo;
	}

	/**
	 * @param boolean $indTamanhoFixo
	 */
	public function setIndTamanhoFixo($indTamanhoFixo) {
		$this->indTamanhoFixo = $indTamanhoFixo;
	}

	/**
	 * Carregar as configurações do banco de dados
	 */
	protected function _loadConfigFromDb() {
		global $em;
		
		if ($this->getTipo() != null) {
			$info	= $em->getRepository('Entidades\ZgappMascara')->findOneBy(array('nome' => $this->getTipo()));
			
			if ($info) {
				$this->setMascara($info->getMascara());
				$this->setIndTamanhoFixo($info->getIndTamanhoFixo());
			}
			
		}
		
	}
	
	
	/**
	 * Aplicar a mascara a uma determinada string
	 * @param string $string
	 */
	public function aplicaMascara($string) {
		global $log;
		
		if ($this->getTipo() == self::TP_FONE) {
			if (strlen($string) == 11) {
				$this->setTipo(self::TP_FONE11);
				$this->_loadConfigFromDb();
			}
		}elseif ($this->getTipo() == self::TP_FONE11) {
			if (strlen($string) == 10) {
				$this->setTipo(self::TP_FONE);
				$this->_loadConfigFromDb();
			}
		}
		
		/** Converte as strings em array **/
		$am		 	= str_split($this->getMascara());
		$as		 	= str_split($string);
		$result		= "";
		$index		= 0;
		
		for ($i = 0; $i < sizeof($am); $i++) {
			if (array_key_exists($am[$i],$this->_digitos)) {
				if (isset($as[$index])) {
					$result	.= $as[$index];
					$index++;
				}
			}else{
				$result	.= $am[$i];
			}
		}
		
		return $result;
	}
	
	/**
	 * Retirar a mascara a uma determinada string
	 * @param string $string
	 */
	public function retiraMascara($string) {
		global $log;
		
		/** Converte as strings em array **/
		$am		 	= str_split($this->getMascara());
		$as		 	= str_split($string);
		$result		= "";
		
		for ($i = 0; $i < sizeof($am); $i++) {
			if (array_key_exists($am[$i],$this->_digitos)) {
				$result	.= $as[$i];
			}
		}
		
		return $result;
	}
	
	
}

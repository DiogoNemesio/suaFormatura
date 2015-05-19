<?php

namespace Zage\App;

/**
 * Gerenciar as Arvores
 *
 * @package \Zage\App\Arvore
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */ 
class Arvore {
	

	/**
	 * Array de itens da árvore (Itens e Pastas)
	 * @var array
	 */
	private $itens;
	
	/**
	 * Array
	 * @var array
	 */
	private $_array;
	
	/**
	 * Indicador se vai exibir ou não a pasta raiz
	 * @var Boolean
	 */
	private $exibeRaiz;
	
	/**
	 * Código da pasta raiz, quando exibida
	 * @var string
	 */
	const _codPastaRaiz = "_pastaRaiz";
	
	/**
	 * nome da pasta raiz, quando exibida
	 * @var string
	 */
	const _nomePastaRaiz = "";
	
	/**
	 * Construtor
	 *
	 * @param string $tipo
	 * @return void
	 */
	public function __construct() {
		
		/** Inicializa os arrays **/
		$this->itens	= array();
		$this->_array	= array();
		
		/** Define valor padrão de configurações **/
		$this->exibirRaiz(false);
	
	}
	
	/**
	 * Adiciona uma pasta
	 * @param integer $codigo
	 * @param string $nome
	 */
	public function adicionaPasta($codigo,$nome,$pastaMae) {
		global $system,$tr;

		/**
		 * Define o código
		 */
		if ($codigo == $this::_codPastaRaiz) {
			$id			= $codigo;
		}else{
			$id			= "P".$codigo;
		}

		if ($this->exibeRaiz	== true) {
			if ($codigo == $this::_codPastaRaiz) {
				$mae 		= null;
			}else{
				$mae		= ($pastaMae == null) ? $this::_codPastaRaiz : "P".$pastaMae;
			}
		}else{
			$mae		= ($pastaMae == null) ? null : "P".$pastaMae;
		}
		
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($id) == true) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Código de pasta já existe na árvore (%s)",array("%s" => $codigo)));
			die('Código já existente ('.$codigo.')');
		}
		
		/**
		 * Verifica se a pasta mãe existe
		 */
		if (($pastaMae !== null) && ($this->existeItem($mae) == false) ) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Pasta mãe não existe na árvore (%s)",array("%s" => $pastaMae)));
			die('Pasta mãe não existe ('.$pastaMae.')');
		}
		
		/**
		 * Cria a pasta
		 */
		$this->itens[$id]	= new \Zage\App\Arvore\Pasta();
		$this->itens[$id]->setId($id);
		$this->itens[$id]->setNome($nome);
		$this->itens[$id]->setPastaMae($pastaMae);
		$this->itens[$id]->setIdMae($mae);
		
		
		if ($codigo == $this::_codPastaRaiz) {
			$this->itens[$id]->setCodigo(null);			
		}else{
			$this->itens[$id]->setCodigo($codigo);
		}
		
		return ($id);
		
	}
	
	
	/**
	 * Adiciona um item
	 * @param integer $codigo
	 * @param string $nome
	 */
	public function adicionaItem($codigo,$nome,$pastaMae) {
		global $system,$tr;
			
		/**
		 * Define o ID do Objeto
		 */
		$id			= "I".$codigo;
		
		if ($this->exibeRaiz	== true) {
			$mae		= ($pastaMae == null) ? $this::_codPastaRaiz : "P".$pastaMae;
		}else{
			$mae		= ($pastaMae == null) ? null : "P".$pastaMae;
		}
		
		//$mae		= ($pastaMae == null) ? null : "P".$pastaMae;
		
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($id) == true) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Código de item já existe na árvore (%s)",array("%s" => $codigo)));
			die('Código já existente ('.$codigo.')');
		}
	
		/**
		 * Verifica se a pasta mãe existe
		 */
		if (($pastaMae !== null) && ($this->existeItem($mae) == false) ) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Pasta mãe não existe na árvore (%s)",array("%s" => $pastaMae)));
			die('Pasta mãe não existe ('.$pastaMae.')');
		}
	
		/**
		 * Cria o Item
		 */
		$this->itens[$id]	= new \Zage\App\Arvore\Item();
		$this->itens[$id]->setId($id);
		$this->itens[$id]->setCodigo($codigo);
		$this->itens[$id]->setNome($nome);
		$this->itens[$id]->setPastaMae($pastaMae);
		$this->itens[$id]->setIdMae($mae);
		
		return ($id);
	
	}
	
	/**
	 * Verifica se existe o item
	 * @param integer $codigo
	 * @return boolean
	 */
	protected function existeItem($codigo) {
		if (!$this->itens) return false;
		if (array_key_exists($codigo, $this->itens)) {
			return true;
		}else{
			return false;
		}
	}
	
	
	
	/**
	 * Gera o array na ordem correta de nível e ordem
	 */
	public function geraArray() {
		global $nivel,$nivelMax;
	
		/**
		 * Define os contadores para não deixar acontecer uma recursividade
		 */
		$nivel			= 0;
		$nivelMax		= 500;
	
		/**
		 * Primeiro percorre o nível 0, os itens que não tem pai
		 **/
		foreach ($this->itens as $codigo => $obj) {
			if ($obj->getPastaMae() == null) {
				//$this->_array[$codigo]	= array();
				//$this->_array[$codigo]['additionalParameters']['children'] =  $obj->getArray();
				$this->_array[$codigo] =  $obj->getArray();
			}
		}
	
		if ($this->_array) {
			/**
			 * Encontrar os filhos
			 **/
			foreach ($this->_array as $codigo => $array) {
				$this->descobreItensFilhos($this->_array[$codigo]['additionalParameters']['children'], $codigo);
			}
	
		}
		return ($this->_array);
	}
	
	
	/**
	 * Gera o código JSON na ordem correta de nível e ordem
	 */
	public function getJsonCode() {
		global $nivel,$nivelMax;
	
		/**
		 * Define os contadores para não deixar acontecer uma recursividade
		 */
		$nivel			= 0;
		$nivelMax		= 500;
		$json			= "[ ";
	
		
		if ($this->exibeRaiz == true) {
			$_itens	= $this->itens;
			$this->itens	= null;
			$this->adicionaPasta($this::_codPastaRaiz,$this::_nomePastaRaiz,null);
			$this->itens = array_merge($this->itens, $_itens);
		}
		
		/**
		 * Percorre os itens para gerar o código JSON deles
		 **/
		foreach ($this->itens as $codigo => $obj) {
			$json .= " ".$obj->getJsonCode().",";
		}
	
		$json 		.= " ]";
		return ($json);
	}
	
	
	/**
	 * Descobre os filhos do $item no $this->_array e coloca em $array
	 * @param array $array
	 * @param string $item
	 */
	protected function descobreItensFilhos(&$array,$item) {
		global $nivel,$nivelMax;
		$nivel++;
		foreach ($this->itens as $codigo => $obj) {
			if ($obj->getPastaMae() == $item) {
				//$array[$codigo] 		= array();
				$array[$codigo]			= $obj->getArray();
				//$array[$codigo]['additionalParameters']['children'] =  $obj->getArray();
				$this->descobreItensFilhos($array[$codigo]['additionalParameters']['children'], $codigo);
			}
			if ($nivel > $nivelMax) die('Recursividade encontrada em :'.__FUNCTION__);
		}
	}
	
	/**
	 * Definir o nível dos itens da árvore
	 * @param array $array
	 * @param Integer $nivel
	 */
	protected function defineNivel(&$array,$nivel) {
		//return;
		foreach ($array as $cod => $arr) {
			$this->itens[$cod]->setNivel($nivel);
			if (!empty($arr)) {
				$this->defineNivel($array[$cod], $nivel+1);
			}
		}
	}
	
	/**
	 * Filtrar os itens da árvore
	 * @param unknown $string
	 * @return multitype:NULL
	 */
	public function filtrar($string) {
		global $log;

		

		if ($this->exibeRaiz == true) {
			$_itens	= $this->itens;
			$this->itens	= null;
			$this->adicionaPasta($this::_codPastaRaiz,$this::_nomePastaRaiz,null);
			$this->itens = array_merge($this->itens, $_itens);
		}
		
		$array	= array();
		if ($this->itens) {
			foreach ($this->itens as $codigo => $obj) {
				if ($obj->getTipo() == 'item') {
					if (stripos(strtolower($obj->getNome()),strtolower($string)) !== false) {
						$array[]	= $codigo;
						//$log->debug("Achei o Tipo: ".$codigo);
					}
				}
			}
		}

		
		/**
		 * Deixar as pastas mãe e os tipos encontrados no filtro.
		 */
		if ($this->itens && sizeof($array) > 0) {
			foreach ($array as $item) {
				$this->descobrePastasMae($array,$item);
			}
		}
		
		//$log->debug("Itens: ".serialize($this->itens));
		
		$array = array_reverse($array);
		$itens	= array();
		
		foreach ($array as $item) {
			$itens[$item]	= $this->itens[$item];
		}
		
		$this->itens	= $itens;
	}
	
	/**
	 * Decobrir as pastas mãe de um item
	 * @param array $array
	 * @param int $item
	 */
	protected function descobrePastasMae(&$array,$item) {
		if ($this->itens) {
			foreach ($this->itens as $codigo => $obj) {
				if ($codigo == $item) {
					if ($obj->getIdMae() != null) {
						$array[]		= $obj->getIdMae();
						$this->descobrePastasMae($array, $obj->getIdMae());
					}
				}
			}
		}	
	
	}
	
	/**
	 * Exibir a pasta raíz
	 * @param boolean $val
	 */
	public function exibirRaiz($val) {
		$this->exibeRaiz	= $val;
	}
	
	
	/**
	 * Adicionar um atributo customizado a um item
	 * @param number $codigo
	 * @param string $atributo
	 * @param string $valor
	 */
	public function adicionaAtributoItem($codigo,$atributo,$valor) {
		
		/**
		 * Definindo o ID do objeto
		 */
		$id			= "I".$codigo;
		
		if (!$this->existeItem($id)) {
			die('Item não existe na árvore ('.$codigo.')');
		}
		
		$this->itens[$id]->adicionaAtributo($atributo,$valor);
		
	}

	/**
	 * Adicionar um atributo customizado a uma pasta
	 * @param number $codigo
	 * @param string $atributo
	 * @param string $valor
	 */
	public function adicionaAtributoPasta($codigo,$atributo,$valor) {
	
		/**
		 * Definindo o ID do objeto
		 */
		$id			= "P".$codigo;
	
		if (!$this->existeItem($id)) {
			die('Item não existe na árvore ('.$codigo.')');
		}
	
		$this->itens[$id]->adicionaAtributo($atributo,$valor);
	
	}
	
	/**
	 * Desabilita um item
	 * @param string $id
	 */
	public function desabilitaItem($id) {
		if ($this->existeItem($id)) {
			if ($this->itens[$id]->getTipo() == "item") {
				$this->itens[$id]->setIndAtivo(false);
			}else{
				$this->itens[$id]->setIndAtiva(false);
			}
		}
	}
	

}

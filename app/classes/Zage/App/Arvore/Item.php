<?php

namespace Zage\App\Arvore;

/**
 * Gerenciar os itens da árvore
 *
 * @package Item
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Item {
	
	/**
	 * Unique ID
	 * @var string
	 */
	private $id;
	
	/**
	 * Codigo
	 * @var string
	 */
	private $codigo;
	
	/**
	 * Nome
	 * @var string
	 */
	private $nome;
	
	/**
	 * Tipo
	 *
	 * @var string
	 */
	private $tipo;
	
	/**
	 * Id Mãe
	 * @var string
	 */
	private $idMae;
	
	/**
	 * Pasta Mãe
	 * @var string
	 */
	private $pastaMae;
	
	/**
	 * Indicador de item selecionado
	 * @var int
	 */
	private $indSelecionado;
	
	/**
	 * Indicador de Ativo
	 * @var int
	 */
	private $indAtivo;
	
	/**
	 * Nivel
	 * @var int
	 */
	private $nivel;
	
	/**
	 * Atributos
	 * @var array
	 */
	private $atributos;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		/**
		 * Definir o tipo
		 */
		$this->setTipo("item");
		
		/**
		 * Definir os padrões
		 */
		$this->setIndAtivo('true');
		$this->setIndSelecionado('false');
		
		/**
		 * Inicializa o array de atributos
		 */
		$this->atributos	= array();
		
	}
	
	/**
	 * Gerar o array do item
	 */
	public function getArray() {
		$nome	= 	"<span id='spanItemPasta_".$this->getCodigo()."' data-rel='zgItemPasta'>".htmlentities($this->getNome())."</span>";
		$item = array(
			'name' 						=> $nome,
			'type' 						=> $this->getTipo(),
			'additionalParameters' 		=>  array('codItem' => $this->getCodigo(),'codPasta' => $this->getPastaMae())
		);
		$item['additionalParameters']['children'] = false;
		
		return ($item);
		
	}
	
	/**
	 * Gerar o código JSON do item
	 */
	public function getJsonCode() {
		
		/**
		 * Carrega os atributos em uma variável
		 */
		$atrStr	= "";
		if ($this->atributos) {
			foreach ($this->atributos as $atributo => $valor) {
				$atrStr .= ', "'.$atributo.'" : "'.$valor.'"';
			}
		}
		
		$idMae		= ($this->getIdMae() == null) 	? "#"		: $this->getIdMae();
		$disabled	= ($this->getIndAtivo()	== "true") 	? "false" 	: "true";
		$json		= '{ "id" : "'.$this->getId().'", "parent" : "'.$idMae.'", "text" : "'.htmlentities($this->getNome()).'", "icon" : "fa fa-file-text green", "zgCodigo" : "'.$this->getCodigo().'","zgParent" : "'.$this->getPastaMae().'","zgNome" : "'.$this->getNome().'", "zgTipo" : "item", "state" : { "opened" : false, "disabled" : '.$disabled.', "selected" : '.$this->getIndSelecionado().' } ' .$atrStr. ' }';
		return ($json);
	}
	
	
	
	/**
	 * Selecionar um item
	 */
	public function selecionar() {
		$this->setIndSelecionado(true);
	}
	
	/**
	 * Retirar a seleção de um item
	 */
	public function desselecionar() {
		$this->setIndSelecionado(false);
	}
	
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return the $codigo
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param string $codigo
	 */
	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}

	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param string $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param string $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	/**
	 * @return the $idMae
	 */
	public function getIdMae() {
		return $this->idMae;
	}

	/**
	 * @param string $idMae
	 */
	public function setIdMae($idMae) {
		$this->idMae = $idMae;
	}

	/**
	 * @return the $pastaMae
	 */
	public function getPastaMae() {
		return $this->pastaMae;
	}

	/**
	 * @param string $pastaMae
	 */
	public function setPastaMae($pastaMae) {
		$this->pastaMae = $pastaMae;
	}

	/**
	 * @return the $indSelecionado
	 */
	public function getIndSelecionado() {
		return $this->indSelecionado;
	}

	/**
	 * @param number $indSelecionado
	 */
	public function setIndSelecionado($indSelecionado) {
		$this->indSelecionado = $indSelecionado;
	}
	/**
	 * @return the $indAtivo
	 */
	public function getIndAtivo() {
		return $this->indAtivo;
	}

	/**
	 * @param number $indAtivo
	 */
	public function setIndAtivo($indAtivo) {
		$this->indAtivo = $indAtivo;
	}

	/**
	 * @return the $nivel
	 */
	public function getNivel() {
		return $this->nivel;
	}

	/**
	 * @param int $nivel
	 */
	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}


	/**
	 * Adicionar um atributo ao item
	 * @param string $atributo
	 * @param string $valor
	 */
	public function adicionaAtributo($atributo,$valor) {
		$this->atributos[$atributo] = $valor;
	}
	
}

<?php

namespace Zage\App\Arvore;

/**
 * Gerenciar as pastas da árvore
 *
 * @package Pasta
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Pasta {
	
	
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
	 * Indicador de pasta aberta
	 * @var int
	 */
	private $indAberta;
	
	/**
	 * Indicador de Pasta selecionada
	 * @var int
	 */
	private $indSelecionada;
	
	/**
	 * Indicador de Ativa
	 * @var int
	 */
	private $indAtiva;
	
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
		$this->setTipo("folder");
		
		/**
		 * Definir os padrões
		 */
		$this->setIndAberta('false');
		$this->setIndAtiva('true');
		$this->setIndSelecionada('false');
		
		/**
		 * Inicializa o array de atributos
		 */
		$this->atributos	= array();
		
	}
	
	/**
	 * Gerar o array da pasta
	 */
	public function getArray() {
		
		$nome	= 	"<span id='spanPasta_".$this->getCodigo()."' data-rel='zgPasta'>".htmlentities($this->getNome())."</span>";
		$item = array(
			'name' 						=> $nome,
			'type' 						=> $this->getTipo(),
			'icon-class' 				=> 'blue',
			'additionalParameters' 		=>  array('codPasta' => $this->getCodigo())
		);
		$item['additionalParameters']['children'] = array();
	
		return ($item);
	
	}
	
	/**
	 * Gerar o código JSON da pasta
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
		$disabled	= ($this->getIndAtiva()	== "true") 	? "false" 	: "true";
		$json	= '{ "id" : "'.$this->getId().'", "parent" : "'.$idMae.'", "text" : "'.htmlentities($this->getNome()).'", "icon" : "fa fa-folder blue", "zgCodigo" : "'.$this->getCodigo().'","zgParent" : "'.$this->getPastaMae().'","zgNome" : "'.$this->getNome().'", "zgTipo" : "folder", "state" : { "opened" : '.$this->getIndAberta().', "disabled" : '.$disabled.' , "selected" : '.$this->getIndSelecionada().' } ' .$atrStr. ' }';
		return ($json);
	}
	
	
	/**
	 * Abrir uma pasta
	 */
	public function abrir() {
		$this->setIndAberta(true);
	}

	/**
	 * Fechar uma pasta
	 */
	public function fechar() {
		$this->setIndAberta(false);
	}
	
	/**
	 * Selecionar uma pasta
	 */
	public function selecionar() {
		$this->setIndSelecionada(true);
	}
	
	/**
	 * Retirar a seleção de uma pasta
	 */
	public function desselecionar() {
		$this->setIndSelecionada(false);
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
	 * @return the $indAberta
	 */
	public function getIndAberta() {
		return $this->indAberta;
	}

	/**
	 * @param number $indAberta
	 */
	public function setIndAberta($indAberta) {
		$this->indAberta = $indAberta;
	}
	
	/**
	 * @return the $indSelecionada
	 */
	public function getIndSelecionada() {
		return $this->indSelecionada;
	}

	/**
	 * @param number $indSelecionada
	 */
	public function setIndSelecionada($indSelecionada) {
		$this->indSelecionada = $indSelecionada;
	}
	
	/**
	 * @return the $indAtiva
	 */
	public function getIndAtiva() {
		return $this->indAtiva;
	}

	/**
	 * @param int $indAtiva
	 */
	public function setIndAtiva($indAtiva) {
		$this->indAtiva = $indAtiva;
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

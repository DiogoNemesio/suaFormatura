<?php

namespace Zage\App\Grid;

/**
 * Gerenciar as colunas do grid em bootstrap
 *
 * @package Zage\App\Grid\Coluna
 *          @created 20/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 */
abstract class Coluna {
	
	/**
	 * Nome da Coluna
	 *
	 * @var string
	 */
	private $nome;
	
	/**
	 * Tamanho (em porcentagem)
	 *
	 * @var number
	 */
	private $tamanho;
	
	/**
	 * Alinhamento (L,C ou R)
	 *
	 * @var string
	 */
	private $alinhamento;
	
	/**
	 * Tipo
	 *
	 * @var string
	 */
	private $tipo;
	
	/**
	 * HTML
	 *
	 * @var string
	 */
	private $html;
	
	/**
	 * Indicador se a celula esta ativa
	 *
	 * @var boolean
	 */
	private $ativa;
	
	/**
	 * Nome do Campo associado ao array de importação
	 *
	 * @var string
	 */
	private $nomeCampo;
	
	/**
	 * Indicador de filtro individual
	 * @var boolean
	 */
	private $indFiltro;
	
	/**
	 * Tipo de Filtro
	 * @var string
	 */
	private $tipoFiltro;
	
	/**
	 * Mascara
	 * @var Mascara que será aplicada na coluna 
	 */
	private $mascara;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		/**
		 * Por padrão toda coluna está ativa *
		 */
		$this->ativar ();
	}
	
	/**
	 *
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}
	
	/**
	 *
	 * @param string $nome        	
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}
	
	/**
	 *
	 * @return the $tamanho
	 */
	public function getTamanho() {
		return $this->tamanho;
	}
	
	/**
	 *
	 * @param number $tamanho        	
	 */
	public function setTamanho($tamanho) {
		$this->tamanho = $tamanho;
	}
	
	/**
	 *
	 * @return the $alinhamento
	 */
	public function getAlinhamento() {
		return $this->alinhamento;
	}
	
	/**
	 *
	 * @param string $alinhamento        	
	 */
	public function setAlinhamento($alinhamento) {
		$this->alinhamento = $alinhamento;
	}
	
	/**
	 *
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}
	
	/**
	 *
	 * @param string $tipo        	
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 *
	 * @return the $html
	 */
	public function getHtml() {
		return $this->html;
	}
	
	/**
	 *
	 * @param string $html        	
	 */
	public function setHtml($html) {
		$this->html = $html;
	}
	
	/**
	 * Ativar / Habilitar a coluna
	 */
	public function ativar() {
		$this->ativa = true;
	}
	
	/**
	 * Desativar / Desabilitar a coluna
	 */
	public function desativar() {
		$this->ativa = false;
	}
	
	/**
	 *
	 * @return the $ativa
	 */
	public function getAtiva() {
		return $this->ativa;
	}
	
	/**
	 *
	 * @return the $nomeCampo
	 */
	public function getNomeCampo() {
		return $this->nomeCampo;
	}
	
	/**
	 *
	 * @param string $nomeCampo        	
	 */
	public function setNomeCampo($nomeCampo) {
		$this->nomeCampo = $nomeCampo;
	}

	/**
	 * @return the $indFiltro
	 */
	public function getIndFiltro() {
		return $this->indFiltro;
	}

	/**
	 * @param boolean $indFiltro
	 */
	public function setIndFiltro($indFiltro) {
		$this->indFiltro = $indFiltro;
	}

	/**
	 * @return the $tipoFiltro
	 */
	public function getTipoFiltro() {
		return $this->tipoFiltro;
	}

	/**
	 * @param string $tipoFiltro
	 */
	public function setTipoFiltro($tipoFiltro) {
		$this->tipoFiltro = $tipoFiltro;
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
	
}

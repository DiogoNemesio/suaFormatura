<?php

namespace Zage\Adm;

/**
 * TipoMascara
 * 
 * @package: TipoMascara
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class TipoMascara {

	/**
	 * Nome da Mascara
	 * @var string
	 */
	private $nome;
	
	/**
	 * Máscara
	 * @var string
	 */
	private $mascara;
	
	/**
	 * Valor Padrão
	 * @var string
	 */
	private $valorPadrao;
	
	/**
	 * indicador que a máscara é reversa
	 * @var boolean
	 */
	private $reversa;

	/**
	 * Função de validação
	 * @var string
	 */
	private $funcao;
	
	/**
	 * Indica se o valor precisa ter o mesmo tamanho da máscara
	 * @var boolean
	 */
	private $mesmoTamanho;
	
	/**
	 * Tipo de dados
	 * @var string
	 */
	private $tipo;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		global $log;
		
		$log->debug(__CLASS__.": nova Instância");
		
	}
	
	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @return the $mascara
	 */
	public function getMascara() {
		return $this->mascara;
	}

	/**
	 * @return the $valorPadrao
	 */
	public function getValorPadrao() {
		return $this->valorPadrao;
	}

	/**
	 * @return the $reversa
	 */
	public function getReversa() {
		return $this->reversa;
	}
	
	/**
	 * @return the $funcao
	 */
	public function getFuncao() {
		return $this->funcao;
	}

	/**
	 * @return the $mesmoTamanho
	 */
	public function getMesmoTamanho() {
		return $this->mesmoTamanho;
	}

	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param string $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * @param string $mascara
	 */
	public function setMascara($mascara) {
		$this->mascara = $mascara;
	}

	/**
	 * @param string $valorPadrao
	 */
	public function setValorPadrao($valorPadrao) {
		$this->valorPadrao = $valorPadrao;
	}

	/**
	 * @param string $reversa
	 */
	public function setReversa($reversa) {
		$this->reversa = $reversa;
	}

	/**
	 * @param string $funcao
	 */
	public function setFuncao($funcao) {
		$this->funcao = $funcao;
	}

	/**
	 * @param boolean $mesmoTamanho
	 */
	public function setMesmoTamanho($mesmoTamanho) {
		$this->mesmoTamanho = $mesmoTamanho;
	}
	
	/**
	 * @param string $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
}
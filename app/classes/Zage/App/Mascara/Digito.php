<?php

namespace Zage\App\Mascara;

/**
 * Gerenciar os Dígitos de uma máscara
 *
 * @package \Zage\App\Mascara\Digito
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
abstract class Digito {
	
	/**
	 * Construtor
	 */
	public function __construct() {
	}
	
	/**
	 * Dígito
	 * @var string
	 */
	protected $digito;
	
	/**
	 * Pattern
	 * @var string
	 */
	protected $pattern;
	
	/**
	 * Recursivo
	 * @var boolean
	 */
	protected $recursivo;
	
	
	/**
	 * Opcional
	 * @var boolean
	 */
	protected $opcional;
	
	/**
	 * @return the $digito
	 */
	protected function getDigito() {
		return $this->digito;
	}

	/**
	 * @param string $digito
	 */
	protected function setDigito($digito) {
		$this->digito = $digito;
	}
	
	/**
	 * @return the $pattern
	 */
	protected function getPattern() {
		return $this->pattern;
	}

	/**
	 * @param string $pattern
	 */
	protected function setPattern($pattern) {
		$this->pattern = $pattern;
	}
	
	/**
	 * @return the $recursivo
	 */
	protected function getRecursivo() {
		return $this->recursivo;
	}

	/**
	 * @return the $opcional
	 */
	protected function getOpcional() {
		return $this->opcional;
	}

	/**
	 * @param boolean $recursivo
	 */
	protected function setRecursivo($recursivo) {
		$this->recursivo = $recursivo;
	}

	/**
	 * @param boolean $opcional
	 */
	protected function setOpcional($opcional) {
		$this->opcional = $opcional;
	}

}
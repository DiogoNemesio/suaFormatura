<?php

namespace Zage\App\Aviso;

/**
 * Gerenciar os tipos de aviso
 *
 * @package \Zage\App\Aviso\Tipo
 * @created 10/04/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
abstract class Tipo {
	
	/**
	 * Tipos de aviso
	 */
	const INFO	 	= 0;
	const ALERTA 	= 1;
	const ERRO	 	= 2;
	
	/**
	 * Mensagem
	 */
	var $mensagem;
	
	/**
	 * Ícone
	 */
	var $icone;
	
	/**
	 * Tipo 
	 * @var tipo
	 */
	var $tipo;
	
	/**
	 * classe
	 * @var $classe
	 */
	var $classe;
	
	/**
	 * @return the $mensagem
	 */
	public function getMensagem() {
		return $this->mensagem;
	}

	/**
	 * @param field_type $mensagem
	 */
	public function setMensagem($mensagem) {
		$this->mensagem = $mensagem;
	}
	
	/**
	 * @return the $icone
	 */
	public function getIcone() {
		return $this->icone;
	}

	/**
	 * @param field_type $icone
	 */
	protected function setIcone($icone) {
		$this->icone = $icone;
	}
	
	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param tipo $tipo
	 */
	protected function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 * @return the $classe
	 */
	public function getClasse() {
		return $this->classe;
	}

	/**
	 * @param $classe $classe
	 */
	public function setClasse($classe) {
		$this->classe = $classe;
	}


}
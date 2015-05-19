<?php

namespace Zage\App\Aviso\Tipo;

/**
 * 
 *
 * @package Zage\App\Aviso\Tipo\Alerta
 * @created 10/04/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Alerta extends \Zage\App\Aviso\Tipo {
	
	/**
	 * Construtor
	 *
	 * @param string $mensagem
	 * @return void
	 */
	public function __construct($mensagem) {
	
		/**
		 * Define o ícone
		 */
		$this->setIcone("fa fa-warning");
		
		/**
		 * Define o tipo
		 */
		$this->setTipo(\Zage\App\Aviso\Tipo::ALERTA);
		
		/**
		 * Define a classe
		 */
		$this->setClasse("alert-warning");
		
		/**
		 * Define a mensagem
		 */
		$this->setMensagem($mensagem);
	}
	
}
<?php

namespace Zage\App\Aviso\Tipo;

/**
 * 
 *
 * @package Zage\App\Aviso\Tipo\Info
 * @created 10/04/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Info extends \Zage\App\Aviso\Tipo {
	
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
		$this->setIcone("fa fa-info");
		
		/**
		 * Define o tipo
		 */
		$this->setTipo(\Zage\App\Aviso\Tipo::INFO);
		
		/**
		 * Define a classe
		 */
		$this->setClasse("alert-success");
				
		/**
		 * Define a mensagem
		 */
		$this->setMensagem($mensagem);
	}
	
}
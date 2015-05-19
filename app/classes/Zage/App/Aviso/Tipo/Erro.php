<?php

namespace Zage\App\Aviso\Tipo;

/**
 * 
 *
 * @package Zage\App\Aviso\Tipo\Erro
 * @created 10/04/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Erro extends \Zage\App\Aviso\Tipo {
	
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
		$this->setIcone("fa fa-times-circle");
		
		/**
		 * Define o tipo
		 */
		$this->setTipo(\Zage\App\Aviso\Tipo::ERRO);
		
		/**
		 * Define a classe
		 */
		$this->setClasse("alert-danger");
		
		/**
		 * Define a mensagem
		 */
		$this->setMensagem($mensagem);
	}
	
}
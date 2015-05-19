<?php

namespace Zage\App\Mascara\Digito;

/**
 * Gerenciar os Digito de uma Máscara
 *
 * @package \Zage\App\Mascara\Digito\A
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class A extends \Zage\App\Mascara\Digito {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Define as configurações do dígito
		 */
		$this->setDigito("A");
		$this->setPattern("[A-Z]");
		$this->setOpcional(false);
		$this->recursivo(false);
		
	}

}

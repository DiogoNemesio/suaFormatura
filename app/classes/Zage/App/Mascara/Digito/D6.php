<?php

namespace Zage\App\Mascara\Digito;

/**
 * Gerenciar os Digito de uma Máscara
 *
 * @package \Zage\App\Mascara\Digito\D6
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class D6 extends \Zage\App\Mascara\Digito {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Define as configurações do dígito
		 */
		$this->setDigito("6");
		$this->setPattern("[0-6]");
		$this->setOpcional(false);
		$this->recursivo(false);
		
	}

}

<?php

namespace Zage\App\Mascara\Digito;

/**
 * Gerenciar os Digito de uma Máscara
 *
 * @package \Zage\App\Mascara\Digito\D0
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class D0 extends \Zage\App\Mascara\Digito {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Define as configurações do dígito
		 */
		$this->setDigito("0");
		$this->setPattern("[0-9]");
		$this->setOpcional(false);
		$this->recursivo(false);
		
	}

}

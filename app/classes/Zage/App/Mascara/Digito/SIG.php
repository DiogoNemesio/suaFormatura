<?php

namespace Zage\App\Mascara\Digito;

/**
 * Gerenciar os Digito de uma Máscara
 *
 * @package \Zage\App\Mascara\Digito\SIG
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class SIG extends \Zage\App\Mascara\Digito {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Define as configurações do dígito
		 */
		$this->setDigito("~");
		$this->setPattern("[+-]");
		$this->setOpcional(true);
		$this->recursivo(false);
	}

}

<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo NUMERO
 *
 * @package \Zage\App\Mascara\Tipo\Numero
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Numero extends \Zage\App\Mascara\Tipo {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Carrega as configurações
		 */
		$this->setTipo($this::TP_NUMERO);
		$this->_loadConfigFromDb();
		
	}

}

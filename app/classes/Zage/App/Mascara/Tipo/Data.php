<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo DATA
 *
 * @package \Zage\App\Mascara\Tipo\Data
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Data extends \Zage\App\Mascara\Tipo {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Carrega as configurações
		 */
		$this->setTipo($this::TP_DATA);
		$this->_loadConfigFromDb();
		
	}

}

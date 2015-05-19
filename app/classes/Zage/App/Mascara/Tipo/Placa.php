<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo Placa
 *
 * @package \Zage\App\Mascara\Tipo\Placa
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Placa extends \Zage\App\Mascara\Tipo {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Carrega as configurações
		 */
		$this->setTipo($this::TP_PLACA);
		$this->_loadConfigFromDb();
		
	}

}

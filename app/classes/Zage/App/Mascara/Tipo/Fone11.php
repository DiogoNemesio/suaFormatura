<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo FONE11
 *
 * @package \Zage\App\Mascara\Tipo\Fone11
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *
 */
class Fone11 extends \Zage\App\Mascara\Tipo {

	/**
	 * Construtor
	 */
	public function __construct() {

		parent::__construct();

		/**
		 * Carrega as configurações
		*/
		$this->setTipo($this::TP_FONE11);
		$this->_loadConfigFromDb();

	}

}

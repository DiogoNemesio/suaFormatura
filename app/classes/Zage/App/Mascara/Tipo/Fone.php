<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo FONE
 *
 * @package \Zage\App\Mascara\Tipo\Fone
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *
 */
class Fone extends \Zage\App\Mascara\Tipo {

	/**
	 * Construtor
	 */
	public function __construct() {

		parent::__construct();

		/**
		 * Carrega as configurações
		*/
		$this->setTipo($this::TP_FONE);
		$this->_loadConfigFromDb();

	}

}

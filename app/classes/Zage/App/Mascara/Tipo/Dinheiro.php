<?php

namespace Zage\App\Mascara\Tipo;

/**
 * Gerenciar as Mascaras do Tipo DINHEIRO
 *
 * @package \Zage\App\Mascara\Tipo\Dinheiro
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Dinheiro extends \Zage\App\Mascara\Tipo {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		parent::__construct();

		/**
		 * Carrega as configurações
		 */
		$this->setTipo($this::TP_DINHEIRO);
		$this->_loadConfigFromDb();
		
	}

	
	/**
	 * Retirar a mascara a uma determinada string
	 * @param string $string
	 */
	public function retiraMascara($string) {
		global $log;
	
		$result		= str_replace(',','',$string);
		$result		= str_replace('.','',$result);
		
		return $result;
	}
	
}

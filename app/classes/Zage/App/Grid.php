<?php

namespace Zage\App;

/**
 * Gerenciar os grids em bootstrap
 *
 * @package \Zage\App\Grid
 * @created 20/03/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.2
 *         
 */
class Grid  {
	
	/**
	 * Objeto que irá guardar a instância
	 */
	private static $instance;
	
	/**
	 * Construtor 
	 *
	 * @return object
	 */
	public static function criar($tipo,$nome) {
		
		/**
		 * Verifica se o tipo do Grid é válido
		 */
		switch ($tipo) {
			case \Zage\App\Grid\Tipo::TP_BOOTSTRAP:
				self::$instance	= new \Zage\App\Grid\Tipo\Bootstrap($nome);
				break;
			case \Zage\App\Grid\Tipo::TP_DHTMLX:
				self::$instance	= new \Zage\App\Grid\Tipo\DHTMLX($nome);
				break;
			default:
				die ('Tipo de grid não implementado !!!');
				break;
		}
		
		return self::$instance;
	}
	
	/**
	 * Construtor privado, usar \Zage\App\Grid::getInstance();
	 */
	private function __construct($tipo,$nome) {
		
	}
	

}

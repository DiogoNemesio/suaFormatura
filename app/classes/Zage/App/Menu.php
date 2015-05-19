<?php

namespace Zage\App;

/**
 * Gerenciar os menus
 *
 * @package \Zage\App\Menu
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */ 
class Menu {
	
	/**
	 * Objeto que irá guardar a instância
	 */
	private static $instance;
	
	/**
	 * Construtor
	 *
	 * @return object
	 */
	public static function criar($tipo) {
		/**
		 * Define o tipo do Menu
		 */
		switch ($tipo) {
			case \Zage\App\Menu\Tipo::TIPO1:
				self::$instance	= new \Zage\App\Menu\Tipo1($tipo);
				break;
			case \Zage\App\Menu\Tipo::TIPO2:
				self::$instance	= new \Zage\App\Menu\Tipo2($tipo);
				break;
			default:
				\Zage\App\Erro::halt('Tipo de Menu desconhecido !!!');
		}
		return self::$instance;
	}
	
	/**
	 * Construtor privado, usar \Zage\App\Menu::getInstance();
	 */
	private function __construct($tipo,$nome) {
	
	}

}

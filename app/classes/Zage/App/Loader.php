<?php

namespace Zage\App;

/**
 * Carregador de classes
 *
 * @package \Zage\App\Loader
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Loader {
	
	/**
	 * Carregar automaticamente a classe
	 * 
	 * @param string $class        	
	 * @return void
	 */
	public static function autoload($class) {
		$prodDirs = array(CLASS_PATH);
		
		if (stripos ( $class, '\\' ) === false) {
			if (defined('MODULE_CLASS_PATH')) {
				$prodDirs[] = MODULE_CLASS_PATH;
			}
			
			for ($i=0; $i<sizeof($prodDirs); $i++) {
				$file   = $prodDirs[$i] . '/'.$class.'.php';
				if (file_exists($file)) {
					include_once ($file);
					return $class;
				}
			}
			return false;
		} else {
			include_once (CLASS_PATH . DIRECTORY_SEPARATOR . str_replace ( '\\', DIRECTORY_SEPARATOR, $class ) . '.php');
		}
		return false;
	}
}
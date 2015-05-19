<?php

namespace Zage\App;

/**
 * Avisos
 *
 * @package \Zage\App\Aviso
 * @created 10/04/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Aviso {
	
	/**
	 * Objeto que irá guardar a instância
	 */
	private static $instance;
	
	/**
	 * Construtor
	 *
	 * @return object
	 */
	public static function criar($tipo,$mensagem) {
	
		/**
		 * Verifica se o tipo do Grid é válido
		 */
		switch ($tipo) {
			case \Zage\App\Aviso\Tipo::ERRO:
				self::$instance	= new \Zage\App\Aviso\Tipo\Erro($mensagem);
				break;
			case \Zage\App\Aviso\Tipo::ALERTA:
				self::$instance	= new \Zage\App\Aviso\Tipo\Alerta($mensagem);
				break;
			case \Zage\App\Aviso\Tipo::INFO:
				self::$instance	= new \Zage\App\Aviso\Tipo\Info($mensagem);
				break;
			default:
				die ('Tipo de aviso não implementado !!!');
				break;
		}
	
		return self::$instance;
	}
	
	/**
	 * Construtor privado, usar \Zage\App\Aviso::getInstance();
	 */
	private function __construct($tipo,$mensagem) {
	
	}
	
	/**
	 * @return the $instance
	 */
	public static function getInstance() {
		return self::$instance;
	}


}
<?php

namespace Zage\App;

use Zage\App\Mascara\Tipo\Cnpj;
/**
 * Gerenciar Mascaras
 *
 * @package \Zage\App\Mascara
 * @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0.3
 *         
 */
class Mascara  {
	
	/**
	 * Objeto que irá guardar a instância
	 */
	private static $instance;
	
	/**
	 * Construtor 
	 *
	 * @return object
	 */
	public static function tipo($tipo) {
		
		/**
		 * Verifica se o tipo da Máscara é válido
		 */
		switch ($tipo) {
			case \Zage\App\Mascara\Tipo::TP_CARTAO:
				self::$instance	= new \Zage\App\Mascara\Tipo\Cartao();
				break;
			case \Zage\App\Mascara\Tipo::TP_CEP:
				self::$instance	= new \Zage\App\Mascara\Tipo\Cep();
				break;
			case \Zage\App\Mascara\Tipo::TP_CNPJ:
				self::$instance	= new \Zage\App\Mascara\Tipo\Cnpj();
				break;
			case \Zage\App\Mascara\Tipo::TP_CPF:
				self::$instance	= new \Zage\App\Mascara\Tipo\Cpf();
				break;
			case \Zage\App\Mascara\Tipo::TP_DATA:
				self::$instance	= new \Zage\App\Mascara\Tipo\Data();
				break;
			case \Zage\App\Mascara\Tipo::TP_DINHEIRO:
				self::$instance	= new \Zage\App\Mascara\Tipo\Dinheiro();
				break;
			case \Zage\App\Mascara\Tipo::TP_FONE:
				self::$instance	= new \Zage\App\Mascara\Tipo\Fone();
				break;
			case \Zage\App\Mascara\Tipo::TP_NUMERO:
				self::$instance	= new \Zage\App\Mascara\Tipo\Numero();
				break;
			case \Zage\App\Mascara\Tipo::TP_PORCENTAGEM:
				self::$instance	= new \Zage\App\Mascara\Tipo\Porcentagem();
				break;
			case \Zage\App\Mascara\Tipo::TP_TEMPO:
				self::$instance	= new \Zage\App\Mascara\Tipo\Tempo();
				break;
			case \Zage\App\Mascara\Tipo::TP_PLACA:
				self::$instance	= new \Zage\App\Mascara\Tipo\Placa();
				break;
			default:
				die ('Tipo de Mascara não implementado !!!');
				break;
		}
		
		return self::$instance;
	}
	
	/**
	 * Construtor privado, usar \Zage\App\Mascara::getInstance();
	 */
	private function __construct($tipo) {
		
	}
	

}

<?php

namespace Zage\Seg\Auth;

/**
 * Gerenciar a autenticação
 *
 * @package \Zage\validaUsuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */
class validaUsuario extends \Zend\Validator\AbstractValidator {
	/**
	 * Constante de erro
	 *
	 */
	const MSG_USUARIO	= 'usuario';
	
	/**
	 * Mensagens de erro
	 *
	 * @var array
	 */
	protected $messageTemplates = array(
		self::MSG_USUARIO	=> "Usuário inválido !!!"
	);


	/**
	 * Construtor
	 *
	 */
	public function __construct() {
	}
	
	/**
	 * Verificar se a informação é válida
	 *
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
	
		$this->setValue($value);
	
		/** Verificar se a string é alpha numérica e tem entre 2 e 25 caracteres **/
		$validatorChain = new \Zend\Validator\ValidatorChain();
		$validatorChain->attach(new \Zend\Validator\StringLength(array('min' => 2,'max' => 100)));
		$validatorChain->attach(new \Zend\Validator\Regex(array('pattern' => '/^[a-zA-Z0-9.@\s]+$/')));
	
		if ($validatorChain->isValid($value)) {
			return true;
		} else {
			$this->error(self::MSG_USUARIO);
			return false;
		}
	
	}
	
}
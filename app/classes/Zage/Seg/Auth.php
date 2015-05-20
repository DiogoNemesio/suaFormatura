<?php

namespace Zage\Seg;

/**
 * Gerenciar a autenticação
 *
 * @package \Zage\Seg\Auth
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 12/07/2013
 */
class Auth implements \Zend\Authentication\Adapter\AdapterInterface {

	/**
	 * Usuário
	 */
	private $username;
	
	/**
	 * Senha
	 */
	private $password;
	
	/**
	 * Código da Organização
	 */
	private $codOrganizacao;
	
	
	/**
	 * Sets username and password for authentication
	 *
	 * @return void
	 */
	public function __construct($username,$password,$codOrganizacao) {
	
		/** Definindo Variáveis globais **/
		global $log;
	
		$log->debug(__CLASS__.": nova instância");
		$log->debug(__CLASS__.': Definindo usuario: '.$username);
	
		$this->username 		= $username;
		$this->password			= $password;
		$this->codOrganizacao	= $codOrganizacao;
	
	}
	
	/**
	 * Faz a autenticação
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		global $em,$log,$system;
		

		/** Verifica se o usuário existe e resgata os dados dele **/
		$user 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array ('usuario' => $this->username));
		
		if ($user) {
			/** Verifica se a senha está correta **/
			if ($user->getSenha() !== $this->password) {
				$result		= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
				$messages[] = "Informações incorretas !!!";
				return new \Zend\Authentication\Result($result,$this->username,$messages);
			}
			
			/** Verifica se o usuário está ativo **/
			if ($user->getCodStatus()->getIndPermiteAcesso() == 0) {
				$result		= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
				$messages[] = "Usuário bloqueado/desativado !!!";
				return new \Zend\Authentication\Result($result,$this->username,$messages);
			}
			
			$result	= \Zend\Authentication\Result::SUCCESS;
			$messages[] = null;
			return new \Zend\Authentication\Result($result,$this->username,$messages);
		}else{
			$result		= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
			$messages[] = "Informações incorretas !!!";
			return new \Zend\Authentication\Result($result,$this->username,$messages);
		}
	}
}

<?php

namespace Zage\App;

/**
 * Gerenciamento de email
 *
 * @package \Zage\App\Mail
 * @created 01/06/2015
 * @author Daniel Henrique Cassela
 * @version GIT: $Id$ 1.0.1
 *
 */
class Mail {

	/**
	 * Nome do Rementente
	 *
	 * @var string
	 */
	private static $nomeRemetente;
	
	/**
	 * Email do Rementente
	 *
	 * @var string
	 */
	private static $emailRemetente;
	
	/**
	 * Usuário do servidor SMTP
	 *
	 * @var string
	 */
	private static $usuarioSmtp;
	
	/**
	 * Senha do Usuário do servidor SMTP
	 *
	 * @var string
	 */
	private static $senhaSmtp;
	
	/**
	 * Servidor SMTP
	 *
	 * @var string
	 */
	private static $servidorSmtp;
	
	/**
	 * Porta do servidor SMTP
	 *
	 * @var string
	 */
	private static $portaSmtp;
	
	/**
	 * Tipo de autenticação do servidor SMTP
	 *
	 * @var string
	 */
	private static $tipoAutSmtp;
	
	/**
	 * Tipo de criptografia do servidor SMTP
	 *
	 * @var string
	 */
	private static $tipoCriptSmtp;
	
	/**
	 * Caso o servidor SMTP seja o google, usar o domínio principal
	 *
	 * @var string
	 */
	private static $dominioSmtp;
	
	/**
	 * Objeto que irá guardar a instância de transporte
	 */
	private static $transport;
	
	/**
	 * Objeto que irá guardar a instância de Mail
	 */
	private static $mail;
	
	/**
	 * Construtor privado, usar \Zage\App\Mail::getTransport() ou \Zage\App\Mail::getMail();
	 */
	private function __construct() {
		
	}	
	
	/**
	 * Construtor
	 *
	 * @return object
	 */
	public static function getTransport() {
		global $system,$log;
		
		#################################################################################
		## Resgatar os parâmetros de e-mail
		#################################################################################
		self::$servidorSmtp		= $system->config["mail"]["servidorSmtp"];
		self::$portaSmtp		= $system->config["mail"]["portaSmtp"];
		self::$tipoAutSmtp		= $system->config["mail"]["tipoAutSmtp"];
		self::$tipoCriptSmtp	= $system->config["mail"]["tipoCriptSmtp"];
		self::$usuarioSmtp		= $system->config["mail"]["usuarioSmtp"];
		self::$dominioSmtp		= $system->config["mail"]["dominioSmtp"];
		
		
		#################################################################################
		## Decriptar a senha
		#################################################################################
		$crypt				= new Crypt();
		self::$senhaSmtp	= $crypt->decrypt($system->config["mail"]["senhaSmtp"],self::$usuarioSmtp);
		//$log->debug("Senha do servidor SMTP: ".self::$senhaSmtp);
		
		#################################################################################
		## Cria o objeto de configuração
		#################################################################################
		$array	= array(
			'name' 				=> self::$dominioSmtp,
			'host' 				=> self::$servidorSmtp,
			'connection_class'	=> self::$tipoAutSmtp,
			'port' 				=> self::$portaSmtp,
			'connection_config' => array(
				'ssl' 			=> self::$tipoCriptSmtp, /* Page would hang without this line being added */
				'username' 		=> self::$usuarioSmtp,
				'password' 		=> self::$senhaSmtp,
			),
		);
		//$log->debug("Configuração SMTP:".serialize($array));
		$options 	= new \Zend\Mail\Transport\SmtpOptions($array);
		
		
		#################################################################################
		## Cria o objeto de transport
		#################################################################################
		try {
			self::$transport 		= new \Zend\Mail\Transport\Smtp();
			self::$transport->setOptions($options);
		} catch (Exception $e) {
			$log->err("Erro ao conectar ao servidor SMTP:". $e->getTraceAsString());
			return null;
		}
		
		return self::$transport;
	}
	

	/**
	 * Construtor
	 *
	 * @return object
	 */
	public static function getMail() {
		global $system,$log;
	
		#################################################################################
		## Resgatar os parâmetros de e-mail
		#################################################################################
		self::$emailRemetente		= $system->config["mail"]["remetente"];
		self::$nomeRemetente		= $system->config["mail"]["nomeRemetente"];
	
		#################################################################################
		## Cria o objeto de Mail
		#################################################################################
		self::$mail 		= new \Zend\Mail\Message();
		self::$mail->setFrom(self::$emailRemetente, self::$nomeRemetente);
		self::$mail->setEncoding($system->config["charset"]);
	
		return self::$mail;
	}
	
	
}

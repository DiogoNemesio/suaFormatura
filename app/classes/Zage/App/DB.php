<?php

namespace Zage\App;

use \Zend\Crypt\PublicKey\Rsa\PublicKey;
use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;

/**
 * Gerenciar conexões com o banco de dados
 *
 * @package \Zage\App\DB
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 12/07/2013
 */
class DB {
	
	const DB_SENHA_TEXTO = 0;
	const DB_SENHA_CRYPT = 1;
	
	/**
	 * Objeto que irá guardar a instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Objeto que irá guardar a instância do \Zend\DB
	 */
	public $con;
	
	/**
	 * Driver que será utilizado
	 * @var string
	 */
	private $driver;
	
	/**
	 * Construtor privado, usar DB::getInstance();
	 *
	 */
	public function __construct() {
		global $system,$log;
	
		$log->debug(__CLASS__.": nova instância");
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	/**
	 * Refazer a função para não permitir a clonagem deste objeto.
	 *
	 */
	public function __clone() {
		global $system,$log;
		$log->debug(__CLASS__.": tentativa de clonagem");
		die(__CLASS__.': não pode ser clonado !!!');
	}
	
	/**
	 * Fazer conexão ao banco
	 *
	 * @param string $ip
	 * @param string $usuario
	 * @param string $senha
	 * @param string $banco
	 * @param string $indSenhaCript Indicador de senha criptografada (1 Criptografada, 0 não criptografada)
	 */
	public function conectar ($ip = '',$usuario = '',$senha = '',$banco = '',$indSenhaCript = '',$driver = '') {
		global $system,$log,$em;
		 
		if (!$driver) {
			$driver	= $system->config["database"]["driver"];
		}
		 
		/** Checando se os parâmetros de banco de dados foram configurados **/
		if (!$driver) {
			Erro::halt('parâmetros de banco de dados não informado: (database.driver)');
		}else{
			$this->setDriver($driver);
		}
		 
		/**
		 * Os parâmetros que forem passados em branco serão resgatados do arquivo de configuração
		 */
		if ($ip				=== null) 		$ip 			= $system->config["database"]["ip"];
		if ($usuario 		=== null)		$usuario 		= $system->config["database"]["usuario"];
		if ($senha			=== null)		$senha 			= $system->config["database"]["senha"];
		if ($banco			=== null)		$banco 			= $system->config["database"]["banco"];
		if ($indSenhaCript	=== null)		$indSenhaCript 	= $system->config["database"]["indSenhaCript"];
		 
		/**
		 * Por padrão a senha passada deve ser criptografada
		 */
		if (!$indSenhaCript) {
			$indSenhaCript = self::DB_SENHA_TEXTO;
			$log->debug("Senha não escondida");
		}elseif ($indSenhaCript == self::DB_SENHA_CRYPT) {
			$indSenhaCript = self::DB_SENHA_CRYPT;
		}
	
		/** 
		 * Checando se os parâmetros obrigatórios estão corretos
		 **/
		if ((!$usuario) || (!$senha)) {
			Erro::halt('parâmetros de banco de dados não informado');
		}
	
		/** 
		 * Recuperando a senha do banco de dados caso esteja criptografada
		 **/
		if ($indSenhaCript == self::DB_SENHA_CRYPT) {
			$crypt	= new Crypt();
			$pass	= $crypt->decrypt($senha,$usuario);
		}else {
			$pass	= $senha;
		}
		
		/** 
		 * Monta o array de parâmetro para conectar ao banco
		 **/
		if ($ip !== null) $dbParams["host"] 	= $ip;
		$dbParams["driver"]			= $driver;
		$dbParams["username"]		= $usuario;
		$dbParams["user"]			= $usuario;
		$dbParams["password"]		= $pass;
		$dbParams["database"]		= $banco;
		$dbParams["dbname"]			= $banco;
		$dbParams["charset"]		= $system->config["database"]["charset"];
		$dbParams["options"] 		= array('buffer_results' => true);
	
		
		/** 
		 * Salva o parâmetro de display erro do PHP
		 **/
		$dispErroSave	= ini_get('display_errors');
	
		/** 
		 * Altera o parâmetro para não mostrar os erros 
		 **/
		ini_set('display_errors',true);
	
		try {
			
			/**
			 * Cria a adaptador da conexão
			 */
			$config = new \Doctrine\DBAL\Configuration();
			$this->con = \Doctrine\DBAL\DriverManager::getConnection($dbParams, $config);
			//$this->con->setFetchMode(\PDO::FETCH_OBJ);
				
			/**
			 * Testa se a conexao foi bem sucedida
			 */
			$this->testaConexao();
			
			
			/**
			 * Configura o modo de recuperação de dados
			 **/
			//$this->con->setFetchMode(Zend_Db::FETCH_OBJ);
	
			//$this->Executa("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
			
			//print_r($this->con);
	
		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
	
		/** retornar o parâmetro de display erro **/
		ini_set('display_errors',$dispErroSave);
		

		try {
			$isDevMode 	= true;
			$config 	= Setup::createAnnotationMetadataConfiguration(array(ENTITY_PATH), $isDevMode);
			$em 		= EntityManager::create($this->con, $config);
				
			$driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver($em->getConnection()->getSchemaManager());
			$driver->setNamespace('Entidades\\');
			
			$em->getConfiguration()->setMetadataDriverImpl($driver);

		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
		
		
		
	}
	
	/**
	 * Testar a conexão
	 */
	private function testaConexao () {
		global $system;
		switch ($this->getDriver()) {
			case "mysqli":
			case "pdo_mysql":
				$sql	= "SELECT USER() AS USUARIO";
				break;
			case "oci8":
				$sql	= "SELECT USER USUARIO FROM DUAL";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
		
		try {
			$res	= @$this->con->query($sql);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getMessage();
			Erro::halt($erro);
		}
	}
	

	/**
	 * Definir variáveis globais no banco (session variables)
	 */
	public function setLoggedUser ($codUsuario) {
		switch ($this->getDriver()) {
			case "mysqli":
			case "pdo_mysql":
				$sql	= "SET @ZG_USER = ".$codUsuario;
				break;
			case "oci8":
				$sql	= "exec dbms_application_info.set_client_info('".$codUsuario."');";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
	
		try {
			$res	= @$this->con->query($sql);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getMessage();
			Erro::halt($erro);
		}
	}
	

	/**
	 * Definir variáveis globais no banco (session variables)
	 */
	public function setOrganizacao($codOrganizacao) {
		switch ($this->getDriver()) {
			case "mysqli":
			case "pdo_mysql":
				$sql	= "SET @ZG_ORG = ".$codOrganizacao;
				break;
			case "oci8":
				$sql	= "exec dbms_application_info.set_module('".$codOrganizacao."',null);";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
	
		try {
			$res	= @$this->con->query($sql);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getMessage();
			Erro::halt($erro);
		}
	}
	
	
	/**
	 * Resgatar o usuário logado no sistema através das variáveis de sessão
	 */
	public function getLoggedUser () {
		switch ($this->getDriver()) {
			case "mysqli":
			case "pdo_mysql":
				$sql	= "SELECT @ZG_USER as USUARIO";
				break;
			case "oci8":
				$sql	= "SELECT sys_context('USERENV', 'CLIENT_INFO') USUARIO FROM DUAL";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
	
		$info	= $this->extraiPrimeiro($sql);
		
		if (!isset($info->USUARIO)) {
			die('Não foi possível resgatar o usuário logado no sistema !!!');
		}else{
			return $info->USUARIO;
		}
	}
	
	/**
	 * Resgatar a organizacao no sistema através das variáveis de sessão
	 */
	public function getOrganizacao () {
		switch ($this->getDriver()) {
			case "mysqli":
			case "pdo_mysql":
				$sql	= "SELECT @ZG_ORG as ORG";
				break;
			case "oci8":
				$sql	= "SELECT sys_context('USERENV', 'MODULE') ORG FROM DUAL";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
	
		$info	= $this->extraiPrimeiro($sql);
		
		if (!isset($info->ORG)) {
			die('Não foi possível resgatar o usuário logado no sistema !!!');
		}else{
			return $info->ORG;
		}
	}
	
	/**
	 * @return the $driver
	 */
	protected function getDriver() {
		return $this->driver;
	}

	/**
	 * @param string $driver
	 */
	protected function setDriver($driver) {
		$this->driver = $driver;
	}

	/**
	 * Extrair todos os dados de uma consulta SQL
	 * @param string $sql
	 * @param array $parametros
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function extraiTodos($sql, $parametros = null) {
		
		try {
			$statement 	= $this->con->prepare($sql);
			$statement->execute($parametros);
			$result		= $statement->fetchAll();
			
			$res	= array();
			for ($i = 0; $i < sizeof($result); $i++) {
				$res[$i]	= (object) $result[$i];
			}
			return ($res);
			
		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
	}
	
	/**
	 * Extrair o primeiro registro de uma consulta SQL
	 * @param string $sql
	 * @param array $parametros
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function extraiPrimeiro($sql, $parametros = null) {
		try {
			$statement 	= $this->con->prepare($sql);
			$statement->execute($parametros);
			$result		= $statement->fetch();
			return ((object) $result);
						
		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
	}
	
	/**
	 * Executar uma instrução SQL
	 * @param string $sql
	 * @param array $parametros
	 */
	public function Executa ($sql, $parametros = null) {
		try {
			$statement 	= $this->con->prepare($sql);
			$statement->execute($parametros);
		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
	
	}
	
}

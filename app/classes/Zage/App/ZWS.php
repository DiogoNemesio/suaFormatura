<?php

namespace Zage\App;

/**
 * Zage Web System: Sistema para desenvolvimento de softwares web
 *
 * @package \Zage\App\ZWS
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */
abstract class ZWS {
	
	const EXT_HTML	= 'html';
	const EXT_XML	= 'xml';
	const EXT_PHP	= 'php';
	const EXT_DP	= 'dp.php';
	
	const CAMINHO_ABSOLUTO	= 1;
	const CAMINHO_RELATIVO	= 2;
	
	/**
	 * Constantes de Fim de Linha e Tabulação
	 * @var unknown
	 */
	const NL	= "\r\n";
	const TAB	= "	";
	
	
	/**
	 * Array com as configurações do sistema
	 *
	 * @var array
	 */
	public $config;
	
	/**
	 * Instância do Zend\Mail
	 *
	 * @var object
	 */
	public $mail;
	
	/**
	 * Indica se o sistema já foi iniciado
	 * @var boolean
	 */
	private $iniciado;
	
	/**
	 * Código do usuário logado no sistema
	 *
	 * @var object
	 */
	private $codUsuario;
	
	/**
	 * Código do módulo atual
	 *
	 * @var object
	 */
	private $codModulo;
	
	/**
	 * Indica se o usuário ja está autenticado
	 *
	 * @var boolean
	 */
	private $autenticado;
	
	
	/**
	 * Array de avisos
	 *
	 * @var array
	 */
	public $avisos;
	
	/**
	 * Data do último acesso do usuário
	 * @var unknown
	 */
	private $dataUltAcesso;
	
	/**
	 * Array de mascaras
	 * @var unknown
	 */
	public $mascaras;
	
	
	
	/**
	 * Construtor: Inicializa os objetos
	 *
	 * @return void
	 */
	protected function __construct() {
		global $db,$log,$em;
		
		/**
		 * Instânciando o objeto de configuração
		 */
		$config 		= new \Zage\App\Config ( CONFIG_PATH . "/config.xml" );
		$this->config 	= $config->load ();

		
		/** 
		 * Instânciando o objeto de e-mail
		 **/
		$this->mail			= new \Zend\Mail\Message();
		$this->mail->setEncoding($this->config["charset"]);
		
		/** 
		 * Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail)
		 **/
		$this->mail->addFrom($this->config["mail"]["remetente"],$this->config["mail"]["nomeRemetente"]);
		$this->mail->addTo($this->config["mail"]["admin"],$this->config["mail"]["nomeAdmin"]);
		
		/**
		 * Iniciar recursos 
		 */
		$this->iniciaRecursos();
		
		/**
		 * Defini o indicador de sistema iniciado 
		 */
		$this->inicia();
		
		/**
		 * Inicia o array de avisos
		 */
		$this->avisos 	= array();
		
		/**
		 * Inicia o array de mascaras
		 */
		$this->mascaras	= array();

		/**
		 * Carrega as máscaras
		 */
		/*$mascaras	= $em->getRepository('Entidades\ZgappMascara')->findAll();
		for ($i = 0; $i < sizeof($mascaras); $i++) {
			$n		= sizeof($this->mascaras);
			$this->mascaras[$n]	= new \Zage\App\Mascara(); 
			$this->mascaras[$n]->setCodigo		= $mascaras[$i]->getCodigo();
			$this->mascaras[$n]->setNome		= $mascaras[$i]->getNome();
			$this->mascaras[$n]->setMascara		= $mascaras[$i]->getMascara();
			$this->mascaras[$n]->setIndReversa	= $mascaras[$i]->getIndReversa();
			$this->mascaras[$n]->setFuncao		= $mascaras[$i]->getFuncao();
			$this->mascaras[$n]->setTipo		= $mascaras[$i]->getCodTipo()->getDatatype();
		}
		*/
		
	}
	
	/**
	 * Instanciar os objetos que não podem ser serializados (resources)
	 */
	public function iniciaRecursos() {
		global $log,$db,$em;
		
		/**
		 * Define o Timezone padrão
		 */
		date_default_timezone_set($this->config["data"]["timezone"]);
		setlocale(LC_ALL, $this->config["data"]["locale"]);
		
		/**
		 * Instânciando o objeto de log
		 */
		$log		= Log::getInstance();
		
		/** 
		 * Fazendo a conexão ao banco de dados 
		 **/
		$db		= DB::getInstance();
		$db->conectar(null,null,null,null,$this->config["database"]["indSenhaCript"]);
		
	}
	
	/**
	 * Define o indicador de sistema inicializado
	 */
	protected function inicia() {
		$this->iniciado		= true;
	}
	
	
	/**
	 * Retorna o indicador de sistema inicializado
	 * @return boolean
	 */
	public function estaIniciado() {
		return ($this->iniciado);
	} 
	
	
	/**
	 * Indicar que o usuário está autenticado
	 */
	public function setAutenticado() {
		$this->autenticado = 1;
	}
	
	/**
	 * Desautenticar
	 *
	 */
	public function desautentica() {
		$this->autenticado = 0;
	}
	
	/**
	 * Verifica se o usuario ja está autenticado
	 *
	 * @return boolean
	 */
	public function estaAutenticado() {
		return $this->autenticado;
	}
	
	/**
	 * @return the $codUsuario
	 */
	public function getCodUsuario() {
		return $this->codUsuario;
	}

	/**
	 * @param object $codUsuario
	 */
	public function setCodUsuario($codUsuario) {
		$this->codUsuario = $codUsuario;
	}

	/**
	 * @return the $codModulo
	 */
	public function getCodModulo() {
		return $this->codModulo;
	}

	/**
	 * @param object $codModulo
	 */
	public function setCodModulo($codModulo) {
		$this->codModulo = $codModulo;
	}
	
	
	/**
	 * Criar um aviso
	 * @param unknown $tipo
	 * @param unknown $mensagem
	 */
	public function criaAviso($tipo, $mensagem) {
		$n = uniqid();
		$this->avisos[$n]	= \Zage\App\Aviso::criar($tipo,$mensagem);
	}
	
	/**
	 * Apaga o primeiro aviso
	 */
	public function excluiAviso($id) {
		if (array_key_exists($id,$this->avisos))
		unset($this->avisos[$id]);
	}
	
	/**
	 * @return the $dataUltAcesso
	 */
	public function getDataUltAcesso() {
		return $this->dataUltAcesso;
	}

	/**
	 * @param unknown $dataUltAcesso
	 */
	public function setDataUltAcesso($dataUltAcesso) {
		$this->dataUltAcesso = $dataUltAcesso;
	}

}

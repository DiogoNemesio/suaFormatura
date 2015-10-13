<?php

namespace Zage\Wap;

/**
 * Chip
 * 
 * @package: Chip
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Chip extends \Entidades\ZgwapChip {

	/**
	 * Código
	 * @var unknown
	 */
	private $_codigo;
	
	/**
	 * Conexão com o whatsapp
	 * @var socket
	 */
	public $w;
	
	/**
	 * Array de contatos
	 * @var array
	 */
	public $_contacts;
	
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		
	}
	
	/**
	 * Salvar os dados do chip
	 * @return number $codigo
	 */
	public function salvar() {
		global $em,$tr,$log;
		
		#################################################################################
		## Fazer validação dos campos
		#################################################################################
		if (!$this->getCodOrganizacao())				throw new \Exception($tr->trans("Campo Organização é obrigatório !!"));
		if (!$this->getIdentificacao())					throw new \Exception($tr->trans("Campo Identificação é obrigatório!"));
		if (strlen($this->getIdentificacao()) > 40)		throw new \Exception($tr->trans("A identificação não deve conter mais de 40 caracteres!"));
		if (!$this->getNumero())						throw new \Exception($tr->trans("Campo Número é obrigatório !!"));
		if (!$this->getCodPais())						throw new \Exception($tr->trans("Campo País é obrigatório !!"));
		
		#################################################################################
		## Separar o ddd do número
		#################################################################################
		$ddd		= substr($this->getNumero(),0,2);
		$celular	= substr($this->getNumero(),2);
		
		#################################################################################
		## Salvar no banco
		#################################################################################
		try {
			#################################################################################
			## Verifica se o código foi passado para atualizar, se já existir o chip
			#################################################################################
			if ($this->_getCodigo()) {
				$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
				if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));
			}else{
				$oChip	= new \Entidades\ZgwapChip();
				$oChip->setDataCadastro(new \DateTime("now"));
				
				#################################################################################
				## Resgatar o status inicial do chip
				#################################################################################
				$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "R");
				$oChip->setCodStatus($oStatus);
				
			}

			$oChip->setDdd($ddd);
			$oChip->setIdentificacao($this->getIdentificacao());
			$oChip->setNumero($celular);
			$oChip->setCodOrganizacao($this->getCodOrganizacao());
			$oChip->setCodPais($this->getCodPais());
		
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
			
			return ($oChip->getCodigo());
		
		} catch (\Exception $e) {
			$log->err('Erro ao cadastrar o chip "'.$this->getNumero().'" -> '.$e->getMessage());
			throw new \Exception($e->getMessage());
		}
		
	}
	
	/**
	 * Solicitar o código SMS
	 * @throws \Exception
	 */
	public function solicitaCodigoPorSms() {
		global $log,$tr,$em;
		
		#################################################################################
		## Verifica se o chip existe
		#################################################################################
		if (!$this->_getCodigo()) throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));
		
		#################################################################################
		## Solicitar o registro através de SMS
		#################################################################################
		if ($oChip->getCodStatus()->getCodigo() == "R") {
			$debug 		= false;
			$waUser 	= $oChip->getCodPais()->getCallingCode() . $oChip->getDdd() . $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
			$nickname 	= $oChip->getIdentificacao();    														// This is the username displayed by WhatsApp clients.
			 
			$log->info("Solicitando código SMS para o chip: ".$waUser);

			try {
				// 	Create an instance of WhatsProt.
				$w 			= new \WhatsProt($waUser, $nickname, $debug);
				$return		= $w->codeRequest('sms');
				$log->debug("Retorno SMS: ".serialize($return));
				if ($return->status	== "ok") {
					$login = $return->login;
					$oChip->setLogin($login);
					$em->persist($oChip);
					$em->flush();
					$em->detach($oChip);
				}elseif ($return->status	!= "sent") {
					throw new \Exception($tr->trans("Erro ao enviar a requisição, status do retorno: ".$return->status));
				}
					
			} catch (\Exception $e) {
				$log->err('Erro ao solicitar código através do SMS do chip "'.$waUser.'" -> '.$e->getMessage());
				throw new \Exception($e->getMessage());
			}
		}else{
			throw new \Exception($tr->trans("Status do chip não permite solicitação de código"));
		}
	}
	
	/**
	 * Registrar o código SMS
	 * @throws \Exception
	 */
	public function registrar() {
		global $log,$tr,$em;
	
		#################################################################################
		## Verifica se o chip existe
		#################################################################################
		if (!$this->_getCodigo()) throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)	throw new \Exception($tr->trans("Chip não encontrado !!"));

		#################################################################################
		## Validar o código
		#################################################################################
		if (!$this->getCode()) 					throw new \Exception($tr->trans("Código SMS deve ser informado !!"));
		if (strlen($this->getCode()) < 3) 		throw new \Exception($tr->trans("Código SMS deve conter mais de 3 caracteres!"));
		$code 	= str_replace("-", "", $this->getCode());

		#################################################################################
		## Formatar os campos para efetuar o registro
		#################################################################################
		$debug 		= false;
		$waUser 	= ($oChip->getLogin()) ?  $oChip->getLogin() : $oChip->getCodPais()->getCallingCode() . $oChip->getDdd(). $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
		$nickname 	= $oChip->getIdentificacao();    													// This is the username displayed by WhatsApp clients.
		
		#################################################################################
		## Fazer o registro
		#################################################################################
		try {
			$w 			= new \WhatsProt($waUser, $nickname, $debug);
			$log->info("Vou registrar o número: ".$waUser, "Nickname: ".$nickname);
			$return		= $w->codeRegister($code);
			$log->info(serialize($return));
			$status		= $return->status;
			$senha		= $return->pw;
			$login 		= $return->login;

			if ($status != "ok") {
				$log->err("Falha no registro do chip: $waUser -> ".serialize($return));
				throw new \Exception("Falha ao registrar o chip: $waUser, retorno dos servidores whatsapp: ".$status);
			}
		
		} catch (\Exception $e) {
			$log->err("Falha no registro do chip: $waUser -> ".$e->getMessage());
			throw new \Exception("Falha ao registrar o chip, entre em contato com os administradores do sistema através do email: contato@suaformatura.com");
		}
		

		#################################################################################
		## Atualizar o Chip com a senha retornada
		#################################################################################
		
		#################################################################################
		## Resgatar o status que será salvo
		#################################################################################
		$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "A");
		
		try {
			$oChip->setCodStatus($oStatus);
			$oChip->setSenha($senha);
			$oChip->setCode($code);
			$oChip->setLogin($login);
			$oChip->setDataRegistro(new \DateTime("now"));
		
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
		
		} catch (\Exception $e) {
			$log->err("Falha ao salvar os dados de registro do chip: $waUser -> ".$e->getMessage());
			throw new \Exception("Falha ao salvar os dados de registro do chip: $waUser -> ".$e->getMessage());
		}
		
	}
	
	
	/**
	 * Conectar com os servidores do whatsapp
	 * @throws \Exception
	 */
	public function conectar() {
		global $em,$tr,$log;
		
		#################################################################################
		## Verifica se o chip existe
		#################################################################################
		if (!$this->_getCodigo())	throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)				throw new \Exception($tr->trans("Chip não encontrado !!"));
		$codStatus		= $oChip->getCodStatus()->getCodigo();
		if ($codStatus	!= "A")		throw new \Exception($tr->trans("Status do Chip não permite conexão!!"));
		
		#################################################################################
		## Resgata as configurações do chip
		#################################################################################
		$numero		 	= ($oChip->getLogin()) ?  $oChip->getLogin() : $oChip->getCodPais()->getCallingCode() . $oChip->getDdd(). $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
		$identificacao	= $oChip->getIdentificacao();
		$senha			= $oChip->getSenha();
		$debug			= false;
		
		try {
			$log->debug("Tentando conexão ao whatsapp com o numero: $numero, senha: $senha, Ident: $identificacao");
			$this->w 	= new \WhatsProt($numero, $identificacao, $debug);
			//$this->w->eventManager()->bind("onCredentialsBad", "onCredentialsBad");
			//$this->w->checkCredentials();
			$this->w->connect(); 
			$this->w->loginWithPassword($senha);
			$this->w->sendGetServerProperties();
			$this->w->sendClientConfig();
			
			$log->debug("Conexão WA estabelecida !!!");
			
		} catch (\Exception $e) {
			$log->err("Falha ao conectar com o whatsapp do chip: $numero -> ".$e->getMessage());
			throw new \Exception("Falha ao conectar com o whatsapp do chip: $numero -> ".$e->getMessage());
		}
		
		$log->debug("Iniciar sincronização dos contatos !");
		
		#################################################################################
		## Sincronizar os contatos
		#################################################################################
		$this->sincronizarContatos();
		
		$log->debug("Contatos sincronizados !");
	}
	
	
	/**
	 * Sincronizar os contatos
	 */
	public function sincronizarContatos() {
		global $em,$tr,$log,$oChip;
		
		#################################################################################
		## Verifica se está conectado
		#################################################################################
		if (!$this->_getCodigo())	throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)				throw new \Exception($tr->trans("Chip não encontrado !!"));
		if (!$this->w || !$this->w->isConnected())		{
			$this->conectar();
		}
		
		#################################################################################
		## Verifica se já atualizou alguma vez
		#################################################################################
		if (!$oChip->getDataUltimaSincronizacao()) {
			$syncType		= 0; 
		}else{
			$syncType		= 2;
		}
		
		#################################################################################
		## Resgata a lista de contatos
		#################################################################################
		$celulares			= $this->_getCelularesOrganizacao($oChip->getCodOrganizacao()->getCodigo());
		$contatos			= array();
		$this->_contacts	= array();
		for ($i = 0; $i < sizeof($celulares); $i++) {
			$numero		= "+".$oChip->getCodPais()->getCallingCode() . $celulares[$i]->getTelefone();
			$contatos[]	= $numero;
		}
		
		#################################################################################
		## Bind event handler
		#################################################################################
		$this->w->eventManager()->bind('onGetSyncResult', '\Zage\Wap\Chip::onSyncResult');

		#################################################################################
		## send dataset to server
		#################################################################################
		$this->w->sendSync($contatos,null,$syncType);

		#################################################################################
		## wait for response
		#################################################################################
		/*while (true) {
			$this->w->pollMessage();
		}*/
		
		#################################################################################
		## Atualiza as informações do Chip
		#################################################################################
		$em->beginTransaction();
		try {
			$oChip->setDataUltimaSincronizacao(new \DateTime("now"));
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
			$em->commit();
		} catch (\Exception $e) {
			$log->err($tr->trans("Falha ao atualizar a data da última sincronização do chip: $oChip->getCodigo() ".$e->getMessage()));
			throw new \Exception($tr->trans("Falha ao atualizar a data da última sincronização do chip: $oChip->getCodigo() ".$e->getMessage()));
		}
	}
	

	/**
	 * Adiciona as informações do Whatsapp na tabela de telefones
	 * @param number $codChip
	 * @param number $numero
	 * @throws \Exception
	 */
	public function _addContact($codChip,$numero,$waLogin) {
		global $em,$tr,$log;
		
		#################################################################################
		## Verifica se o código foi passado e se o chip existe
		#################################################################################
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
		
		#################################################################################
		## Resgata o registro do telefone
		#################################################################################
		$tel		= self::_getCelular($numero, $oChip->getCodOrganizacao()->getCodigo(),$waLogin);
		
		if (!$tel) {
			$log->err('Telefone "'.$numero.'" não encontrado na lista de usuários');
		}else{
			$tel->setIndTemWa(1);
			$tel->setDataUltVerificacao(new \DateTime("now"));
			$tel->setWaLogin($waLogin);
			try {
				$em->beginTransaction();
				$em->persist($tel);
				$em->flush();
				$em->detach($tel);
				$em->commit();
			} catch (\Exception $e) {
				$log->err($tr->trans("Falha ao atualizar o status do contato: $tel->getNumero() ".$e->getMessage()));
				throw new \Exception($tr->trans("Falha ao atualizar o status do contato: $tel->getNumero() ".$e->getMessage()));
			}
		}
	}
	
	/**
	 * Remove as informações do Whatsapp na tabela de telefones
	 * @param number $codChip
	 * @param number $numero
	 * @throws \Exception
	 */
	public function _delContact($codChip,$numero,$waLogin) {
		global $em,$tr,$log;
	
		#################################################################################
		## Verifica se o código foi passado e se o chip existe
		#################################################################################
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
		
		#################################################################################
		## Resgata o registro do telefone
		#################################################################################
		$tel		= self::_getCelular($numero, $oChip->getCodOrganizacao()->getCodigo(),$waLogin);
	
		if (!$tel) {
			$log->err('Telefone "'.$numero.'" não encontrado na lista de usuários');
		}else{
			$tel->setIndTemWa(0);
			$tel->setDataUltVerificacao(new \DateTime("now"));
			$tel->setWaLogin($waLogin);
			try {
				$em->beginTransaction();
				$em->persist($tel);
				$em->flush();
				$em->detach($tel);
				$em->commit;
			} catch (\Exception $e) {
				$log->err($tr->trans("Falha ao atualizar o status do contato: $tel->getNumero() ".$e->getMessage()));
				throw new \Exception($tr->trans("Falha ao atualizar o status do contato: $tel->getNumero() ".$e->getMessage()));
			}
		}
	}
	
	
	/**
	 * Resgatar a lista de telefones da organização
	 * @param number $codOrganizacao
	 * @return multitype:
	 */
	private function _getCelularesOrganizacao($codOrganizacao) {
		global $em;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('t')
			->from('\Entidades\ZgsegUsuarioTelefone','t')
			->leftJoin('\Entidades\ZgsegUsuario', 'u', \Doctrine\ORM\Query\Expr\Join::WITH, 't.codProprietario = u.codigo')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao', 'uo', \Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codUsuario = u.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('uo.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('t.codTipoTelefone'	, ':codTipoTelefone')
			))
			->orderBy('u.nome','ASC')
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codTipoTelefone'	, "C");
				
			$query 		= $qb->getQuery();
			return		($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
	
	#################################################################################
	## Função para tratar o retorno
	#################################################################################
	public static function onSyncResult($result) {
		global $em,$tr,$log,$oChip;
		foreach ($result->existing as $number) {
			$cell	= self::_convertWaIDToCell($number,$oChip->getCodPais()->getCallingCode());
			\Zage\Wap\Chip::_addContact($oChip->getCodigo(),$cell,self::_convertWaIdToWaLogin($number));
		}
		foreach ($result->nonExisting as $number) {
			$cell	= self::_convertWaIDToCell($number,$oChip->getCodPais()->getCallingCode());
			\Zage\Wap\Chip::_delContact($oChip->getCodigo(),$cell,self::_convertWaIdToWaLogin($number));
		}
		$log->debug("OnSynResult ends OK");
		//die(); //to break out of the while(true) loop
	}
	

	/**
	 * Resgatar o registro de um telefone
	 * @param number $numero
	 * @param number $codOrganizacao
	 * @return multitype:
	 */
	public static function _getCelular($numero,$codOrganizacao,$waLogin) {
		global $em;
		
		$aNumbers	= array($numero,substr($numero,0,2)."9".substr($numero,2));
		
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('t')
			->from('\Entidades\ZgsegUsuarioTelefone','t')
			->leftJoin('\Entidades\ZgsegUsuario', 'u', \Doctrine\ORM\Query\Expr\Join::WITH, 't.codProprietario = u.codigo')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao', 'uo', \Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codUsuario = u.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('uo.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->orX(
						$qb->expr()->in('t.telefone'	, ':telefone'),
						$qb->expr()->eq('t.waLogin'		, ':waLogin')
					)
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('telefone'			, $aNumbers)
			->setParameter('waLogin'			, $waLogin);
	
			$query 		= $qb->getQuery();
			return		($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	/**
	 * Converter o retorno do whatsapp em um número de celular
	 * @param string $number
	 * @param number $callingCode
	 */
	public static function _convertWaIDToCell($number,$callingCode) {
		
		#################################################################################
		## Retirar o domínio (sufixo) 
		#################################################################################
		$temp		= split("\@",$number);
		$n			= $temp[0];
		
		#################################################################################
		## Retirar o código do país
		#################################################################################
		$return = preg_replace ("/".$callingCode."/" ,"" ,$n , 1);
		return ($return); 
		
	}
	
	/**
	 * Converter o retorno do whatsapp em um número de login do Wa
	 * @param string $number
	 */
	public static function _convertWaIdToWaLogin($number) {
	
		#################################################################################
		## Retirar o domínio (sufixo)
		#################################################################################
		$temp		= split("\@",$number);
		$n			= $temp[0];
	
		return ($n);
	
	}
	
	/**
	 * Converter um celular para um número do Whatsapp
	 * @param string $celular
	 */
	public function _convertCellToWaNumber($celular) {
		global $em,$tr,$log;
		
		#################################################################################
		## Verifica se o Chip foi informado
		#################################################################################
		if (!$this->_getCodigo())	throw new \Exception($tr->trans("Código do chip deve ser informado !!"));
		$oChip		= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $this->_getCodigo()));
		if (!$oChip)				throw new \Exception($tr->trans("Chip não encontrado !!"));
		
		if (strlen($celular) < 8) 	throw new \Exception($tr->trans("Número do celular inválido !!"));

		
		#################################################################################
		## Número já está completo, retornar o próprio 
		#################################################################################
		if (strlen($celular) > 11) 	return $celular;
		
		#################################################################################
		## Número sem o DDD, colocar o DDD e o código do País do Chip 
		#################################################################################
		if (strlen($celular) < 10) 	{
			$number		= $oChip->getCodPais()->getCallingCode() . $oChip->getDdd() . $celular;
		}
		
		#################################################################################
		## Número sem o Código do País
		#################################################################################
		$number		= $oChip->getCodPais()->getCallingCode() . $celular;
		
		return ($number);
	
	}
	
	
	/**
	 * Buscar os números com wa do usuário
	 * @param number $codUsuario
	 */
	public static function buscaNumeroComWa($codUsuario) {
		global $em,$tr,$log;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('t')
			->from('\Entidades\ZgsegUsuarioTelefone','t')
			->where($qb->expr()->andX(
				$qb->expr()->eq('t.codProprietario'	, ':codUsuario'),
				$qb->expr()->eq('t.indTemWa'		, ':temWa')
			))
			->setParameter('codUsuario'			, $codUsuario)
			->setParameter('temWa'				, 1);
		
			$query 		= $qb->getQuery();
			return		($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
	
	
	/**
	 * Acha um CHIP para enviar mensagem whatsapp
	 * @param number $codUsuario
	 */
	public static function buscaChipUsuario($codUsuario) {
		global $em;
		
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('c')
			->from('\Entidades\ZgwapChip','c')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao', 'uo', \Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codOrganizacao = c.codOrganizacao')
			->where($qb->expr()->andX(
				$qb->expr()->eq('uo.codUsuario'	, ':codUsuario'),
				$qb->expr()->in('c.codStatus'	, ':status')
			))
			->setParameter('codUsuario'			, $codUsuario)
			->setParameter('status'				, array("A"));
		
			$query 		= $qb->getQuery();
			return		($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
	
	/**
	 * Definir o código
	 * @param number $codigo
	 */
	public function _setCodigo($codigo) {
		$this->_codigo	= $codigo;
	}
	
	/**
	 * Retornar o código
	 * @return $_codigo
	 */
	public function _getCodigo() {
		return ($this->_codigo);
	}
}




#################################################################################
## Função para verificar se o chip está bloqueado
#################################################################################
function onCredentialsBad($mynumber, $status, $reason) {
	global $em,$tr,$log,$oChip;
	if ($reason == 'blocked') {

		#################################################################################
		## Resgatar o status que será salvo
		#################################################################################
		$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus'		, "B");
		$oBloqueio	= $em->getReference('\Entidades\ZgwapChipBloqueioTipo'	, "W");

		$oChip->setCodStatus($oStatus);
		$oChip->setCodTipoBloqueio($oBloqueio);
		$oChip->setDataBloqueio(new \DateTime("now"));

		try {
			$em->persist($oChip);
			$em->flush();
			$em->detach($oChip);
		} catch (\Exception $e) {
			$log->err("Falha ao atualizar o status do chip: $oChip->getCodigo() ".$e->getMessage());
			throw new \Exception("Falha ao atualizar o status do chip: $oChip->getCodigo() ".$e->getMessage());
		}

	}
}

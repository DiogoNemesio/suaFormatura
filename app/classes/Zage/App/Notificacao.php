<?php
namespace Zage\App;

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

/**
 * Implementação de notificações
 * 
 * @package: Notificacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class Notificacao extends \Entidades\ZgappNotificacao {
	
	
	const TIPO_MENSAGEM_TEXTO 		= 'TX';
	const TIPO_MENSAGEM_HTML 		= 'H';
	const TIPO_MENSAGEM_TEMPLATE 	= 'TP';
	const TIPO_MENSAGEM_AUDIO 		= 'A';
	
	const TIPO_DEST_USUARIO 		= 'U';
	const TIPO_DEST_ORGANIZACAO		= 'O';
	const TIPO_DEST_PESSOA			= 'P';
	const TIPO_DEST_ANONIMO			= 'A';
	
	
	/**
	 * Array de variáveis / valores
	 * @var array
	 */
	private $variaveis;
	
	/**
	 * Array de usuários associados a notificação
	 * @var array
	 */
	private $usuarios;
	
	/**
	 * Array de organizacoes associadas a notificação
	 * @var array
	 */
	private $organizacoes;
	
	/**
	 * Array de pessoas associadas a notificação
	 * @var array
	 */
	private $pessoas;
	
	/**
	 * Array de Anexos
	 * @var array
	 */
	private $anexos;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	
	/**
	 * Cria uma nova notificação
	 * @param string $tipoMensagem
	 * @param string $tipoDestinatario
	 */
	public function __construct($tipoMensagem,$tipoDestinatario) {
		global $em;
		
		#################################################################################
		## Valida o tipo de Mensagem
		#################################################################################
		switch ($tipoMensagem) {
			case \Zage\App\Notificacao::TIPO_MENSAGEM_AUDIO:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_HTML:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO:
				break;
			default:
				throw new \Exception('Tipo de mensagem desconhecido');
				break;
		}
		
		#################################################################################
		## Valida o tipo de Destinatário
		#################################################################################
		switch ($tipoDestinatario) {
			case \Zage\App\Notificacao::TIPO_DEST_USUARIO:
			case \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO:
			case \Zage\App\Notificacao::TIPO_DEST_PESSOA:
			case \Zage\App\Notificacao::TIPO_DEST_ANONIMO:
				break;
			default:
				throw new \Exception('Tipo de destinatário desconhecido');
				break;
		}
		
		#################################################################################
		## Resgata os objetos do doctrine referente ao tipo de Mensagem e Destinatário
		#################################################################################
    	$oDest		= $em->getRepository('\Entidades\ZgappNotificacaoDestTipo')->findOneBy(array('codigo'=> $tipoDestinatario));
    	$oMen		= $em->getRepository('\Entidades\ZgappNotificacaoMensTipo')->findOneBy(array('codigo'=> $tipoMensagem));
    	
    	if (!$oDest)	throw new \Exception('Tipo de destinatário não encontrado');
    	if (!$oMen)		throw new \Exception('Tipo de mensagem não encontrada');
		
    	#################################################################################
    	## Salva os tipos
    	#################################################################################
    	$this->setCodTipoDestinatario($oDest);
    	$this->setCodTipoMensagem($oMen);
		
    	#################################################################################
    	## Inicializa os arrays
    	#################################################################################
    	$this->variaveis	= array();
    	$this->usuarios		= array();
    	$this->organizacoes	= array();
    	$this->anexos		= array();
    	$this->pessoas		= array();
    	   
    	#################################################################################
    	## Por padrão a notificação é somente de sistema, ou seja, não envia e-mail nem wa
    	#################################################################################
    	$this->naoEnviaEmail();
    	$this->naoEnviaWa();
    	   
	}

	/**
	 * Salvar a notificação no banco
	 * @throws \Exception
	 */
	public function salva() {
		global $em;
		
		#################################################################################
		## Valida se os campos obrigatórios foram informados
		#################################################################################
		if ($this->getCodTipoMensagem()->getCodigo() == \Zage\App\Notificacao::TIPO_MENSAGEM_HTML || $this->getCodTipoMensagem()->getCodigo() == \Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO) {
			if (!$this->getAssunto())	throw new \Exception('Assunto deve ser definido !!!');
			if (!$this->getMensagem())	throw new \Exception('Mensagem deve ser definida !!!');
		}elseif ($this->getCodTipoMensagem()->getCodigo() == \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE){
			if (!$this->getCodTemplate())	throw new \Exception('Template deve ser definido para esse tipo de notificação !!!');
		}
		
		#################################################################################
		## Validar alguns campos
		#################################################################################
		if (strlen($this->getAssunto() > 60)) $this->setAssunto(substr($this->getAssunto(),0,60));

		#################################################################################
		## Verificar se a notificação tem alguma via para o usuário
		#################################################################################
		if (!$this->getIndViaSistema() && !$this->getIndViaEmail() && !$this->getIndViaWa() ) {
			throw new \Exception('Pelo menos uma via deve ser informada !!!');
		}

		#################################################################################
		## Verificar se a notificação tem a via de e-mail, se o campo email estiver definido
		#################################################################################
		if ($this->getEmail() && !$this->getIndViaEmail())	throw new \Exception('E-mail só pode ser definido caso a notificação tenha a via de e-mail definida');
		
		#################################################################################
		## Verificar se a notificação tem um destinatário válido
		#################################################################################
		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_USUARIO) && (sizeof($this->usuarios) == 0)) 
			throw new \Exception('Notificação de usuário deve ter pelo menos 1 usuário associado !!');
		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO) && (sizeof($this->organizacoes) == 0)) 
			throw new \Exception('Notificação de Organização deve ter pelo menos 1 organização associada !!');
		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_PESSOA) && (sizeof($this->pessoas) == 0))
			throw new \Exception('Notificação de Pessoa deve ter pelo menos 1 pessoa associada !!');
		
		#################################################################################
		## Validar o tipo de destinatário com a via de envio
		#################################################################################
		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_PESSOA) && ($this->getIndViaSistema()))
			throw new \Exception('Notificação de Pessoa não pode ser via Sistema !!');		
		
		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_PESSOA) && ($this->getIndViaWa()))
			throw new \Exception('Notificação de Pessoa não pode ser via WhatsApp!!');

		if (($this->getCodTipoDestinatario()->getCodigo() == \Zage\App\Notificacao::TIPO_DEST_ANONIMO) && (!$this->getEmail()))
			throw new \Exception('Notificação Anônima deve ter o e-mail definido !!');
		
		
		#################################################################################
		## Ajusta os campos da via de notificação
		#################################################################################
		if (!$this->getIndViaEmail())	$this->naoEnviaEmail();
		if (!$this->getIndViaWa())		$this->naoEnviaWa();
		if (!$this->getIndViaSistema())	$this->naoEnviaSistema();
		
		#################################################################################
		## Criar o objeto do doctrine
		#################################################################################
		$_not		= new \Entidades\ZgappNotificacao();

		#################################################################################
		## Calcular se será necessário processar (enviar a notificação)
		#################################################################################
		if ($this->getIndViaEmail() || $this->getIndViaWa()) {
			$this->setIndProcessada(0);
		}else{
			$this->setIndProcessada(1);
		}
		
		#################################################################################
		## Salva os campos
		#################################################################################
		$_not->setCodTipoDestinatario($this->getCodTipoDestinatario());
		$_not->setCodTipoMensagem($this->getCodTipoMensagem());
		$_not->setCodTemplate($this->getCodTemplate());
		$_not->setCodRemetente($this->getCodRemetente());
		$_not->setData(new \DateTime("now"));
		$_not->setIndViaEmail($this->getIndViaEmail());
		$_not->setIndViaWa($this->getIndViaWa());
		$_not->setIndViaSistema($this->getIndViaSistema());
		$_not->setAssunto($this->getAssunto());
		$_not->setMensagem($this->getMensagem());
		$_not->setIndProcessada($this->getIndProcessada());
		$_not->setEmail($this->getEmail());
		$_not->setNome($this->getNome());
		$em->persist($_not);
		
		#################################################################################
		## Salva as variáveis
		#################################################################################
		if (sizeof($this->variaveis) > 0) {
			foreach ($this->variaveis as $var => $valor) {
				$oVar	= new \Entidades\ZgappNotificacaoVariavel();
				$oVar->setCodNotificacao($_not);
				$oVar->setVariavel($var);
				$oVar->setValor($valor);
				$em->persist($oVar);
			}
		}

		#################################################################################
		## Salva as associações de usuários
		#################################################################################
		if (sizeof($this->usuarios) > 0) {
			foreach ($this->usuarios as $codUsuario => $oUsu) {
				$oAssUsu	= new \Entidades\ZgappNotificacaoUsuario();
				$oAssUsu->setCodNotificacao($_not);
				$oAssUsu->setCodUsuario($oUsu);
				$oAssUsu->setIndLida(0);
				$em->persist($oAssUsu);
			}
		}
		
		#################################################################################
		## Salva as associações de organizações
		#################################################################################
		if (sizeof($this->organizacoes) > 0) {
			foreach ($this->organizacoes as $codOrg => $oOrg) {
				$oAssOrg	= new \Entidades\ZgappNotificacaoOrganizacao();
				$oAssOrg->setCodNotificacao($_not);
				$oAssOrg->setCodOrganizacao($oOrg);
				$em->persist($oAssOrg);
			}
			
			#################################################################################
			## Salvar o registro de controle de leitura para cada usuário da organização
			#################################################################################
			$usuarios	= $em->getRepository('\Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codOrganizacao' => $codOrg,'codStatus' => array('A','B')));
			for ($i = 0; $i < sizeof($usuarios); $i++) {
				$oAssUsu	= new \Entidades\ZgappNotificacaoUsuario();
				$oAssUsu->setCodNotificacao($_not);
				$oAssUsu->setCodUsuario($usuarios[$i]->getCodUsuario());
				$oAssUsu->setIndLida(0);
				$em->persist($oAssUsu);
			}
		}

		#################################################################################
		## Salva as associações de pessoas
		#################################################################################
		if (sizeof($this->pessoas) > 0) {
			foreach ($this->pessoas as $codPessoa => $oPessoa) {
				$oAssPessoa	= new \Entidades\ZgappNotificacaoPessoa();
				$oAssPessoa->setCodNotificacao($_not);
				$oAssPessoa->setCodPessoa($oPessoa);
				$em->persist($oAssPessoa);
			}
		}
		
		
		#################################################################################
		## Salva os anexos
		#################################################################################
		if (sizeof($this->anexos) > 0) {
			foreach ($this->anexos as $nome => $anexo) {
				$_notAnexo		= new \Entidades\ZgappNotificacaoAnexo();
				$_notAnexo->setCodNotificacao($_not);
				$_notAnexo->setNome($nome);
				$_notAnexo->setAnexo($anexo);
				$em->persist($_notAnexo);
			}
		}
		
		#################################################################################
		## Salva no banco
		#################################################################################
		/*$em->getConnection()->beginTransaction();
		try {
			$em->flush();
			$em->clear();
			$em->getConnection()->commit();
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			throw new \Exception($e->getMessage());
		}*/
		
	}
	
	/**
	 * Adiciona uma variável / Valor
	 * @param string $variavel
	 * @param string $valor
	 * @throws \Exception
	 */
	public function adicionaVariavel($variavel,$valor) {
		
		#################################################################################
		## Verifica se o tipo da Notificação é Template
		#################################################################################
		if (!$this->getCodTipoMensagem() || !is_object($this->getCodTipoMensagem())) {
			throw new \Exception('Tipo de mensagem deve ser definida !!!');
		}elseif ($this->getCodTipoMensagem()->getCodigo() != \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE) {
			throw new \Exception('Tipo de mensagem deve ser \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE !!!');
		}
		
		$this->variaveis[$variavel]		= $valor;
		
	}

	/**
	 * Associa a notificação a um usuário
	 * @param number $codUsuario
	 */
	public function associaUsuario($codUsuario) {
		global $em;
		
		#################################################################################
		## Verifica se o tipo de destinatário da Notificação é Usuário
		#################################################################################
		if (!$this->getCodTipoDestinatario() || !is_object($this->getCodTipoDestinatario())) {
			throw new \Exception('Tipo de destinatário deve ser definido !!!');
		}elseif ($this->getCodTipoDestinatario()->getCodigo() != \Zage\App\Notificacao::TIPO_DEST_USUARIO) {
			throw new \Exception('Tipo de destinatário deve ser \Zage\App\Notificacao::TIPO_DEST_USUARIO !!!');
		}
		
		#################################################################################
		## Só associar o usuário caso ainda não esteja
		#################################################################################
		if (!array_key_exists($codUsuario, $this->usuarios)) {
			$oUsuario	= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo'=> $codUsuario));
			if (!$oUsuario)	throw new \Exception('Usuário não encontrado !!!');
			$this->usuarios[$codUsuario]	= $oUsuario;
		}
		
	}
	
	/**
	 * Associa a notificação a uma organização
	 * @param number $codOrganizacao
	 */
	public function associaOrganizacao($codOrganizacao) {
		global $em;
		
		#################################################################################
		## Verifica se o tipo de destinatário da Notificação é Organização
		#################################################################################
		if (!$this->getCodTipoDestinatario() || !is_object($this->getCodTipoDestinatario())) {
			throw new \Exception('Tipo de destinatário deve ser definido !!!');
		}elseif ($this->getCodTipoDestinatario()->getCodigo() != \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO) {
			throw new \Exception('Tipo de destinatário deve ser \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO !!!');
		}
	
		#################################################################################
		## Só associar a organização caso ainda não esteja
		#################################################################################
		if (!array_key_exists($codOrganizacao, $this->organizacoes)) {
			$oOrg	= $em->getRepository('\Entidades\ZgadmOrganizacao')->findOneBy(array('codigo'=> $codOrganizacao));
			if (!$oOrg)	throw new \Exception('Organização não encontrada !!!');
			$this->organizacoes[$codOrganizacao]	= $oOrg;
		}
	}
	
	/**
	 * Associa a notificação a uma pessoa
	 * @param number $codPessoa
	 */
	public function associaPessoa($codPessoa) {
		global $em;
	
		#################################################################################
		## Verifica se o tipo de destinatário da Notificação é Pessoa
		#################################################################################
		if (!$this->getCodTipoDestinatario() || !is_object($this->getCodTipoDestinatario())) {
			throw new \Exception('Tipo de destinatário deve ser definido !!!');
		}elseif ($this->getCodTipoDestinatario()->getCodigo() != \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
			throw new \Exception('Tipo de destinatário deve ser \Zage\App\Notificacao::TIPO_DEST_PESSOA !!!');
		}
	
		#################################################################################
		## Só associar a pessoa caso ainda não esteja
		#################################################################################
		if (!array_key_exists($codPessoa, $this->pessoas)) {
			$oPessoa	= $em->getRepository('\Entidades\ZgfinPessoa')->findOneBy(array('codigo'=> $codPessoa));
			if (!$oPessoa)	throw new \Exception('Pessoa não encontrada !!!');
			$this->pessoas[$codPessoa]	= $oPessoa;
		}
	
	}
	
	/**
	 * Anexar um arquivo a notificação
	 * @param string $nome
	 * @param blob $conteudo
	 * @throws \Exception
	 */
	public function anexarArquivo($nome,$conteudo) {
		global $em;
		
		#################################################################################
		## Adicionar o arquivo no array de anexos, verificar se o nome já foi anexado
		#################################################################################
		if (!array_key_exists($nome, $this->anexos)) {
			$this->anexos[$nome]		= $conteudo;
		}else{
			throw new \Exception('Nome de anexo já utilizado !!');
		}
		
	}
	
	/**
	 * Lista as notificações pendentes de um usuário
	 * @param number $codUsuario
	 */
	public static function listaPendentes($codUsuario) {
		global $em;
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('n')
			->from('\Entidades\ZgappNotificacao','n')
			->leftJoin('\Entidades\ZgappNotificacaoUsuario', 'nu', \Doctrine\ORM\Query\Expr\Join::WITH, 'nu.codNotificacao = n.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('nu.codUsuario'		, ':codUsuario'),
				$qb->expr()->eq('nu.indLida'		, ':lida'),
				$qb->expr()->eq('n.indViaSistema'	, ':indSistema')
			))
			->orderBy('n.data','ASC')
			->setParameter('codUsuario'	, $codUsuario)
			->setParameter('lida'		, 0)
			->setParameter('indSistema'	, 1);
			
			$query 		= $qb->getQuery();
			return		($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Lista as notificações ainda não enviadas
	 */
	public static function listaNaoEnviadas() {
		global $em;
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('n')
			->from('\Entidades\ZgappNotificacao','n')
			->where($qb->expr()->andX(
				$qb->expr()->eq('n.indProcessada'	, ':processada')
			))
			->orderBy('n.data','ASC')
			->setParameter('processada'	, 0);
				
			$query 		= $qb->getQuery();
			return		($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Seta a flag de lida na notificação
	 * @param number $codNotificacao
	 * @param number $codUsuario
	 */
	public static function ler($codNotificacao,$codUsuario) {
		global $em;
		
		#################################################################################
		## Verifica se a notificação existe
		#################################################################################
		$oNot		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao));
		if (!$oNot) exit;
		
		#################################################################################
		## Resgata o objeto do usuário
		#################################################################################
		$oUsu		= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
		if (!$oUsu) exit;
		
		#################################################################################
		## Resgata o registro de leitura
		#################################################################################
		$oNotUsu	= $em->getRepository('\Entidades\ZgappNotificacaoUsuario')->findOneBy(array('codNotificacao' => $codNotificacao,'codUsuario' => $codUsuario));
		if (!$oNotUsu)	$oNotUsu	= new \Entidades\ZgappNotificacaoUsuario();
		
		$oNotUsu->setCodNotificacao($oNot);
		$oNotUsu->setCodUsuario($oUsu);
		$oNotUsu->setDataLeitura(new \DateTime('now'));
		$oNotUsu->setIndLida(1);
		$em->persist($oNotUsu);
		
		#################################################################################
		## Salva no banco
		#################################################################################
		$em->getConnection()->beginTransaction();
		try {
			$em->flush();
			$em->clear();
			$em->getConnection()->commit();
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			throw new \Exception($e->getMessage());
		}
		
	}
	
	/**
	 * Verifica se uma notificação tem anexo
	 * @param number $codNotificacao
	 */
	public static function temAnexo($codNotificacao) {
		global $em;
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('count(n.codigo)')
			->from('\Entidades\ZgappNotificacaoAnexo','n')
			->where($qb->expr()->andX(
				$qb->expr()->eq('n.codNotificacao'	, ':codNotificacao')
			))
			->setParameter('codNotificacao'	, $codNotificacao);
				
			$query 		= $qb->getQuery();
			$num		= $query->getSingleScalarResult();
			
			if ($num > 0)	{
				return true;
			}else{
				return false;
			}
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Enviar os e-mails de uma notificação
	 * @param \Entidades\ZgappNotificacao $notificacao
	 */
	public static function _notificaEmail($codNotificacao) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$tr,$log,$system;
		
		#################################################################################
		## Resgata a notificação
		#################################################################################
		$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao)); 
		if (!$notificacao)	{
			throw new \Exception('Notificação não encontrada');
		}
		
		#################################################################################
		## Verifica se a notificação tem a flag de enviar e-mail
		#################################################################################
		if (!$notificacao->getIndViaEmail())	{
			throw new \Exception('Notificação não está com a flag de envio de e-mail');
		}
		
		#################################################################################
		## Resgatar a mensagem da notificação
		#################################################################################
		$mensagem		= self::_getMensagem($codNotificacao);
		$codTipoDest	= $notificacao->getCodTipoDestinatario()->getCodigo();
		$assunto		= $notificacao->getAssunto();
		
		#################################################################################
		## Criar os objeto do email ,transporte e validador
		#################################################################################
		$mail 			= \Zage\App\Mail::getMail();
		$transport 		= \Zage\App\Mail::getTransport();
		$validator 		= new \Zend\Validator\EmailAddress();
		$htmlMail 		= new MimePart($mensagem);
		$htmlMail->type = "text/html";
		$body 			= new MimeMessage();
		$bodyArray		= array();
		$bodyArray[]	= $htmlMail;
		
		if (!$mail || !$transport) return 0;
			
		
		#################################################################################
		## Colocar os anexos, caso existam
		#################################################################################
		$anexos		= $em->getRepository('\Entidades\ZgappNotificacaoAnexo')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
		for ($a = 0; $a < sizeof($anexos); $a++) {
			$attachment 				= new Mime\Part($anexos[$a]->getAnexo());
			$attachment->type 			= 'application/octet-stream';
			$attachment->filename 		= $anexos[$a]->getNome();
			$attachment->disposition 	= Mime\Mime::DISPOSITION_ATTACHMENT;
			$attachment->encoding 		= Mime\Mime::ENCODING_BASE64;
			$bodyArray[]	= $attachment;
		}
		
		#################################################################################
		## Definir o conteúdo do e-mail
		#################################################################################
		$body->setParts($bodyArray);
		$mail->setBody($body);
		$mail->setSubject("<SF> ".$assunto);
		
		#################################################################################
		## Controlar a quantidade de emails a enviar
		#################################################################################
		$numEmails	= 0;
		
		#################################################################################
		## Verificar o tipo de destinatário
		#################################################################################
		if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
			$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoPessoa')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
		}elseif ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_ANONIMO) {
			$destinatarios	= array();
			$destinatarios[0]["NOME"]	= $notificacao->getNome();
			$destinatarios[0]["EMAIL"]	= $notificacao->getEmail();
		}else{
			$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoUsuario')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
		}

		#################################################################################
		## Cascade da notificação
		#################################################################################
		$em->persist($notificacao);
		
		#################################################################################
		## Verifica se esse tipo de notificação já foi enviada e Cria o log de envio
		#################################################################################
		$mailLog		= $em->getRepository('\Entidades\ZgappNotificacaoLog')->findOneBy(array('codNotificacao' => $notificacao->getCodigo(),'codFormaEnvio' => "E"));
		if (!$mailLog)	{
			$mailLog		= new \Entidades\ZgappNotificacaoLog();
			$oFormaEnvio	= $em->getReference('\Entidades\ZgappNotificacaoFormaEnvio', "E");
			$mailLog->setCodFormaEnvio($oFormaEnvio);
			$mailLog->setCodNotificacao($notificacao);
		}else{
			if ($mailLog->getIndProcessada() == 1) {
				$log->info("Notificação (".$notificacao->getCodigo().") já foi processado o envio dos e-mails");
				return 1;
			}
		}

		#################################################################################
		## Array para controle de registro de envio por destinatário
		#################################################################################
		$logDest	= array();
		
		#################################################################################
		## Resgata a lista de usuários/Pessoas que receberão a notificação
		#################################################################################
		for ($j = 0; $j < sizeof($destinatarios); $j++) {
		
			if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
				$nomeDest		= $destinatarios[$j]->getCodPessoa()->getNome();
				$codDest		= $destinatarios[$j]->getCodPessoa()->getCodigo();
				$emailDest		= $destinatarios[$j]->getCodPessoa()->getEmail();
				$campoDest		= "codPessoa";
			}elseif ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_ANONIMO) {
				$nomeDest		= $destinatarios[$j]["NOME"];
				$codDest		= $destinatarios[$j]["EMAIL"];
				$emailDest		= $destinatarios[$j]["EMAIL"];
				$campoDest		= "email";
			}else{
				$nomeDest		= $destinatarios[$j]->getCodUsuario()->getNome();
				$codDest		= $destinatarios[$j]->getCodUsuario()->getCodigo();
				$emailDest		= $destinatarios[$j]->getCodUsuario()->getUsuario();
				$campoDest		= "codUsuario";
			}
		
			$log->debug("Usuario/Pessoa que será notificado[a]: ".$nomeDest);
		
			#################################################################################
			## Valida o e-mail
			#################################################################################
			if (!$validator->isValid($emailDest)) {
				continue;
			}

			#################################################################################
			## Verifica se a notificação já foi enviada para esse destinatário
			#################################################################################
			$indEnvia		= 1;
			$logDest[$codDest]	= $em->getRepository('\Entidades\ZgappNotificacaoLogDest')->findOneBy(array('codLog' => $mailLog->getCodigo(), $campoDest => $codDest));
			if (!$logDest[$codDest]) {
				$logDest[$codDest]	= new \Entidades\ZgappNotificacaoLogDest();
			}else{
				if (!$logDest[$codDest]->getIndErro()) {
					$indEnvia		= 0;
				}elseif ($logDest[$codDest]->getIndErro() > 4){
					$indEnvia		= 0;
				}
			}

			if ($indEnvia) {
					
				#################################################################################
				## Associa os destinatários
				#################################################################################
				if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
					$oPessoa			= $em->getReference('\Entidades\ZgfinPessoa', $codDest);
					$logDest[$codDest]->setCodPessoa($oPessoa);
				}elseif ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_USUARIO) {
					$oUsu			= $em->getReference('\Entidades\ZgsegUsuario', $codDest);
					$logDest[$codDest]->setCodUsuario($oUsu);
				}else{
					$logDest[$codDest]->setEmail($emailDest);
				}
	
				$logDest[$codDest]->setCodLog($mailLog);
				$logDest[$codDest]->setDataEnvio(new \DateTime("now"));
				$logDest[$codDest]->setIndErro(0);
				$logDest[$codDest]->setErro(null);
				
				#################################################################################
				## Definir os destinatários
				#################################################################################
				if (sizeof($destinatarios) > 1) {
					$mail->addBcc($emailDest);
					if ($notificacao->getEmail() && ($validator->isValid($notificacao->getEmail())) ) $mail->addBcc($notificacao->getEmail());
				}else{
					$mail->addTo($emailDest);
					if ($notificacao->getEmail() && ($validator->isValid($notificacao->getEmail())) ) $mail->addCc($notificacao->getEmail());
				}
	
				$numEmails++;
			}
		}
		
		#################################################################################
		## Salvar as informações e enviar o e-mail
		#################################################################################
		if ($numEmails > 0) {
			try {
				$indOK		= true;
				$log->debug("Vou enviar o e-mail");
				$transport->send($mail);
				$log->debug("E-mail enviado com sucesso !!!");
				$erro		= null;
					
			} catch (\Exception $e) {
				$log->err("Erro ao enviar o e-mail:". $e->getTraceAsString());
				$indOK		= false;
				$erro		= $e->getTraceAsString();
			}
			
			try {
					
				$indProcessada		= ($indOK == true) ? 1 : 0;
				$indErro			= ($indOK == true) ? 0 : 1;
				
				$mailLog->setIndProcessada($indProcessada);
				
				#################################################################################
				## Atualizar o controle de log por destinatário
				#################################################################################
				$em->persist($mailLog);
				foreach ($logDest as $codDest => $aLogDest) {
					$aLogDest->setDataEnvio(new \DateTime("now"));
					$aLogDest->setErro($erro);
					$aLogDest->setIndErro($indErro);
					$em->persist($aLogDest);
				}
				
				$em->flush();
				$em->clear();
			
			} catch (\Exception $e) {
				return 0;
			}
			
			if ($indOK) {
				return 1;
			}else{
				return 0;
			}
		}
		
		return 1;
	}
	
	/**
	 * Enviar as mensagens Whatsapp de uma notificação
	 * @param \Entidades\ZgappNotificacao $notificacao
	 */
	public static function _notificaWa($codNotificacao) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$tr,$log,$system;
	
		#################################################################################
		## Resgata a notificação
		#################################################################################
		$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao));
		if (!$notificacao)	{
			throw new \Exception('Notificação não encontrada');
		}
		
		#################################################################################
		## Verifica se a notificação tem a flag de enviar e-mail
		#################################################################################
		if (!$notificacao->getIndViaWa())	{
			throw new \Exception('Notificação não está com a flag de envio de whatsapp');
		}
		
		$log->debug("Envia wa para notificacao: ".$notificacao->getAssunto());
			
		#################################################################################
		## Não enviar templates via whatsapp
		#################################################################################
		if ($notificacao->getCodTipoMensagem()->getCodigo() == "TP") {
			return 1;
		}
		
		#################################################################################
		## Resgatar a mensagem da notificação
		#################################################################################
		$mensagem		= self::_getMensagem($codNotificacao);
		$codTipoDest	= $notificacao->getCodTipoDestinatario()->getCodigo();
		
		#################################################################################
		## Verificar o tipo de destinatário
		#################################################################################
		if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
			$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoPessoa')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
		}elseif ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_ANONIMO) {
			/** não enviar whatsapp para o tipo de notificação anônima **/
			return 1;
		}else{
			$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoUsuario')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
		}

		#################################################################################
		## Cascade da notificação
		#################################################################################
		$em->persist($notificacao);
		
		#################################################################################
		## Verifica se esse tipo de notificação já foi enviada e Cria o log de envio
		#################################################################################
		$waLog		= $em->getRepository('\Entidades\ZgappNotificacaoLog')->findOneBy(array('codNotificacao' => $notificacao->getCodigo(),'codFormaEnvio' => "W"));
		if (!$waLog)	{
			$waLog		= new \Entidades\ZgappNotificacaoLog();
			$oFormaEnvio	= $em->getReference('\Entidades\ZgappNotificacaoFormaEnvio', "W");
			$waLog->setCodFormaEnvio($oFormaEnvio);
			$waLog->setCodNotificacao($notificacao);
		}else{
			if ($waLog->getIndProcessada() == 1) {
				$log->info("Notificação (".$notificacao->getCodigo().") já foi processado o envio das mensagens whatsapp");
				return 1;
			}
		}
		
		#################################################################################
		## Faz o controle de execução 
		#################################################################################
		$indOK		= true;
		
		#################################################################################
		## Inicializa o array de Chips, que serão usados para enviar as mensagens
		#################################################################################
		$chips 			= array();
		
		#################################################################################
		## Resgata a lista de usuários/Pessoas que receberão a notificação
		#################################################################################
		for ($j = 0; $j < sizeof($destinatarios); $j++) {
		
			if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
				$nomeDest		= $destinatarios[$j]->getCodPessoa()->getNome();
				$codDest		= $destinatarios[$j]->getCodPessoa()->getCodigo();
				$campoDest		= "codPessoa";
			}else{
				$nomeDest		= $destinatarios[$j]->getCodUsuario()->getNome();
				$codDest		= $destinatarios[$j]->getCodUsuario()->getCodigo();
				$campoDest		= "codUsuario";
			}
		
			$log->debug("Usuario/Pessoa que será notificado[a]: ".$nomeDest);
		

			#################################################################################
			## Verifica se a notificação já foi enviada para esse destinatário
			#################################################################################
			$indEnvia		= 1;
			$logDest		= $em->getRepository('\Entidades\ZgappNotificacaoLogDest')->findOneBy(array('codLog' => $waLog->getCodigo(), $campoDest => $codDest));
			if (!$logDest) {
				$logDest	= new \Entidades\ZgappNotificacaoLogDest();
			}else{
				if (!$logDest->getIndErro()) {
					$indEnvia		= 0;
				}elseif ($logDest->getIndErro() > 4){
					$indEnvia		= 0;
				}
			}
			
				
			if ($indEnvia) {
			
				#################################################################################
				## Associa os destinatários
				#################################################################################
				if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
					$oPessoa			= $em->getReference('\Entidades\ZgfinPessoa', $codDest);
					$logDest->setCodPessoa($oPessoa);
				}elseif ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_USUARIO) {
					$oUsu			= $em->getReference('\Entidades\ZgsegUsuario', $codDest);
					$logDest->setCodUsuario($oUsu);
				}
				
				$logDest->setCodLog($waLog);
				
				#################################################################################
				## Busca o Chip que a mensagem será enviada
				#################################################################################
				$c	= \Zage\Wap\Chip::buscaChipUsuario($codDest);
			
				#################################################################################
				## Caso não tenha chips disponíveis, não tentar enviar a mensagem
				#################################################################################
				if (!$c) {
					$indOK	= false;
					$logDest->setDataEnvio(new \DateTime("now"));
					$logDest->setErro("Não encontramos chips disponíveis para enviar a mensagem");
					$logDest->setIndErro($logDest->getIndErro() + 1);
					$em->persist($logDest);
					continue;
				}
			
				$log->debug("Chip selecionado: ".$c->getLogin());
			
				#################################################################################
				## Instancia a classe para envio
				#################################################################################
				$chip		= new \Zage\Wap\Chip();
				$chip->_setCodigo($c->getCodigo());
				
				#################################################################################
				## Converte o número do celular para o formato do whatsapp
				#################################################################################
				$celulares	= \Zage\Wap\Chip::buscaNumeroComWa($codDest);
				if (!$celulares || sizeof($celulares) ==  0)	continue;
			
				try {
						
					if (!isset($chips[$c->getCodigo()])) {
						$log->debug("Tentando conexão com WA!!! ");
						$chip->conectar();
						$log->debug("Conexão ao wa feita com sucesso !!! ");
			
						$chips[$c->getCodigo()]	= $chip;
			
					}
						
					for ($n = 0; $n < sizeof($celulares); $n++) {
						$log->debug("Convertendo o número : ".$celulares[$n]->getTelefone());
						//$waNumber	= $chip->_convertCellToWaNumber($celulares[$n]->getTelefone());
						$waNumber	= $celulares[$n]->getWaLogin();
						$log->debug("Enviando wa para o número: ".$waNumber);
						$ret	= $chips[$c->getCodigo()]->w->sendMessage($waNumber, $mensagem);
						//$log->debug("Retorno do envio: ".$ret);
					}

					$logDest->setDataEnvio(new \DateTime("now"));
					$logDest->setErro(null);
					$logDest->setIndErro(0);
					$em->persist($logDest);
						
				} catch (\Exception $e) {
					$log->err("Mensagem wa não enviada, por problema no chip: ".$chip->getLogin()." -> ". $e->getMessage());
					$logDest->setDataEnvio(new \DateTime("now"));
					$logDest->setErro($e->getTraceAsString());
					$logDest->setIndErro($logDest->getIndErro() + 1);
					$em->persist($logDest);
					$indOK	= false;
				}
			}
		}

		try {
			
			$indProcessada		= ($indOK == true) ? 1 : 0;
			$waLog->setIndProcessada($indProcessada);
			
			$em->persist($waLog);
			$em->flush();
			$em->clear();
		
		} catch (\Exception $e) {
			return 0;
		}
		
		if ($indOK) {
			return 1;
		}else{
			return 0;
		}
	
	}
	
	/**
	 * Retorna a mensagem associada a notificação
	 * @param \Entidades\ZgappNotificacao $notificacao
	 */
	public static function _getMensagem($codNotificacao) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
		
		#################################################################################
		## Resgata a notificação
		#################################################################################
		$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao));
		if (!$notificacao)	{
			throw new \Exception('Notificação não encontrada');
		}
		
		#################################################################################
		## Monta a mensagem
		#################################################################################
		$codTipoMens		= $notificacao->getCodTipoMensagem()->getCodigo();
		
		if ($codTipoMens	== \Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO) {
			$mensagem		= $notificacao->getMensagem();
		}elseif ($codTipoMens	== \Zage\App\Notificacao::TIPO_MENSAGEM_HTML) {
			$mensagem		= $notificacao->getMensagem();
		}elseif ($codTipoMens	== \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE) {
		
			#################################################################################
			## Verificar se o template foi informado
			#################################################################################
			if (!$notificacao->getCodTemplate()) return null;
		
			#################################################################################
			## Resgata as informações do template
			#################################################################################
			$template		= $notificacao->getCodTemplate();
		
			#################################################################################
			## Verificar se o template existe
			#################################################################################
			if (!file_exists(TPL_PATH . '/' . $template->getCaminho())) return null;
		
			#################################################################################
			## Carregando o template html
			#################################################################################
			$tpl	= new \Zage\App\Template();
			$tpl->load(TPL_PATH . '/' . $template->getCaminho());
		
			#################################################################################
			## Atribui as variáveis do template
			#################################################################################
			$variaveis		= $em->getRepository('\Entidades\ZgappNotificacaoVariavel')->findBy(array('codNotificacao' => $notificacao->getCodigo()));
			for ($v = 0; $v < sizeof($variaveis); $v++) {
				$tpl->set($variaveis[$v]->getVariavel(), $variaveis[$v]->getValor());
			}
		
			#################################################################################
			## Por fim exibir a página HTML
			#################################################################################
			$mensagem	= $tpl->getHtml();
		
		}else{
			return null;
		}

		return $mensagem;
		
	}
	
	/**
	 * Define a flag para enviar e-mail
	 */
	public function enviaEmail() {
		$this->setIndViaEmail(1);
	}
	
	/**
	 * Define a flag para enviar Whatsapp
	 */
	public function enviaWa() {
		$this->setIndViaWa(1);
	}
	
	/**
	 * Define a flag para enviar para o sistema
	 */
	public function enviaSistema() {
		$this->setIndViaSistema(1);
	}
	
	/**
	 * Define a flag para não enviar e-mail
	 */
	public function naoEnviaEmail() {
		$this->setIndViaEmail(0);
	}
	
	/**
	 * Define a flag para não enviar Whatsapp
	 */
	public function naoEnviaWa() {
		$this->setIndViaWa(0);
	}
	
	/**
	 * Define a flag para não enviar para o sistema
	 */
	public function naoEnviaSistema() {
		$this->setIndViaSistema(0);
	}
	
	
}
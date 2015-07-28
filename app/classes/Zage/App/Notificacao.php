<?php
namespace Zage\App;

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
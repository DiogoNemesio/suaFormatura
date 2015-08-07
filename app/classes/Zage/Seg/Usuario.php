<?php

namespace Zage\Seg;


/**
 * Usuário
 *
 * @package Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Usuario extends \Entidades\ZgsegUsuario {

	/**
	 * Usuario
	 * @var unknown
	 */
	private $_usuario;
	
	/**
	 * Código Usuario postado no formulário
	 * @var unknown
	 */
	private $_codUsuario;
	
	/**
	 * Associação Usuário - Organizacao
	 * @var unknown
	 */
	private $_oUsuOrg;
	
	/**
	 * Indicador Endereço obrigatório
	 * @var unknown
	 */
	
	private $_indEndObrigatorio;
	
	/**
	 * Perfil
	 * @var unknown
	 */
	private $_perfil;
	
	/**
	 * Cod Organizacao
	 * @var unknown
	 */
	private $_codOrganizacao;
	
	/**
	 * Confirmação de envio de email
	 * @var unknown
	 */
	private $_enviarEmail;
	
	/**
	 * Ententidade do telefone
	 * @var unknown
	 */
	private $_entidadeTel;
	
	/**
	 * Telefones
	 * @var unknown
	 */
	private $_telefone;
	
	/**
	 * Codigo do tipo de telefone
	 * @var unknown
	 */
	private $_codTipoTel;
	
	/**
	 * Codigo do telefone
	 * @var unknown
	 */
	private $_codTelefone;
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	public function _setCodUsuario($codigo) {
		$this->_codUsuario = $codigo;
	}
	
	public function _getCodUsuario() {
		return ($this->_codUsuario);
	}
	
	public function _setUsuOrg($usuOrg) {
		$this->_oUsuOrg = $usuOrg;
	}
	
	public function _getUsuOrg() {
		return ($this->_oUsuOrg);
	}
	
	public function _setIndEndObrigatorio($valor) {
		$this->_indEndObrigatorio = $valor;
	}
	
	public function _getIndEndObrigatorio() {
		return ($this->_indEndObrigatorio);
	}
	
	public function _setPerfil($codigo) {
		$this->_perfil = $codigo;
	}
	
	public function _getPerfil() {
		return ($this->_perfil);
	}
	
	public function _setCodOrganizacao($codigo) {
		$this->_codOrganizacao = $codigo;
	}
	
	public function _getCodOrganizacao() {
		return ($this->_codOrganizacao);
	}
	
	//GET-SET ENTIDADE TELEFONE
	public function _setEntidadeTel($entidade) {
		$this->_entidadeTel = $entidade;
	}
	
	public function _getEntidadeTel() {
		return ($this->_entidadeTel);
	}
	
	public function _setTelefone(array $telefone) {
		$this->_telefone = $telefone;
	}
	
	public function _getTelefone() {
		return ($this->_telefone);
	}
	
	public function _setCodTipoTel(array $tipoTelefone) {
		$this->_codTipoTel = $tipoTelefone;
	}
	
	public function _getCodTipoTel() {
		return ($this->_codTipoTel);
	}
	
	public function _setCodTelefone(array $codTelefone) {
		$this->_codTelefone = $codTelefone;
	}
	
	public function _getCodTelefone() {
		return ($this->_codTelefone);
	}
	
	public function _setEnviarEmail($enviarEmail) {
		$this->_enviarEmail = $enviarEmail;
	}
	
	public function _getEnviarEmail() {
		return ($this->_enviarEmail);
	}
	
    /**
     * Lista as empresas que o usuário tem acesso
     */
    public static function listaOrganizacaoAcesso ($codUsuario) {
    	global $em;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	$qb->select('o')
    	->from('\Entidades\ZgadmOrganizacao','o')
    	->leftJoin('\Entidades\ZgsegUsuarioOrganizacao',	'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 		= uo.codOrganizacao')
    	->leftJoin('\Entidades\ZgsegPerfil', 				'p', 	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo 		= uo.codPerfil')
    	->leftJoin('\Entidades\ZgadmOrganizacaoStatusTipo', 'st',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codStatus 	= st.codigo')
    	->where($qb->expr()->andX(
    		$qb->expr()->eq('uo.codUsuario'			, ':codUsuario'),
   			$qb->expr()->eq('uo.codStatus'			, ':status'),
    		$qb->expr()->eq('p.indAtivo'			, '1'),
    		$qb->expr()->eq('st.indPermiteAcesso'	, '1')
    	))
    	->orderBy('o.identificacao', 'ASC')
    	->setParameter('codUsuario', $codUsuario)
    	->setParameter('status', 	"A");
    	 
    	$query 		= $qb->getQuery();
    	return($query->getResult());
    	
    }
    
    /**
     * Lista todos os usuarios de uma organizacao (retirando os cancelados)
     */
    public static function listaUsuarioOrganizacao ($codOrganizacao, $codTipo) {
    	global $em;
    
    	$qb 	= $em->createQueryBuilder();
    	 
    	$qb->select('uo')
    	->from('\Entidades\ZgsegUsuario','us')
    	->leftJoin('\Entidades\ZgsegUsuarioOrganizacao',		'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'us.codigo 	= uo.codUsuario')
    	->leftJoin('\Entidades\ZgsegPerfil',					'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codPerfil	= p.codigo')
    	->where($qb->expr()->andX(
   			$qb->expr()->eq('uo.codOrganizacao'				, ':codOrganizacao'),
    		$qb->expr()->eq('p.codTipoUsuario'				, ':codTipoUsuario'),
    		$qb->expr()->not($qb->expr()->eq('uo.codStatus'	, ':codStatusExcluido'))
    	))
    	->orderBy('us.nome', 'ASC')
    	->setParameter('codOrganizacao', $codOrganizacao)
    	->setParameter('codStatusExcluido', C)
    	->setParameter('codTipoUsuario', $codTipo);
    	$query 		= $qb->getQuery();
    	return($query->getResult());
    	 
    }
    
    /**
     * Lista todos os usuarios ATIVOS de uma organizacao
     */
    public static function listaUsuarioOrganizacaoAtivo ($codOrganizacao, $codTipo) {
    	global $em;
    
    	$qb 	= $em->createQueryBuilder();
    
    	$qb->select('uo')
    	->from('\Entidades\ZgsegUsuario','us')
    	->leftJoin('\Entidades\ZgsegUsuarioOrganizacao',		'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'us.codigo 	= uo.codUsuario')
    	->leftJoin('\Entidades\ZgsegPerfil',					'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codPerfil	= p.codigo')
    	->where($qb->expr()->andX(
    			$qb->expr()->eq('uo.codOrganizacao'		, ':codOrganizacao'),
    			$qb->expr()->eq('p.codTipoUsuario'		, ':codTipoUsuario'),
    			$qb->expr()->eq('uo.codStatus'			, ':codStatusAtivo'))
    	)
    	->orderBy('us.nome', 'ASC')
    	->setParameter('codOrganizacao', $codOrganizacao)
    	->setParameter('codStatusAtivo', A)
    	->setParameter('codTipoUsuario', $codTipo);
    	$query 		= $qb->getQuery();
    	return($query->getResult());
    
    }
	
    /**
     * Lista os menus do usuário em uma determinada empresa
     */
    public static function listaMenusAcesso ($codUsuario) {
    	global $em,$log,$system;
    	
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('m')
	    	->from('\Entidades\ZgappMenu','m')
	    	->leftJoin('\Entidades\ZgappMenuPerfil'			,'mp'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo 		= mp.codMenu')
	    	->leftJoin('\Entidades\ZgsegUsuarioOrganizacao'	,'uo'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codPerfil 	= mp.codPerfil')
	    	->leftJoin('\Entidades\ZgsegUsuario'			,'u'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 		= uo.codUsuario')
	    	->leftJoin('\Entidades\ZgadmOrganizacao'		,'o'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 		= uo.codOrganizacao')
	    	->where($qb->expr()->andX(
	   			$qb->expr()->eq('m.indFixo'				, '0'),
	   			$qb->expr()->eq('u.codigo'				, ':codUsuario'),
	   			$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
	    		$qb->expr()->eq('mp.codTipoOrganizacao'	, 'o.codTipo')
	    	))
	    	->addOrderBy('m.nivel', 'ASC')
	    	->addOrderBy('m.codMenuPai', 'ASC')
	    	->addOrderBy('mp.ordem', 'ASC')
	    	->setParameter('codUsuario', $codUsuario)
	    	->setParameter('codOrganizacao', $system->getCodOrganizacao());
	    	
	    	$query = $qb->getQuery();
	    	//$log->debug("SQL: ". $query->getSQL());
	    	return($query->getResult());
	    }catch (\Doctrine\ORM\ORMException $e) {
	    	\Zage\App\Erro::halt($e->getMessage());
	    }
    	
    }
    
    /**
     * Verifica se o usuário tem Acesso a organização
     */
    public function temAcessoOrganizacao($codUsuario,$codOrganizacao) {
    	global $em,$system;

    	$qb 	= $em->createQueryBuilder();
    	 
    	try {
    		$qb->select('uo')
    		->from('\Entidades\ZgsegUsuarioOrganizacao'	,'uo')
    		->leftJoin('\Entidades\ZgsegUsuario'		,'u'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 		= uo.codUsuario')
    		->where($qb->expr()->andX(
   				$qb->expr()->eq('u.codigo'				, ':usuario'),
   				$qb->expr()->eq('uo.codOrganizacao'		, ':codOrganizacao')
    		))
    		->setParameter('usuario', $codUsuario)
    		->setParameter('codOrganizacao', $codOrganizacao);
    	
    		$query = $qb->getQuery();
    		$info	= $query->getOneOrNullResult();
    	}catch (\Doctrine\ORM\ORMException $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    	if (!$info)	{
    		return false;
    	}elseif(!$info->getCodStatus()) {
    		return false;
    	}elseif($info->getCodStatus()->getIndPermiteAcesso() == 0) {
    		return false;
    	}
    	 
    	return true;
    }
    
 	
	/**
	 * Busca usuários
	 */
	public static function busca ($sBusca = null,$start = 0,$limite = 10, $colunaOrdem = null,$dirOrdem = null) {
		global $em,$tr,$system;
		 
		//$em->getRepository('Entidades\ZgsegUsuario')->findAll();
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('u')
			->from('\Entidades\ZgsegUsuario','u')
	    	->leftJoin('\Entidades\ZgsegUsuarioStatusTipo'	,'st'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codStatus = st.codigo')
			->where($qb->expr()->eq('u.codOrganizacao'	, ':codOrg'))
			->setParameter('codOrg', $system->getCodOrganizacao());
			
			if ($colunaOrdem !== null) {
				$dir	= strtoupper($dirOrdem);
				if (!$dir)	$dir = "ASC";
				$qb->orderBy("u.".$colunaOrdem, $dir);
			}
			
/*			->orderBy('u.nome', 'ASC')
*/
			if ($sBusca) {
				$qb->andWhere($qb->expr()->orx(
					$qb->expr()->like($qb->expr()->upper('u.usuario'), ':busca'),
					$qb->expr()->like($qb->expr()->upper('u.nome'), ':busca'),
					$qb->expr()->like($qb->expr()->upper('u.email'), ':busca')
				))
				->setParameter('busca', '%'.strtoupper($sBusca).'%');
			}
				
			
			if ($start 	!== null) $qb->setFirstResult( $start );
			if ($limite	!== null) $qb->setMaxResults( $limite );
			 
		
			$query = $qb->getQuery();
			return ($query->getResult());
		
		}catch (\Doctrine\ORM\ORMException $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	/**
	 * Busca usuários
	 */
	public static function getTotalbusca ($sBusca = null) {
		global $em,$tr,$system;
			
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select($qb->expr()->count('u'))
			->from('\Entidades\ZgsegUsuario','u');
	
			if ($sBusca) {
				$qb->where($qb->expr()->orx(
						$qb->expr()->like($qb->expr()->upper('u.usuario'), ':busca'),
						$qb->expr()->like($qb->expr()->upper('u.nome'), ':busca'),
						$qb->expr()->like($qb->expr()->upper('u.email'), ':busca')
				))
				->setParameter('busca', '%'.strtoupper($sBusca).'%');
			}
															
			$query = $qb->getQuery();
			
			return ($query->getSingleScalarResult());
	
		}catch (\Doctrine\ORM\ORMException $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Salvar um usuário
	 */
	public function salvar() {
		global $em,$system,$log,$tr;
	
		#################################################################################
		## Valida campos
		#################################################################################
		/** INFORMAÇÃOES BÁSICAS **/
		//USUARIO
		if (empty($this->getUsuario())) {
			return $tr->trans('O email deve ser preenchido!');
		}elseif (strlen($this->getUsuario()) > 200){
			return $tr->trans('O email não deve conter mais de 200 caracteres!');
		}elseif(\Zage\App\Util::validarEMail($this->getUsuario()) == false){
			return $tr->trans('Email inválido!');
		}else{
			$oUsuario = $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $this->getUsuario()));		
			
			if($oUsuario != null && ($oUsuario->getCodigo() != $this->_codUsuario)){
				return $tr->trans('Já existe um usuário cadastrado com este EMAIL! Por favor, verifique os dados informados.');
			}
		}
		
		//NOME
		if (empty($this->getNome())) {
			return $tr->trans('O nome deve ser preenchido!');
		}elseif (strlen($this->getNome()) < 5){
			return $tr->trans('Nome muito pequeno, informe o nome completo!');
		}elseif (strlen($this->getNome()) > 100){
			return $tr->trans('O nome não deve conter mais de 100 caracteres!');
		}
		
		//APELIDO
		if (empty($this->getApelido())) {
			return $tr->trans('O apelido deve ser preenchido!');
		}elseif (strlen($this->getApelido()) > 60){
			return $tr->trans('O apelido não deve conter mais de 60 caracteres!');
		}
		
		//CPF
		$valCgc			= new \Zage\App\Validador\Cpf();
		if (empty($this->getCpf())) {
			return $tr->trans('O CPF deve ser preenchido!');
		}else{
			if ($valCgc->isValid($this->getCpf()) == false) {
				return $tr->trans('CPF inválido!');
			}else{
				$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => $this->getCpf()));
				if($oUsuario != null && ($oUsuario->getCodigo() != $this->_codUsuario)){
					return $tr->trans('Já existe um usuário cadastrado com este CPF! Por favor,  verifique os dados informados.');
				}
			}
		}
		
		//RG
		if (strlen($this->getRg()) > 14){
			return $tr->trans('O RG não deve conter mais de 14 caracteres!');
		}
		
		// Data Nascimento
		if (empty($this->getDataNascimento())) {
			return $tr->trans('A data de nascimento deve ser preenchida!');
		}else {
			if (\Zage\App\Util::validaData($this->getDataNascimento(), $system->config["data"]["dateFormat"]) == false) {
				return $tr->trans('A data de nascimento está inválida!');
			}
		}

		//SEXO
		if (empty($this->getSexo())) {
			return $tr->trans('O sexo deve ser preenchido!');
		}
		
		/** ENDEREÇO **/
		if (!empty($this->getCodLogradouro())){

			//CEP
			if (empty($this->getCep())) {
				return $tr->trans('O CEP deve ser preenchido!');
			}elseif ((!empty($this->getCep())) && (strlen($this->getCep()) > 8)) {
				return $tr->trans('O CEP não deve conter mais de 8 caracteres!');
			}
			
			//LOGRADOURO
			if (empty($this->getEndereco())) {
				return $tr->trans('O Logradouro deve ser preenchido!');
			}elseif ((!empty($this->getEndereco())) && (strlen($this->getEndereco()) > 100)) {
				return $tr->trans('O logradouro não deve conter mais de 100 caracteres!');
			}
			
			//BAIRRO
			if (empty($this->getBairro())) {
				return $tr->trans('O bairro deve ser preenchido!');
			}elseif ((!empty($this->getBairro())) && (strlen($this->getBairro()) > 60)) {
				return $tr->trans('O bairro não deve conter mais de 60 caracteres!');
			}
			
			//Verificar o endereço informado é corresponte a base dos correios
			if (!empty($this->getIndEndCorreto())) {
				$endCorreto	= 1;
			}else{
				$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $this->getCodLogradouro()));
					
				if (($oLogradouro->getDescricao() != $this->getEndereco()) || ($oLogradouro->getCodBairro()->getDescricao() != $this->getBairro())){
					$endCorreto	= 0;
				}else{
					$endCorreto	= 1;
				}
			}
			
		}else{
			//Verifica se o endereço deve ser obrigatório
			if ($this->_getIndEndObrigatorio() == true){
				//CEP
				if (empty($this->getCep())) {
					return $tr->trans('O CEP deve ser preenchido!');
				}elseif ((!empty($this->getCep())) && (strlen($this->getCep()) > 8)) {
					return $tr->trans('O CEP não deve conter mais de 8 caracteres!');
				}
				
				//LOGRADOURO
				if (empty($this->getEndereco())) {
					return $tr->trans('O Logradouro deve ser preenchido!');
				}elseif ((!empty($this->getEndereco())) && (strlen($this->getEndereco()) > 100)) {
					return $tr->trans('O logradouro não deve conter mais de 100 caracteres!');
				}
					
				//BAIRRO
				if (empty($this->getBairro())) {
					return $tr->trans('O bairro deve ser preenchido!');
				}elseif ((!empty($this->getBairro())) && (strlen($this->getBairro()) > 60)) {
					return $tr->trans('O bairro não deve conter mais de 60 caracteres!');
				}
			}else{
				$endCorreto = null;
			}
		}
		
		//NÚMERO
		if ((!empty($this->getNumero())) && (strlen($this->getNumero()) > 10)) {
			return $tr->trans('O número não deve conter mais de 10 caracteres!');
		}
			
		//COMPLEMENTO
		if ((!empty($this->getComplemento())) && (strlen($this->getComplemento()) > 100)) {
			return $tr->trans('O complemento do endereço não deve conter mais de 100 caracteres!');
		}
					
		#################################################################################
		## Salvar Usuário
		#################################################################################
		$this->_usuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $this->getUsuario()));
		
		if(!$this->_usuario){
			$this->_usuario		= new \Entidades\ZgsegUsuario();
			$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
			$novoUsuario 		= true;
			$enviarEmail 		= true;
			$this->_setEnviarEmail($enviarEmail);
		}else{
			$this->_usuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $this->getUsuario()));
			$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => $this->_usuario->getCodStatus()));
			$oUsuarioOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $this->_getCodigo(), 'codOrganizacao' => $this->_getCodOrganizacao()));
			$novoUsuario 		= false;
			$enviarEmail	= false;
			$this->_setEnviarEmail($enviarEmail);
		}
		
		$dataNasc	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $this->getDataNascimento());
						
		$this->_usuario->setUsuario($this->getUsuario());
		$this->_usuario->setCodStatus($oStatus);
		$this->_usuario->setNome($this->getNome());
		$this->_usuario->setRg($this->getRg());
		$this->_usuario->setDataNascimento($dataNasc);
		$this->_usuario->setApelido($this->getApelido());
		$this->_usuario->setCpf($this->getCpf());
		$this->_usuario->setSexo($this->getSexo());
		$this->_usuario->setCodLogradouro($this->getCodLogradouro());
		$this->_usuario->setIndEndCorreto($endCorreto);
		$this->_usuario->setCep($this->getCep());
		$this->_usuario->setEndereco($this->getEndereco());
		$this->_usuario->setBairro($this->getBairro());
		$this->_usuario->setNumero($this->getNumero());
		$this->_usuario->setComplemento($this->getComplemento());
		
		try {

			$em->persist($this->_usuario);
				
		} catch (\Exception $e) {
			$log->err($e->getTraceAsString());
			die($e->getMessage());
		}
		
		#################################################################################
		## Associação Usuário - Organização
		#################################################################################
		/***  Verificar se o usuário já está associado a organização ***/
		
		if ($novoUsuario) {
			$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
			$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
		}else{
			//$oUsuarioOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));
			if (!$oUsuarioOrg)	{
				$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
				$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
			}else{
				$oUsuarioOrgStatus  = $oUsuarioOrg->getCodStatus();
			}
		}
		
		$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $this->_getCodOrganizacao()));
		$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $this->_getPerfil()));
		
		$oUsuarioOrg->setCodUsuario($this->_usuario);
		$oUsuarioOrg->setCodOrganizacao($oOrg);
		$oUsuarioOrg->setCodPerfil($oPerfil);
		$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);

		$this->_setUsuOrg($oUsuarioOrg);
		$em->persist($oUsuarioOrg);
		
		#################################################################################
		## Telefones
		#################################################################################

		$oUsuTel = new \Zage\App\Telefone();
		$oUsuTel->_setEntidadeTel('Entidades\ZgsegUsuarioTelefone');
		$oUsuTel->_setCodProp($this->_usuario);
		$oUsuTel->_setTelefone($this->_getTelefone());
		$oUsuTel->_setCodTipoTel($this->_getCodTipoTel());
		$oUsuTel->_setCodTelefone($this->_getCodTelefone());
		
		$oUsuTel->salvar();

	}
	
	/**
	 * Excluir um usuário
	 */
	public function excluir() {
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Validar dados
		#################################################################################
		// Validação do usuário
		if (!$this->_getCodUsuario()) {
			return $tr->trans('Parâmetro não informado : COD_USUARIO');
		}
		
		$this->_usuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $this->_getCodUsuario()));

		if (!$this->_usuario) {
			return $tr->trans('Usuário não não existe');			
		}
		
		// Validação da associação
		if (!$this->_getCodOrganizacao()) {
			return $tr->trans('Parâmetro não informado : COD_ORGANIZACAO');
		}
		
		$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $this->_getCodUsuario() , 'codOrganizacao' => $this->_getCodOrganizacao()));
		
		if (!$oUsuOrg) {
			return $tr->trans('Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização.');
		}else{
			if ($oUsuOrg->getCodStatus()->getCodigo() == 'C'){
				return $tr->trans('Este usuário já está cancelado!');
			}
		}
		
		#################################################################################
		## Excluir ou Cancelar usuário
		#################################################################################
		
		\Zage\Seg\Usuario::cancelar($this->_usuario, $oUsuOrg);
		
		/**
		$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codUsuario' => $this->_getCodUsuario()));
		if ($this->_usuario->getCodStatus()->getCodigo() == P){
			if (sizeof($oUsuAdm) == 1 && $oUsuAdm[0]->getCodOrganizacao()->getCodigo() == $this->_getCodOrganizacao() && $oUsuAdm[0]->getCodStatus()->getCodigo() == P){
					
				\Zage\Seg\Usuario::excluirCompleto($this->_usuario, $oUsuOrg);
				
			}else{
				
				\Zage\Seg\Usuario::cancelar($this->_usuario, $oUsuOrg);
			}
			
		}else{
			
			\Zage\Seg\Usuario::cancelar($this->_usuario, $oUsuOrg);
		}
		**/
		
	}
	
	/**
	 * Excluir completo
	 */
	public function excluirCompleto($oUsuario,$oUsuOrg) {
		global $em,$system,$log,$tr;
	
		/*** Exclusão dos telefone ***/
		$oTel = new \Zage\App\Telefone();
		$oTel->_setEntidadeTel('Entidades\ZgsegUsuarioTelefone');
		$oTel->_setCodProp($oUsuario);
		$oTel->excluir();
			
		/*** Exclusão da associação ***/
		$em->remove($oUsuOrg);
	
		/*** Exclusão do convite ***/
		$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codOrganizacaoOrigem' => $oUsuOrg->getCodOrganizacao(),'codUsuarioDestino' => $oUsuario->getCodigo()));
		for ($i = 0; $i < sizeof($oConvite); $i++) {
			$em->remove($oConvite[$i]);
		}

		$log->debug($oUsuario->getNome());
		/*** Exclusão do usuário ***/
		$em->remove($oUsuario);
	}
	
	/**
	 * Cancelar
	 */
	public function cancelar($oUsuario,$oUsuOrg) {
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Validações
		#################################################################################
		if (!$oUsuOrg) {
			return $tr->trans('Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização.');
		}else{
			if ($oUsuOrg->getCodStatus()->getCodigo() == 'C'){
				return $tr->trans('Este usuário já está cancelado!');
			}
		}

		#################################################################################
		## Cancelar
		#################################################################################
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
		$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codOrganizacaoOrigem' => $oUsuOrg->getCodOrganizacao(),'codUsuarioDestino' => $oUsuario->getCodigo(),'codStatus' => A));
	
		/*** Cancelar associação ***/
		$oUsuOrg->setCodStatus($oStatus);
		$oUsuOrg->setDataCancelamento(new \DateTime());
		$em->persist($oUsuOrg);
		
		/*** Cancelar convites ***/
		if($oConvite){
			$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => C));
	
			for ($i = 0; $i < sizeof($oConvite); $i++) {
				$oConvite[$i]->setCodStatus($oConviteStatus);
				$oConvite[$i]->setDataCancelamento(new \DateTime());
				$em->persist($oConvite[$i]);
			}
		}
	}
	
	public function _getCodigo() {
		return $this->_usuario->getCodigo();
	}
	
	public function _getUsuario() {
		return $this->_usuario;
	}
	
}

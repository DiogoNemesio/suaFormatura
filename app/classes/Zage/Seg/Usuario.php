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
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
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
    		$qb->expr()->eq('p.indAtivo'			, '1'),
    		$qb->expr()->eq('st.indPermiteAcesso'	, '1')
    	))
    	->orderBy('o.identificacao', 'ASC')
    	->setParameter('codUsuario', $codUsuario);
    	 
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
     * Lista os módulos que o usuário tem acesso
     */
    public static function listaModulosAcesso ($codUsuario,$codEmpresa) {
    	global $em;
    	 
    	$qb 	= $em->createQueryBuilder();

		try { 
	    	$qb->select('distinct m')
	    	->from('\Entidades\ZgappModulo','m')
	    	->leftJoin('\Entidades\ZgappMenu'				,'me'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo 		= me.codModulo')
	    	->leftJoin('\Entidades\ZgappMenuPerfil'			,'mp'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'me.codigo 		= mp.codMenu')
	    	->leftJoin('\Entidades\ZgsegUsuarioEmpresa'		,'ue'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'ue.codPerfil 	= mp.codPerfil')
	    	->leftJoin('\Entidades\ZgsegUsuario'			,'u'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 		= ue.codUsuario')
	    	->where($qb->expr()->andX(
	    			$qb->expr()->eq('me.indFixo'	, '0'),
	    			$qb->expr()->eq('u.codigo'		, ':codUsuario'),
	    			$qb->expr()->eq('ue.codEmpresa'	, ':codEmpresa')
	    	))
	    	->orderBy('m.nome', 'ASC')
	    	->setParameter('codUsuario', $codUsuario)
	    	->setParameter('codEmpresa', $codEmpresa);
	    	 
	    	$query = $qb->getQuery();
	    	return($query->getResult());
     	
		}catch (\Doctrine\ORM\ORMException $e) {
	    	\Zage\App\Erro::halt($e->getMessage());
	    }
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
	

}

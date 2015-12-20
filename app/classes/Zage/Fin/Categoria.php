<?php

namespace Zage\Fin;


/**
 * Categoria
 *
 * @package Categoria
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Categoria extends \Entidades\ZgfinCategoria {

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
	 * Lista Categorias
	 */
	public static function listaTodas () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('ca')
			->from('\Entidades\ZgfinCategoria','ca')
			->leftJoin('ca.codOrganizacao', 'c')
			->where($qb->expr()->andX(
				$qb->expr()->eq('ca.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('ca.codCategoriaPai,ca.descricao', 'ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
     * Lista Categorias
     */
    public static function lista ($codTipo = null, $codCategoriaPai = null,$codTipoOrg = null) {
    	global $em,$system,$log;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('ca')
	    	->from('\Entidades\ZgfinCategoria','ca')
	    	->where($qb->expr()->orX(
	    		$qb->expr()->andX(
	    			$qb->expr()->eq('ca.codOrganizacao'	, ':codOrganizacao'),
    				$qb->expr()->isNull('ca.codTipoOrganizacao')
	    		),
    			$qb->expr()->andX(
   					$qb->expr()->isNull('ca.codOrganizacao'),
   					$qb->expr()->eq('ca.codTipoOrganizacao'	, ':codTipoOrg')
    			)
	    	))
	    	->orderBy('ca.descricao', 'ASC')
	    	->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
	    	->setParameter('codTipoOrg'		, $codTipoOrg);

	    	if ($codTipo != null) {
	    		$qb->andWhere($qb->expr()->eq('ca.codTipo'	, ':codTipo'))
	    		->setParameter('codTipo', $codTipo);
	    	}
	    	if ($codCategoriaPai != null) {
	    		$qb->andWhere($qb->expr()->eq('ca.codCategoriaPai'	, ':codCategoriaPai'))
	    		->setParameter('codCategoriaPai', $codCategoriaPai);
	    	}else{
	    		$qb->andWhere($qb->expr()->isNull('ca.codCategoriaPai'));
	    	}
	    	$query 		= $qb->getQuery();
	    	$log->info("SQL Cat: ".$query->getSQL());
	    	return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }

    /**
     * Verifica se a categora está sendo usada em alguma conta
     */
    public static function estaEmUso ($codCategoria) {
    	global $em,$system;
    
    	$qbP 	= $em->createQueryBuilder();
    	$qbR 	= $em->createQueryBuilder();
    	 
    	try {
    		$qbP->select('count(cp.codigo) as num')
    		->from('\Entidades\ZgfinContaPagarRateio','cp')
    		->where($qbP->expr()->andX(
   				$qbP->expr()->eq('cp.codCategoria'	, ':codCategoria')
    		))
    		->setParameter('codCategoria', $codCategoria);
    
    		$query 		= $qbP->getQuery();
    		$count		= $query->getSingleScalarResult();
    		
    		if ($count > 0) return true;

    		$qbR->select('count(cr.codigo) as num')
    		->from('\Entidades\ZgfinContaReceberRateio','cr')
    		->where($qbR->expr()->andX(
    			$qbR->expr()->eq('cr.codCategoria'	, ':codCategoria')
    		))
    		->setParameter('codCategoria', $codCategoria);
    		
    		$query 		= $qbR->getQuery();
    		$count		= $query->getSingleScalarResult();
    		
    		if ($count > 0) {
    			return true;
    		}else{
    			return false;
    		}
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
    
    /**
     * Busca uma categoria
     */
    public static function busca($sBusca) {
    	global $em,$system;
    
    	$qb 	= $em->createQueryBuilder();
    		
    	try {
    		$qb->select('ca')
    		->from('\Entidades\ZgfinCategoria','ca')
    		->leftOuterJoin('\Entidades\ZgfinCategoriaTipo', 'ct', \Doctrine\Orm\Query\Expr\Join::WITH, 'ct.codigo = ca.codTipo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('ca.codOrganizacao'	, ':codOrganizacao'),
    			$qb->expr()->like($qb->expr()->upper('ca.nome'), ':busca')
    		))
    		->orderBy('ca.codCategoriaPai,ca.descricao', 'ASC')
    		->setParameter('busca', '%'.strtoupper($sBusca).'%')
    		->setParameter('codOrganizacao', $system->getCodOrganizacao());
    		$query 		= $qb->getQuery();
    		return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }

    
    /**
     * Lista Categorias para montar a combo
     */
    public static function listaCombo ($codTipo,$codTipoOrg) {
    	global $em,$system;
    
    	$qb 	= $em->createQueryBuilder();
    	 
    	try {
    		$qb->select('ca')
    		->from('\Entidades\ZgfinCategoria','ca')
    		->leftJoin('\Entidades\ZgfinCategoria', 'p', \Doctrine\Orm\Query\Expr\Join::WITH, 'p.codigo = ca.codCategoriaPai')
    		->where($qb->expr()->orX(
    			$qb->expr()->andX(
    				$qb->expr()->eq('ca.codOrganizacao'	, ':codOrganizacao'),
    				$qb->expr()->isNull('ca.codTipoOrganizacao')
    			),
    			$qb->expr()->andX(
    				$qb->expr()->isNull('ca.codOrganizacao'),
    				$qb->expr()->eq('ca.codTipoOrganizacao'	, ':codTipoOrg')
    			),
    			$qb->expr()->andX(
    				$qb->expr()->isNull('ca.codOrganizacao'),
    				$qb->expr()->isNull('ca.codTipoOrganizacao')
    			)
    		))
    		->andWhere($qb->expr()->eq('ca.codTipo'	, ':codTipo'))
    		->orderBy('ca.descricao', 'ASC')
    		->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
    		->setParameter('codTipoOrg'		, $codTipoOrg)
    		->setParameter('codTipo'		, $codTipo);
    
    		$query 		= $qb->getQuery();
    		return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
    
}

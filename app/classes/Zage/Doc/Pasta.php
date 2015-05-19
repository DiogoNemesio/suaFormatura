<?php

namespace Zage\Doc;


/**
 * Pasta
 *
 * @package Pasta
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Pasta extends \Entidades\ZgdocPasta {

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
	 * Lista pastas
	 */
	public static function listaTodas () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('p')
			->from('\Entidades\ZgdocPasta','p')
			->leftJoin('p.codEmpresa', 'e')
			->where($qb->expr()->andX(
					$qb->expr()->eq('p.codEmpresa'	, ':codEmpresa')
			))
			->orderBy('p.codPastaPai,p.nome', 'ASC')
			->setParameter('codEmpresa', $system->getCodEmpresa());
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
     * Lista pastas
     */
    public static function lista ($codPastaPai = null) {
    	global $em,$system;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('p')
	    	->from('\Entidades\ZgdocPasta','p')
	    	->leftJoin('p.codEmpresa', 'e')
	    	->where($qb->expr()->andX(
	    		$qb->expr()->eq('p.codEmpresa'	, ':codEmpresa')
	    	))
	    	->orderBy('p.nome', 'ASC')
	    	->setParameter('codEmpresa', $system->getCodEmpresa());
	    	 
	    	if ($codPastaPai != null) {
	    		$qb->andWhere($qb->expr()->eq('p.codPastaPai'	, ':codPastaPai'))
	    		->setParameter('codPastaPai', $codPastaPai);
	    	}else{
	    		$qb->andWhere($qb->expr()->isNull('p.codPastaPai'));
	    	}
	    	$query 		= $qb->getQuery();
	    	return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }


    /**
     * Lista pastas
     */
    public static function buscaPorTipo ($sBusca) {
    	global $em,$system;
    
    	$qb 	= $em->createQueryBuilder();
    		
    	try {
    		$qb->select('p')
    		->from('\Entidades\ZgdocPasta','p')
    		->leftOuterJoin('\Entidades\ZgdocDocumentoTipo', 'dt', \Doctrine\Orm\Query\Expr\Join::WITH, 'p.codigo = dt.codPasta')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.codEmpresa'	, ':codEmpresa'),
    			$qb->expr()->like($qb->expr()->upper('dt.nome'), ':busca')
    		))
    		->orderBy('p.codPastaPai,p.nome', 'ASC')
    		->setParameter('busca', '%'.strtoupper($sBusca).'%')
    		->setParameter('codEmpresa', $system->getCodEmpresa());
    		$query 		= $qb->getQuery();
    		return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
}

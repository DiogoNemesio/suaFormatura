<?php

namespace Zage\Est;


/**
 * Pasta
 *
 * @package Grupo
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Grupo extends \Entidades\ZgestGrupo {

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
			->from('\Entidades\ZgestGrupo','p')
			->orderBy('p.codGrupoPai,p.descricao', 'ASC');

			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
     * Lista pastas
     */
    public static function lista ($codGrupo = null) {
    	global $em,$system;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('p')
	    	->from('\Entidades\ZgestSubgrupo','p')
	    	->orderBy('p.descricao', 'ASC');
	    	 
	    	if ($codGrupo != null) {
	    		$qb->andWhere($qb->expr()->eq('p.codGrupo'	, ':codGrupo'))
	    		->setParameter('codGrupo', $codGrupo);
	    	}else{
	    		$qb->andWhere($qb->expr()->isNull('p.codGrupo'));
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

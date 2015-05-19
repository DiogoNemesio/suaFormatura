<?php

namespace Zage\Doc;


/**
 * Tipo de Documento
 *
 * @package DocumentoTipo
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class DocumentoTipo extends \Entidades\ZgdocDocumentoTipo {

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
	 * Lista Tipos de Documento por Empresa
	 */
	public static function listaTodos () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('dt')
			->from('\Entidades\ZgdocDocumentoTipo','dt')
			->leftJoin('\Entidades\ZgdocPasta', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo = dt.codPasta')
			->where($qb->expr()->andX(
					$qb->expr()->eq('p.codEmpresa'	, ':codEmpresa')
			))
			->orderBy('dt.nome', 'ASC')
			->setParameter('codEmpresa', $system->getCodEmpresa());
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	
    /**
     * Lista Tipos de Documento por Pasta
     */
    public static function lista ($codPasta) {
    	global $em,$system;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('dt')
	    	->from('\Entidades\ZgdocDocumentoTipo','dt')
	    	->where($qb->expr()->andX(
	    		$qb->expr()->eq('dt.codPasta'	, ':codPasta')
	    	))
	    	->orderBy('dt.nome', 'ASC')
	    	->setParameter('codPasta', $codPasta);
	    	 
	    	$query 		= $qb->getQuery();
	    	return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	
    }

    /**
     * Busca um determinado Documento
     */
    public static function busca ($sBusca) {
   		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('dt')
			->from('\Entidades\ZgdocDocumentoTipo','dt')
			->leftJoin('\Entidades\ZgdocPasta', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo = dt.codPasta')
			->where($qb->expr()->andX(
					$qb->expr()->eq('p.codEmpresa'	, ':codEmpresa'),
					$qb->expr()->like($qb->expr()->upper('dt.nome'), ':busca')
			))
			->orderBy('dt.nome', 'ASC')
			->setParameter('codEmpresa', $system->getCodEmpresa())
			->setParameter('busca', '%'.strtoupper($sBusca).'%');
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
    }
    
}


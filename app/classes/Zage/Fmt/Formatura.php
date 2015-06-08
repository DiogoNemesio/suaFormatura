<?php

namespace Zage\Fmt;

/**
 * Organização
 * 
 * @package: Organizacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Formatura {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Lista formatura por organizacao
     *
     * @param integer $ident
     * @return array
     */
	public static function listaFormaturaOrganizacao() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'	,'oa',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->where($qb->expr()->andX(
				$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
				$qb->expr()->eq('o.codTipo'	, '1')
				
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	

}
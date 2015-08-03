<?php

namespace Zage\App;

/**
 * Enquete
 * 
 * @package: Enquete
 * @Author: Jalon Vitor 
 * @version: 1.0.1
 * 
 */

class Enquete {

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
     * Buscar uma organização através da identificação
     *
     * @param integer $ident
     * @return array
     */
	public static function listaEnqueteAtivo() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('e')
			->from('\Entidades\ZgappEnquetePergunta','e')
			->where($qb->expr()->andX(
				$qb->expr()->eq('e.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->gte('e.dataPrazo', ':now')
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', new \DateTime("now"));
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
}
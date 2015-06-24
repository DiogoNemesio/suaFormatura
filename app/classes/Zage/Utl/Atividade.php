<?php

namespace Zage\Utl;

/**
 * Atividade
 * 
 * @package: Atividade
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Atividade {

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
     * Buscar uma atividade através da identificação
     *
     * @param integer $ident
     * @return array
     */
	public static function buscaPorIdentificacao($ident) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('a')
			->from('\Entidades\ZgutlAtividade','a')
			->where($qb->expr()->andX(
				$qb->expr()->eq($qb->expr()->upper('a.identificacao')	, ':ident')
			))
			->setParameter('ident', strtoupper($ident));
	
			$query 		= $qb->getQuery();
			return($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}


}
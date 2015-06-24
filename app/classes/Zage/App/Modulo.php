<?php

namespace Zage\App;

/**
 * Modulo
 * 
 * @package: Modulo
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Modulo {

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
     * Buscar um módulo através do Apelido
     *
     * @param integer $ident
     * @return array
     */
	public static function buscaPorApelido($apelido) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('m')
			->from('\Entidades\ZgappModulo','m')
			->where($qb->expr()->andX(
				$qb->expr()->eq($qb->expr()->upper('m.apelido')	, ':apelido')
			))
			->setParameter('apelido', strtoupper($apelido));
	
			$query 		= $qb->getQuery();
			return($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}


}
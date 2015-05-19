<?php

namespace Zage\Fin;

/**
 * Gerenciar os Bancos
 * 
 * @package: Banco
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Banco {

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
	 *
	 * Busca por bancos
	 */
	public static function busca ($nome = null) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('b')
			->from('\Entidades\ZgfinBanco','b')
			->where(
				$qb->expr()->like(
					$qb->expr()->concat(
						$qb->expr()->upper('b.codBanco'),
						$qb->expr()->concat(
							$qb->expr()->literal(' / '),
							$qb->expr()->upper('b.nome')
						)
					)
					,':nome')
			)
			->orderBy('b.nome','ASC')
			->addOrderBy('b.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
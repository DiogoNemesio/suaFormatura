<?php

namespace Zage\Adm;

/**
 * Gerenciar as cidades
 * 
 * @package: Cidade
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Cidade {

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
	 * Busca por cidades
	 */
	public static function busca ($nome = null) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('c')
			->from('\Entidades\ZgadmCidade','c')
			->leftJoin('\Entidades\ZgadmEstado', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.codUf = e.codUf')
			->where(
				$qb->expr()->like(
					$qb->expr()->concat(
						$qb->expr()->upper('e.codUf'),
						$qb->expr()->concat(
							$qb->expr()->literal(' / '),
							$qb->expr()->upper('c.nome')
						)
					)
					,':nome')
			)
			->orderBy('e.nome','ASC')
			->addOrderBy('c.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
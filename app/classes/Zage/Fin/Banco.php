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
			->setParameter('nome', '%'.strtoupper($nome).'%');
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}


	/**
	 *
	 * Busca as carteiras
	 */
	public static function buscaCarteirasPorAgencia ($codAgencia,$nome = null) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('c')
			->from('\Entidades\ZgfinCarteira','c')
			->leftJoin('\Entidades\ZgfinBanco', 'b', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.codBanco = b.codigo')
			->leftJoin('\Entidades\ZgfinAgencia', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.codBanco = b.codigo')
			->where(
				$qb->expr()->like(
					$qb->expr()->upper('c.codCarteira') ,':nome'
				),
				$qb->expr()->eq('a.codigo'	, ':codAgencia')
			)
			->orderBy('c.codCarteira','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%')
			->setParameter('codAgencia', $codAgencia);
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}
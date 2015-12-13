<?php

namespace Zage\Adm;

/**
 * Gerenciar os Planos
 * 
 * @package: Plano
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Plano {

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
	
	/**
	 *
	 * Buscar valor do plano de acordo como a data base
	 */
	public static function getValorPlano($codPlano) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		$data = new \DateTime("now");
		$data = $data->format($system->config["data"]["datetimeSimplesFormat"]);
	
		//$log->debug($data);
	
		try {
			$qb->select('pv.valor')
			->from('\Entidades\ZgadmPlanoValor','pv')
			->leftJoin('\Entidades\ZgadmPlano'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo 	= pv.codPlano')
			->where($qb->expr()->andx(
					$qb->expr()->eq('p.codigo'				, ':codPlano'),
					$qb->expr()->lte('pv.dataBase'			, ':now')
				)
			)
	
			->setParameter('codPlano', $codPlano)
			->setParameter('now', new \DateTime("now"))
				
			->orderBy('pv.dataBase', 'DESC');
	
			$query 		= $qb->getQuery();
			$query->setMaxResults(1);
			
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
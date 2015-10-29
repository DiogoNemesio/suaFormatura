<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * Gerenciar as convites e convidados
 * 
 * @package: Rifa
 * @Author: Diogo Nemésio
 * @version: 1.0.1
 * 
 */

class Convite {

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
	 * Lista de rifas aptas para venda
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaRifaAptaVenda() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('r')
			->from('\Entidades\ZgfmtRifa','r')
			->leftJoin('\Entidades\ZgadmOrganizacao'	,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= r.codOrganizacao')
			->where($qb->expr()->andx(
							$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
							$qb->expr()->eq('r.indRifaEletronica'	, ':indRifaEletronica'),
							$qb->expr()->eq('r.indRifaGerada'		, ':indRifaEletronica'),
							$qb->expr()->gte('r.dataSorteio'		, ':now')
					)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('indRifaEletronica', '1')
			
			->setParameter('now', new \DateTime("now"))
				
			->orderBy('r.dataSorteio', 'DESC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Lista vendas por formando
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaVendaConviteFormando() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('i')
			->from('\Entidades\ZgfmtConviteExtraItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'	,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codigo 	= i.codConviteConf')
			->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'				, ':codOrganizacao')		
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
				
			->groupBy('v.codFormando');
			//->orderBy('r.codigo', 'ASC');
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
}
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
	 * Lista vendas por formando
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaVendaConviteFormando() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('v')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraEventoConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codigo 	= i.codConviteConf')
			->leftJoin('\Entidades\ZgadmOrganizacao'			,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
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
	
	/**
	* Lista Configuracoes validas
	*
	* @param integer $codOrganizacao
	* @return array
	*/
	public static function listaConviteAptoVenda() {
		global $em,$system, $log;
	
		$qb 	= $em->createQueryBuilder();
		$hoje 	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], date($system->config["data"]["dateFormat"]." 00:00:00"));
		
		try {
			$qb->select('c')
			->from('\Entidades\ZgfmtConviteExtraEventoConf','c')
				->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
				->where($qb->expr()->andx(
						$qb->expr()->eq('o.codigo'					, ':codOrganizacao'),
						$qb->expr()->lte('c.dataInicioPresencial'	, ':now'),
						$qb->expr()->gte('c.dataFimPresencial'		, ':now')
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', $hoje)
				
			->orderBy('c.codigo', 'ASC');
				
			$query 		= $qb->getQuery();
			
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Quantidade convite disponivel por configuração de evento
	 *
	 * @param integer $codFormando
	 * @param integer $codEvento
	 * @return array
	 */
	public static function qtdeConviteDispFormando($codFormando, $codConviteConf) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('c.qtdeMaxAluno - sum(i.quantidade)')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraEventoConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codigo 	= i.codConviteConf')
			->leftJoin('\Entidades\ZgfinPessoa'					,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo 	= v.codFormando')
			//->leftJoin('\Entidades\ZgadmOrganizacao'			,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					//$qb->expr()->eq('o.codigo'			, ':codOrganizacao'),
					$qb->expr()->eq('p.codigo'				, ':codFormando'),
					$qb->expr()->eq('c.codigo'				, ':codConviteConf')
				)
			)
	
			//->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('codFormando'  	, $codFormando)
			->setParameter('codConviteConf'	, $codConviteConf)
	
			->groupBy('c.codEvento');
			//->orderBy('i.codigo', 'ASC');
				
			$query 		= $qb->getQuery();
			//return($query->getResult());
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
}

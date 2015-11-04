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
			$qb->select('v')
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
			->from('\Entidades\ZgfmtConviteExtraConf','c')
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
	 * Quantidade convite disponivel
	 *
	 * @param integer $codFormando
	 * @return array
	 */
	public static function listaConviteDispFormando($codFormando, $codEvento) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('c.qtdeMaxAluno - sum(i.quantidade)')
			->from('\Entidades\ZgfmtConviteExtraItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'	,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codigo 	= i.codConviteConf')
			->leftJoin('\Entidades\ZgfinPessoa'				,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo 	= v.codFormando')
			->leftJoin('\Entidades\ZgfmtEventoTipo'			,'e',	\Doctrine\ORM\Query\Expr\Join::WITH, 'e.codigo 	= c.codTipoEvento')
			//->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					//$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
					$qb->expr()->eq('p.codigo'				, ':codFormando'),
					$qb->expr()->eq('e.codigo'				, ':codTipoEvento')
				)
			)
	
			//->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('codFormando'  , $codFormando)
			->setParameter('codTipoEvento', $codEvento)

			//->groupBy('c.codTipoEvento')
			->orderBy('i.codigo', 'ASC');
				
			$query 		= $qb->getQuery();
			//return($query->getResult());
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
}

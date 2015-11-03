<?php

namespace Zage\Fmt;

/**
 * Gerenciar os orçamentos da Formatura
 * 
 * @package: Orcamento
 * @Author: Daniel Cassela
 * @version: 1.0.1
 * 
 */

class Orcamento {

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
	 * Resgata o último número de versão do orçamento gerado
	 * @param unknown $codFormatura
	 */
	public static function getUltimoNumeroVersao($codFormatura) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('max(o.versao) as versao')
			->from('\Entidades\ZgfmtOrcamento','o')
			->where($qb->expr()->andx(
				$qb->expr()->eq('o.codOrganizacao'		, ':codOrganizacao')
			))
	
			->setParameter('codOrganizacao', $codFormatura);
	
			$query 		= $qb->getQuery();
			$info		= $query->getSingleScalarResult(); 
			
			return ($info);
			
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
					$qb->expr()->eq('o.codigo'					, ':codOrganizacao')
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
}

<?php

namespace Zage\Fmt;

/**
 * Organização
 * 
 * @package: Organizacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Formatura {

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
     * Lista formatura por organizacao
     *
     * @param integer $ident
     * @return array
     */
	public static function listaFormaturaOrganizacao() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'	,'oa',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->where($qb->expr()->andX(
				$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
				$qb->expr()->eq('o.codTipo'	, '1')
				
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Lista os formandos ativos de uma organização
	 * @param number $codOrganizacao
	 */
	public static function listaFormandosAtivos($codOrganizacao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('u')
			->from('\Entidades\ZgsegUsuario','u')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao'	,'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 	= uo.codUsuario')
			->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= uo.codOrganizacao')
			->where($qb->expr()->andX(
				$qb->expr()->eq('uo.codOrganizacao'			, ':codOrganizacao'),
				$qb->expr()->in('uo.codPerfil'				, ':perfil'),
				$qb->expr()->in('uo.codStatus'				, ':status')
			))
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('status'			, array("A"))
			->setParameter('perfil'			, array(4,5));
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Resgata o número de formandos ativos de uma organização
	 * @param number $codOrganizacao
	 */
	public static function getNumFormandosAtivos($codOrganizacao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('count(u.codigo)')
			->from('\Entidades\ZgsegUsuario','u')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao'	,'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 	= uo.codUsuario')
			->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= uo.codOrganizacao')
			->where($qb->expr()->andX(
					$qb->expr()->eq('uo.codOrganizacao'			, ':codOrganizacao'),
					$qb->expr()->in('uo.codPerfil'				, ':perfil'),
					$qb->expr()->in('uo.codStatus'				, ':status')
			))
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('status'			, array("A"))
			->setParameter('perfil'			, array(4,5));
	
			$query 		= $qb->getQuery();
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Calcula o valor total da Formatura
	 * @param unknown $codFormatura
	 */
	public static function getValorTotal ($codFormatura) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('sum(cp.valor + cp.valorJuros + cp.valorMora - (cp.valorDesconto + cp.valorCancelado))')
			->from('\Entidades\ZgfinContaPagar','cp')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->notIn('cp.codStatus'	, ':codStatus')
			))
			->setParameter('codStatus'		, array('C','S'))
			->setParameter('codOrganizacao'	, $codFormatura);
				
			$query 		= $qb->getQuery();
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Resgata o valor já arrecadado para a Formatura
	 * @param int $codFormatura
	 */
	public static function getValorAReceber ($codFormatura) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		try {
			$qb->select('sum(cr.valor + cr.valorJuros + cr.valorMora - (cr.valorDesconto + cr.valorCancelado))')
			->from('\Entidades\ZgfinContaReceber','cr')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->notIn('cr.codStatus'	, ':codStatus')
			))
			->setParameter('codStatus'		, array('C','S'))
			->setParameter('codOrganizacao'	, $codFormatura);
	
			$query 		= $qb->getQuery();
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Resgata as informações do Cerimonial que está administrando a formatura
	 * @param int $codFormatura
	 */
	public static function getCerimonalAdm ($codFormatura) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		$hoje	= new \DateTime('now');
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacaoAdm','ao')
			->leftJoin('\Entidades\ZgadmOrganizacao'	,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'ao.codOrganizacaoPai 	= o.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('ao.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->orX(
						$qb->expr()->andX(
							$qb->expr()->isNotNull('ao.dataValidade'),
							$qb->expr()->lte('ao.dataValidade'	, ':hoje')
						),
						$qb->expr()->isNull('ao.dataValidade')
					)
			))
			->setParameter('hoje'			, $hoje)
			->setParameter('codOrganizacao'	, $codFormatura);
	
			$query 		= $qb->getQuery();
			return($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}
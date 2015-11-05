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
	 * Resgata o valor Total do Orçamento
	 * @param unknown $codFormatura
	 */
	public static function calculaValorTotal($codOrcamento) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('sum(i.quantidade * i.valorUnitario) as total')
			->from('\Entidades\ZgfmtOrcamentoItem','i')
			->where($qb->expr()->andx(
					$qb->expr()->eq('i.codOrcamento'		, ':codOrcamento')
			))
	
			->setParameter('codOrcamento', $codOrcamento);
	
			$query 		= $qb->getQuery();
			$info		= $query->getSingleScalarResult();
				
			return ($info);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}

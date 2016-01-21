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
	 * Resgata o orçamento aceito
	 * @param unknown $codFormatura
	 */
	public static function getVersaoAceita($codFormatura) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('o')
			->from('\Entidades\ZgfmtOrcamento','o')
			->where($qb->expr()->andx(
				$qb->expr()->eq('o.codOrganizacao'		, ':codOrganizacao'),
				$qb->expr()->eq('o.indAceite'			, 1)
			))
	
			->setParameter('codOrganizacao', $codFormatura);
	
			$query 		= $qb->getQuery();
			$info		= $query->getOneOrNullResult();
				
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
	
	/**
	 * Resgata o valor por formando
	 * @param unknown $codFormatura
	 */
	public static function calculaValorFormando($codOrcamento) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('sum(i.quantidade * i.valorUnitario) as total')
			->from('\Entidades\ZgfmtOrcamentoItem','i')
			->where($qb->expr()->andx(
				$qb->expr()->eq('i.codOrcamento'		, ':codOrcamento')
			))
			->setParameter('codOrcamento', $codOrcamento);
	
			$query 				= $qb->getQuery();
			$valorTotal			= \Zage\App\Util::to_float($query->getSingleScalarResult());
			$orcamento			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codOrcamento));
			$qtdeFormandos		= (int) $orcamento->getQtdeFormandos();
			$valorPorFormando	= \Zage\App\Util::to_float(round($valorTotal / $qtdeFormandos,2));
				
			return ($valorPorFormando);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Listar os tipos de evento que o orçamento está cobrindo
	 * @param unknown $codOrcamento
	 */
	public static function listaTipoEventos($codOrcamento) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('distinct et')
			->from('\Entidades\ZgfmtOrcamentoItem','oi')
			->leftJoin('\Entidades\ZgfmtOrcamento', 'o', \Doctrine\ORM\Query\Expr\Join::WITH, 'oi.codOrcamento = o.codigo')
			->leftJoin('\Entidades\ZgfmtPlanoOrcItem', 'poi', \Doctrine\ORM\Query\Expr\Join::WITH, 'oi.codItem = poi.codigo')
			->leftJoin('\Entidades\ZgfmtPlanoOrcGrupoItem', 'pogi', \Doctrine\ORM\Query\Expr\Join::WITH, 'poi.codGrupoItem = pogi.codigo')
			->leftJoin('\Entidades\ZgfmtEventoTipo', 'et', \Doctrine\ORM\Query\Expr\Join::WITH, 'pogi.codTipoEvento = et.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('o.codigo'			, ':codOrcamento'),
				$qb->expr()->eq('o.codOrganizacao'	, ':codOrganizacao')
			))
		
			->orderBy('et.descricao','ASC')
			->setParameter('codOrcamento', $codOrcamento)
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
		
			$query 		= $qb->getQuery();
		
			return($query->getResult());
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
}

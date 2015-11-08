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
	 * Resgata o valor total já provisionado por formando
	 * @param unknown $codFormatura
	 */
	public static function getValorProvisionadoPorFormando($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		#################################################################################
		## Array de status que não serão calculados
		#################################################################################
		$aStatusCanc	= array ("S","C");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$aCat						= array($codCatMensalidade);
		
		#################################################################################
		## Criação dos objetos do querybuilder
		#################################################################################
		$qb 	= $em->createQueryBuilder();
		$qbEx	= $em->createQueryBuilder();
		
		try {
			
			#################################################################################
			## SubQuery para filtrar apenas as contas que estão nas categorias configuradas acima
			#################################################################################
			$qbEx->select('crr')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qbEx->expr()->andx(
				$qbEx->expr()->eq('crr.codContaRec'		, 'cr.codigo'),
				$qbEx->expr()->in('crr.codCategoria'	, ':categoria')
			));
			
			$qb->select('p','SUM(cr.valor + cr.valorOutros - cr.valorDesconto) as total')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa 	= p.codigo')
			->where($qb->expr()->andx(
				$qb->expr()->eq('cr.codOrganizacao'		, ':codOrganizacao'),
				$qb->expr()->notIn('cr.codStatus'		, ':status'),
				$qb->expr()->exists($qbEx->getDql())
			))
			->groupBy("p.cgc")
			
			->setParameter('codOrganizacao'	,$codFormatura)
			->setParameter('status'			,$aStatusCanc)
			->setParameter('categoria'		,$aCat);
			
	
			$query 		= $qb->getQuery();
			$info		= $query->getResult();
	
			return ($info);
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
}

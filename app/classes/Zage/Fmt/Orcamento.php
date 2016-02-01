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
				$qb->expr()->eq('o.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->isNotNull('pogi.codTipoEvento')
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

	/**
	 * Listar os itens de orçamento de um grupo de item de orçamento de uma determinada formatura
	 * Irá listar do orçamento aceito
	 * @param unknown $codOrcamento
	 */
	public static function listaItensGrupoItemOrc($codFormatura,$codGrupoItem) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('oi')
			->from('\Entidades\ZgfmtOrcamentoItem','oi')
			->leftJoin('\Entidades\ZgfmtOrcamento', 'o', \Doctrine\ORM\Query\Expr\Join::WITH, 'oi.codOrcamento = o.codigo')
			->leftJoin('\Entidades\ZgfmtPlanoOrcItem', 'poi', \Doctrine\ORM\Query\Expr\Join::WITH, 'oi.codItem = poi.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('poi.codGrupoItem'	, ':codGrupoItem'),
				$qb->expr()->eq('o.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->eq('o.indAceite'		, ':indAceite')
			))
	
			->orderBy('poi.ordem','ASC')
			->setParameter('indAceite'			,'1')
			->setParameter('codGrupoItem'		,$codGrupoItem)
			->setParameter('codOrganizacao'		,$codFormatura);
	
			$query 		= $qb->getQuery();
	
			return($query->getResult());
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	
	
	/**
	 * Listar as informações de contrato de cada tipo de evento do Orçamento
	 * @param unknown $codOrcamento
	 */
	public static function listaInfoContratoGrupoItemOrc($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfmtPlanoOrcGrupoItem'		, 'POGI');
			$rsm->addFieldResult('POGI', 'CODIGO', 'codigo');
			$rsm->addFieldResult('POGI', 'DESCRICAO', 'descricao');
			$rsm->addScalarResult('valor'		, 'valor');
			$rsm->addScalarResult('qtde'		, 'qtde');

			$query 	= $em->createNativeQuery("
				SELECT 	T.*
				FROM	(
					SELECT	POGI.CODIGO,POGI.DESCRICAO,SUM(OI.QUANTIDADE) AS qtde, SUM(OI.QUANTIDADE*OI.VALOR_UNITARIO) AS valor
					FROM 	ZGFMT_ORCAMENTO_ITEM			OI,
							ZGFMT_ORCAMENTO					O,
							ZGFMT_PLANO_ORC_ITEM			POI,
							ZGFMT_PLANO_ORC_GRUPO_ITEM		POGI
					WHERE   OI.COD_ORCAMENTO				= O.CODIGO
					AND	    OI.COD_ITEM						= POI.CODIGO
					AND		POI.COD_GRUPO_ITEM				= POGI.CODIGO
					AND		O.COD_ORGANIZACAO				= :codOrg
					AND		O.IND_ACEITE					= 1
					GROUP	BY POGI.CODIGO,POGI.DESCRICAO
				) T
				", $rsm);
			$query->setParameter('codOrg'			,$codFormatura);
		
			$info		= $query->getResult();
			return ($info);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Calcula a quantidade contratada por grupo de item de orçamento de uma formatura
	 * @param unknown $codOrcamento
	 */
	public static function calculaQtdeGrupoItemOrc($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfmtPlanoOrcGrupoItem'		, 'POGI');
			$rsm->addFieldResult('POGI', 'CODIGO', 'codigo');
			$rsm->addScalarResult('qtde'		, 'qtde');
	
			$query 	= $em->createNativeQuery("
				SELECT 	T.*
				FROM	(
					SELECT	POGI.CODIGO,SUM(IOCF.QUANTIDADE) AS qtde
					FROM 	ZGFMT_ORCAMENTO_ITEM			OI,
							ZGFMT_ORCAMENTO					O,
							ZGFMT_PLANO_ORC_ITEM			POI,
							ZGFMT_PLANO_ORC_GRUPO_ITEM		POGI,
							ZGFMT_ITEM_ORC_CONTRATO			IOC,
							ZGFMT_ITEM_ORC_CONTRATO_FORNEC	IOCF
					WHERE   OI.COD_ORCAMENTO				= O.CODIGO
					AND	    OI.COD_ITEM						= POI.CODIGO
					AND		POI.COD_GRUPO_ITEM				= POGI.CODIGO
					AND		OI.CODIGO						= IOC.COD_ITEM_ORCAMENTO
					AND		O.CODIGO						= IOC.COD_ORCAMENTO
					AND		IOC.CODIGO						= IOCF.COD_ITEM_CONTRATO
					AND		O.COD_ORGANIZACAO				= :codOrg
					AND		O.IND_ACEITE					= 1
					GROUP	BY POGI.CODIGO
				) T
				", $rsm);
			$query->setParameter('codOrg'			,$codFormatura);
	
			$info		= $query->getResult();
			return ($info);
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Calcula o valor pago por grupo de item de orçamento de uma formatura
	 * @param unknown $codOrcamento
	 */
	public static function calculaValorPagoGrupoItemOrc($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfmtPlanoOrcGrupoItem'		, 'POGI');
			$rsm->addFieldResult('POGI', 'CODIGO', 'codigo');
			$rsm->addScalarResult('valor'		, 'valor');
	
			$query 	= $em->createNativeQuery("
				SELECT 	T.*
				FROM	(
					SELECT	POGI.CODIGO,SUM(HP.VALOR_PAGO) AS valor
					FROM 	ZGFMT_ORCAMENTO_ITEM			OI,
							ZGFMT_ORCAMENTO					O,
							ZGFMT_PLANO_ORC_ITEM			POI,
							ZGFMT_PLANO_ORC_GRUPO_ITEM		POGI,
							ZGFMT_ITEM_ORC_CONTRATO			IOC,
							ZGFIN_CONTA_PAGAR				CP,
							ZGFIN_HISTORICO_PAG				HP
					WHERE   OI.COD_ORCAMENTO				= O.CODIGO
					AND	    OI.COD_ITEM						= POI.CODIGO
					AND		POI.COD_GRUPO_ITEM				= POGI.CODIGO
					AND		OI.CODIGO						= IOC.COD_ITEM_ORCAMENTO
					AND		O.CODIGO						= IOC.COD_ORCAMENTO
					AND		CP.COD_ORGANIZACAO				= O.COD_ORGANIZACAO
					AND		CP.COD_TRANSACAO				= IOC.COD_TRANSACAO
					AND		CP.CODIGO						= HP.COD_CONTA_PAG
					AND		O.COD_ORGANIZACAO				= :codOrg
					AND		O.IND_ACEITE					= 1
					GROUP	BY POGI.CODIGO
				) T
				", $rsm);
			$query->setParameter('codOrg'			,$codFormatura);
	
			$info		= $query->getResult();
			return ($info);
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}

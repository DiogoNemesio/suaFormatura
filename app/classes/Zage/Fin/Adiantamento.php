<?php

namespace Zage\Fin;

/**
 * Gerenciar Adiantamentos
 * 
 * @package: Adiantamento
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Adiantamento {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
	}
	
	/**
	 * Calcular o saldo de adiantamento de uma Pessoa
	 * @param integer $codOrganizacao
	 * @param integer $codPessoa
	 */
	public static function getSaldo ($codOrganizacao,$codPessoa) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		$qb1	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
		

		#################################################################################
		## Calcular os créditos de adiantamento
		#################################################################################
		try {
			$qb1->select('sum(h.valor) as valor')
			->from('\Entidades\ZgfinMovAdiantamento','h')
			->where($qb1->expr()->andX(
				$qb1->expr()->eq('h.codOrganizacao'	, ':codOrganizacao'),
				$qb1->expr()->eq('h.codPessoa'		, ':codPessoa'),
				$qb1->expr()->eq('h.codTipoOperacao', ':codTipoOperacao')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codTipoOperacao'	, "C")
			->setParameter('codPessoa'			, $codPessoa);
			$query 		= $qb1->getQuery();
			$creditos	= $query->getSingleScalarResult();
		
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		

		#################################################################################
		## Calcular os Débitos de adiantamento
		#################################################################################
		try {
			$qb2->select('sum(h.valor) as valor')
			->from('\Entidades\ZgfinMovAdiantamento','h')
			->where($qb1->expr()->andX(
				$qb2->expr()->eq('h.codOrganizacao'	, ':codOrganizacao'),
				$qb2->expr()->eq('h.codPessoa'		, ':codPessoa'),
				$qb2->expr()->eq('h.codTipoOperacao', ':codTipoOperacao')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codTipoOperacao'	, "D")
			->setParameter('codPessoa'			, $codPessoa);
			$query 		= $qb2->getQuery();
			$debitos	= $query->getSingleScalarResult();
		
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular o saldo
		#################################################################################
		$saldo		= round(floatval($creditos),2) + round(floatval($debitos),2);
		return $saldo;
	}



	/**
	 * Calcular o saldo de adiantamento por Pessoa de uma organização
	 * @param integer $codOrganizacao
	 */
	public static function listaSaldoPorPessoa($codOrganizacao) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('Entidades\ZgfinPessoa'	, 'P');
			$rsm->addScalarResult('COD_PESSOA'				, 'COD_PESSOA');
			$rsm->addScalarResult('NOME'					, 'NOME');
			$rsm->addScalarResult('CGC'						, 'CGC');
			$rsm->addScalarResult('SALDO'					, 'SALDO');
		
			$query 	= $em->createNativeQuery("
			SELECT	P.CODIGO AS COD_PESSOA,P.CGC,P.NOME, SUM(IF(A.COD_TIPO_OPERACAO = 'C',IFNULL(A.VALOR,0),IFNULL(A.VALOR,0)*-1)) SALDO 
			FROM 	ZGFIN_MOV_ADIANTAMENTO 		A
			LEFT OUTER JOIN ZGFIN_PESSOA P ON (A.COD_PESSOA = P.CODIGO)
			WHERE 	A.COD_ORGANIZACAO 		= :codOrg
			GROUP 	BY P.CODIGO,P.CGC,P.NOME
			ORDER	BY P.NOME
			", $rsm);
			$query->setParameter('codOrg'		, $codOrganizacao);

			return($query->getResult());
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		
	}
	
}
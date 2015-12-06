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
				$qb1->expr()->eq('h.codTipoOperacao'	, 'C')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
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
			$qb1->select('sum(h.valor) as valor')
			->from('\Entidades\ZgfinMovAdiantamento','h')
			->where($qb1->expr()->andX(
					$qb1->expr()->eq('h.codOrganizacao'	, ':codOrganizacao'),
					$qb1->expr()->eq('h.codPessoa'		, ':codPessoa'),
					$qb1->expr()->eq('h.codTipoOperacao'	, 'D')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codPessoa'			, $codPessoa);
			$query 		= $qb1->getQuery();
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

}
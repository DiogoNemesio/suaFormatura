<?php

namespace Zage\Adm;

/**
 * Organização
 * 
 * @package: Organizacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Organizacao {

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
     * Buscar uma organização através da identificação
     *
     * @param integer $ident
     * @return array
     */
	public static function buscaPorIdentificacao($ident) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacao','o')
			->where($qb->expr()->andX(
				$qb->expr()->eq($qb->expr()->upper('o.identificacao')	, ':ident')
			))
			->setParameter('ident', strtoupper($ident));
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}

	/**
	 * Listar organizações excluindo os tipos FMT (formatura), CAS (casamentos), ADM (admnistração)
	 *
	 * @return array
	 */
	public static function listaOrganizacaoParceiro() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacao','o')
			->where($qb->expr()->andX(
					$qb->expr()->notIn('o.codTipo'	,array('FMT','CAS','ADM'))
			))
			->orderBy('o.nome','ASC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 *
	 * Busca por Organizacao do tipo parceiro
	 */
	public static function buscaOrganizacaoParceiro ($nome = null) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacao','o')
			->where(
					$qb->expr()->like(
							$qb->expr()->upper('o.nome'), ':nome'
					)
			)
			->andWhere(
					$qb->expr()->notIn('o.codTipo' , array('FMT','CAS','ADM'))
			)
			->orderBy('o.nome','ASC')
			->addOrderBy('o.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

	/**
	 * Listar tipo de organização excluindo os tipos MT (formatura), CAS (casamentos), ADM (admnistração)
	 *
	 * @return array
	 */
	public static function listaTipoOrganizacaoParceiro() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('o')
			->from('\Entidades\ZgadmOrganizacaoTipo','o')
			->where($qb->expr()->andX(
					$qb->expr()->notIn('o.codigo'	,array('FMT','CAS','ADM'))
			))
			->orderBy('o.descricao','ASC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Verifica se o cerimonial já foi configurado
	 *
	 * @return boolean
	 */
	public static function cerimonialEstaConfigurado($codOrganizacao) {
		global $em;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('count(oc.codigo) as num')
			->from('\Entidades\ZgfmtOrganizacaoCerimonial','oc')
			->where($qb->expr()->andX(
					$qb->expr()->eq('oc.codOrganizacao'	, ':codOrganizacao')
			))
			->setParameter('codOrganizacao',$codOrganizacao);
	
			$query 		= $qb->getQuery();
			$tem		= $query->getSingleScalarResult(); 
			
			if ($tem > 0) 	return true;
			else			return false;
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}
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
	 * Lista todas as formatura vinculadas (vínculo sem data de validade preenchida) a uma organizacao
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaFormaturaOrganizacao($codOrganizacao,$codStatus = null,$codUsuarioCad = null) {
		global $em,$system,$log;
	
		#################################################################################
		## Validar array
		#################################################################################
		if ($codUsuarioCad && !is_array($codUsuarioCad)) throw new \Exception("Parâmetro codUsuarioCad deve ser um array");
		
		#################################################################################
		## Status da formatura
		#################################################################################
		if ($codStatus){
			if (!is_array($codStatus)){
				throw new \Exception("Parâmetro codStatus deve ser um array");
			}
		}else{
			$codStatus = ["A","AA"];
		}
		
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('ofmt')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgfmtOrganizacaoFormatura'	,'ofmt',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= ofmt.codOrganizacao')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'			,'oa',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->where($qb->expr()->andX(
					$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
					$qb->expr()->eq('o.codTipo'				, ':codTipo'),
					$qb->expr()->in('o.codStatus'			, ':codStatus'),
					$qb->expr()->isNull('oa.dataValidade')
					
	
			))
			->setParameter('codOrganizacao', $codOrganizacao)
			->setParameter('codTipo', 'FMT')
			->setParameter('codStatus', $codStatus);
			
			if (!empty($codUsuarioCad)) {
				$qb->andWhere($qb->expr()->andX(
						$qb->expr()->in('o.codUsuarioCadastro', ':$codUsuarioCad')
				));
				$qb->setParameter('$codUsuarioCad'			,$codUsuarioCad);
			}
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Lista os usuários que já cadastrou uma formatura em uma organizacao
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaUsuarioCadFormatura($codOrganizacao) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
				
			try {
			$qb->select('u')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'			,'oa',		\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->leftJoin('\Entidades\ZgsegUsuario'				,'u',		\Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 	= o.codUsuarioCadastro')
			->where($qb->expr()->andX(
					$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
					$qb->expr()->eq('o.codTipo'				, ':codTipo'),
					$qb->expr()->isNull('oa.dataValidade')
			))

			->groupBy('u.codigo')
			->orderBy('u.nome')
			->setParameter('codOrganizacao', $codOrganizacao)
			->setParameter('codTipo', 'FMT');
			
			$query 		= $qb->getQuery();
			
			return($query->getResult());

			} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Lista todas as formatura vinculadas (vínculo sem data de validade preenchida) a uma organizacao
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaFormaturas() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('ofmt')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgfmtOrganizacaoFormatura'	,'ofmt',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= ofmt.codOrganizacao')
			->where($qb->expr()->andX(
					$qb->expr()->eq('o.codTipo'				, ':codTipo')
			))
			->setParameter('codTipo', 'FMT');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Lista as formaturas que um usuario está vinculado em uma organizacao
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaFmtUsuOrg($codUsuario,$codOrganizacao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('uo')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgsegUsuarioOrganizacao'		,'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= uo.codOrganizacao')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'			,'oa',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->where($qb->expr()->andX(
					$qb->expr()->eq('uo.codUsuario'			, ':codUsuario'),
					$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
					$qb->expr()->eq('o.codTipo'				, ':codTipo'),
					$qb->expr()->eq('o.codStatus'			, ':codStatus')
	
			))
			->setParameter('codOrganizacao', $codOrganizacao)
			->setParameter('codUsuario'	   , $codUsuario)
			->setParameter('codTipo'	   , 'FMT')
			->setParameter('codStatus'	   , 'A');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}

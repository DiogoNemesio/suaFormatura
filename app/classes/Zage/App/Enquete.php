<?php

namespace Zage\App;

/**
 * Organização
 * 
 * @package: Organizacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Enquete {

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
	public static function listaEnqueteAtivo() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('e')
			->from('\Entidades\ZgappEnquetePergunta','e')
			->where($qb->expr()->andX(
				$qb->expr()->eq($qb->expr()->upper('o.codOrganizacao')	, ':codOrganizacao'),
				$qb->expr()->lte('e.dataPrazo', ':now')
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', new \DateTime("now"));
	
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
	public static function listaFormaturaOrganizacao() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('ofmt')
			->from('\Entidades\ZgadmOrganizacao','o')
			->leftJoin('\Entidades\ZgfmtOrganizacaoFormatura'	,'ofmt',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= ofmt.codOrganizacao')
			->leftJoin('\Entidades\ZgadmOrganizacaoAdm'			,'oa',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= oa.codOrganizacao')
			->where($qb->expr()->andX(
					$qb->expr()->eq('oa.codOrganizacaoPai'	, ':codOrganizacao'),
					$qb->expr()->eq('o.codTipo'				, ':codTipo'),
					$qb->expr()->isNull('oa.dataValidade')
	
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
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
	public static function listaFmtUsuOrg($codUsuario) {
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
					$qb->expr()->eq('o.codTipo'				, ':codTipo')
	
			))
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('codUsuario'	   , $codUsuario)
			->setParameter('codTipo'	   , 'FMT');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
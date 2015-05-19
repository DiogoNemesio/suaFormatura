<?php

namespace Zage\Est;


/**
 * Estoque
 *
 * @package Grupo
 * @author Diogo Nemésio
 * @version 1.0.1
 */
class Subgrupo extends \Entidades\ZgestSubgrupoMaterial {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
	 * Lista os subgrupos 
	 */
	public static function listaTodos () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('s')
			->from('\Entidades\ZgestSubgrupoMaterial','s')
			->leftJoin('\Entidades\ZgestGrupoMaterial', 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'g.codigo = s.codGrupo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('g.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('s.descricao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	

	/**
	 * Lista os subgrupos de um determinado grupo
	 */
	public static function lista ($codGrupo) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('s')
			->from('\Entidades\ZgestSubgrupoMaterial','s')
			->leftJoin('\Entidades\ZgestGrupoMaterial', 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'g.codigo = s.codGrupo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('g.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('g.codigo'			, ':codGrupo')
					
			))
			->orderBy('s.descricao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('codGrupo', $codGrupo);
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Verificar se descricao ja Existe
	 */
	public static function existeDescricao ($codSubgrupo,$codGrupo,$descricao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('s')
			->from('\Entidades\ZgestSubgrupoMaterial','s')
			->where($qb->expr()->andX(
					$qb->expr()->eq('s.descricao'		, ':descricao'),
					$qb->expr()->eq('s.codGrupo'		, ':codGrupo')
			))
			->orderBy('s.descricao','ASC')
			->setParameter('descricao'	, $descricao)
			->setParameter('codGrupo'	, $codGrupo);
				
			$query 		= $qb->getQuery();
			$oSubgrupo	= $query->getResult();
				
			if (!$oSubgrupo) {
				return false;
			}else{
				if ($oSubgrupo[0]->getCodigo() != $codSubgrupo) {
					return true;
				}else{
					return false;
				}
			}
				
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

	/**
	 * Lista produtos do subgrupo
	 */
	public static function listaProdutos ($codSubgrupo) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('p')
			->from('\Entidades\ZgestProduto','p')
			->where($qb->expr()->andX(
					$qb->expr()->eq('p.codOrganizacao'			, ':codOrganizacao'),
					$qb->expr()->eq('p.codSubgrupoMaterial'		, ':codSubgrupo')
						
			))
			->orderBy('p.descricao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('codSubgrupo', $codSubgrupo);
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	
	/**
	 * Busca os subgrupos de uma organização
	 */
	public static function busca ($string) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('s')
			->from('\Entidades\ZgestSubgrupoMaterial','s')
			->leftJoin('\Entidades\ZgestGrupoMaterial', 'g', \Doctrine\ORM\Query\Expr\Join::WITH, 'g.codigo = s.codGrupoMaterial')
			->leftJoin('\Entidades\ZgestTipoMaterial', 't', \Doctrine\ORM\Query\Expr\Join::WITH, 't.codigo = g.codTipoMaterial')
			->where(
					$qb->expr()->orx(
						$qb->expr()->like($qb->expr()->upper('s.descricao'),':string')	
					)
			)
			->andWhere($qb->expr()->andX(
					$qb->expr()->eq('t.codOrganizacao'		,':codOrg')
			))
			->orderBy('s.descricao','ASC')
			->setParameter('codOrg', 		$system->getCodOrganizacao())
			->setParameter('string', 		'%'.strtoupper($string).'%');
	
			$query 		= $qb->getQuery();
			$log->debug("SQL: ".$query->getSQL());
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
}


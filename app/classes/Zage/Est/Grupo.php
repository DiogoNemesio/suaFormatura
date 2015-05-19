<?php

namespace Zage\Est;


/**
 * Estoque
 *
 * @package Grupo
 * @author Diogo Nemésio
 * @version 1.0.1
 */
class Grupo extends \Entidades\ZgestGrupoMaterial {

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
	 * Lista os locais de arquivo por organização
	 */
	public static function listaTodos () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('g')
			->from('\Entidades\ZgestGrupoMaterial','g')
			->where($qb->expr()->andX(
				$qb->expr()->eq('g.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('g.descricao','ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	
	/**
	 * Lista os grupos de um determinado grupo
	 */
	public static function lista ($codGrupo) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('g')
			->from('\Entidades\ZgestGrupoMaterial','g')
			->where($qb->expr()->andX(
					$qb->expr()->eq('g.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('g.codGrupoPai'		, ':codGrupoPai')
			))
			->orderBy('g.descricao','ASC')
			->setParameter('codGrupoPai'	, $codGrupo)
			->setParameter('codOrganizacao'	, $system->getCodOrganizacao());
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	
	/**
	 * Verificar se descricao ja Existe
	 */
	public static function existeDescricao ($codGrupo,$codGrupoPai,$descricao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('g')
			->from('\Entidades\ZgestGrupoMaterial','g')
			->where($qb->expr()->andX(
					$qb->expr()->eq('g.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('g.descricao'		, ':descricao')
			))
			->orderBy('g.descricao','ASC')
			->setParameter('descricao', $descricao)
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			
			if (!empty($codGrupoPai)) {
				$qb->andWhere(
					$qb->expr()->eq('g.codGrupoPai'	, ':codGrupoPai')
				);
				$qb->setParameter('codGrupoPai', $codGrupoPai);
				
			}else{
				$qb->andWhere(
					$qb->expr()->isNull('g.codGrupoPai')
				);
			}
			
	
			$query 		= $qb->getQuery();
			$oGrupo		= $query->getResult(); 
			
			if (!$oGrupo) {
				return false;
			}else{
				if ($oGrupo[0]->getCodigo() != $codGrupo) {
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
	 * Lista os locais de arquivo por empresa que estão ativo
	 */
	public static function listaAtivo () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('l')
			->from('\Entidades\ZgdocLocal','l')
			->leftJoin('\Entidades\ZgdocDepartamento', 'd', \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = l.codDepartamento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codEmpresa'	, ':codEmpresa'),
					$qb->expr()->eq('l.indAtivo'	, '1')
			))
			->orderBy('l.nome', 'ASC')
			->setParameter('codEmpresa', $system->getCodEmpresa());
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}

	/**
	 * Busca um local em um departamento de uma empresa
	 */
	public static function buscaLocal ($nome, $departamento) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('l')
			->from('\Entidades\ZgdocLocal','l')
			->leftJoin('\Entidades\ZgdocDepartamento', 'd', \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = l.codDepartamento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codEmpresa'			, ':codEmpresa'),
					$qb->expr()->eq('l.nome'				, ':nome'),
					$qb->expr()->eq('l.codDepartamento'		, ':codDepartamento')
			))
			->setParameter('codEmpresa', 		$system->getCodEmpresa())
			->setParameter('nome', 		 		$nome)
			->setParameter('codDepartamento',	$departamento);
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
}


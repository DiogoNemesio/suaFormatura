<?php

namespace Zage\Doc;


/**
 * Endereço
 *
 * @package Endereço
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Endereco extends \Entidades\ZgdocEndereco{

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
	 * Lista os endereços por empresa
	 */
	public static function listaTodos () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('e')
			->from('\Entidades\ZgdocEndereco','e')
			->leftJoin('\Entidades\ZgdocLocal', 'l', \Doctrine\ORM\Query\Expr\Join::WITH, 'l.codigo = e.codLocal')
			->leftJoin('\Entidades\ZgdocDepartamento', 'd', \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = l.codDepartamento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codEmpresa'	, ':codEmpresa')
			))
			->orderBy('e.nome', 'ASC')
			->setParameter('codEmpresa', $system->getCodEmpresa());
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	
	/**
	 * Lista os endereços por empresa que estão ativo
	 */
	public static function listaAtivo () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('e')
			->from('\Entidades\ZgdocEndereco','e')
			->leftJoin('\Entidades\ZgdocLocal', 'l', \Doctrine\ORM\Query\Expr\Join::WITH, 'l.codigo = e.codLocal')
			->leftJoin('\Entidades\ZgdocDepartamento', 'd', \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = l.codDepartamento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codEmpresa'	, ':codEmpresa'),
					$qb->expr()->eq('e.indAtivo'	, '1')
			))
			->orderBy('e.nome', 'ASC')
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


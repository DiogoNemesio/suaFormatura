<?php

namespace Zage\Rhu;


/**
 * FuncionarioCargo
 *
 * @package FuncionarioCargo
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Cargo extends \Entidades\ZgrhuFuncionarioCargo {

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
	 * Verifica se o cargo está em uso
	 */
	public static function estaEmUso($codCargo) {
		global $em,$system,$_user;
		
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select( $qb->expr()->count('f.codigo'))
			->from('\Entidades\ZgrhuFuncionario','f')
			->where(
				$qb->expr()->eq('f.codCondominio'	, ':codCondominio'),
				$qb->expr()->eq('f.codCargo'		, ':codCargo')
			)
			->setParameter('codCondominio', $system->getCodCondominio())
			->setParameter('codCargo', $codCargo);
				
			$query 		= $qb->getQuery();
			
			$num		= $query->getSingleScalarResult();

			if ($num > 0) {
				return true;
			}else{
				return false;
			}
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 *
	 * Busca por CBO
	 */
	public static function buscaCbo ($nome = null) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('p')
			->from('\Entidades\ZgrhuFuncionarioCbo','p')
			->where(
					$qb->expr()->like(
							$qb->expr()->upper('p.descricao'), ':nome'
					)
			)
			->orderBy('p.descricao','ASC')
			->addOrderBy('p.descricao','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
}


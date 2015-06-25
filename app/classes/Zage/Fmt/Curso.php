<?php

namespace Zage\Fmt;

/**
 * Gerenciar as cidades
 * 
 * @package: Cidade
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Curso {

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
	 *
	 * Busca por Paises
	 */
	public static function busca ($nome = null) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('c')
			->from('\Entidades\ZgfmtCurso','c')
			->where(
					$qb->expr()->like( 
							$qb->expr()->upper('c.nome'), ':nome' 
							)
			)
			->orWhere(
					$qb->expr()->like(
							$qb->expr()->upper('c.codOcde'), ':nome'
					)
			)
			->orderBy('c.nome','ASC')
			->addOrderBy('c.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
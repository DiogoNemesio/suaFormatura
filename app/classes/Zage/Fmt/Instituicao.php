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

class Instituicao {

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
	 * Busca por instutições de ensino
	 */
	public static function busca ($nome = null) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('i')
			->from('\Entidades\ZgfmtInstituicao','i')
			->where(
					$qb->expr()->like( 
							$qb->expr()->upper('i.nome'), ':nome' 
							)
			)
			->orWhere(
					$qb->expr()->like(
							$qb->expr()->upper('i.sigla'), ':nome'
					)
			)
			->orderBy('i.nome','ASC')
			->addOrderBy('i.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
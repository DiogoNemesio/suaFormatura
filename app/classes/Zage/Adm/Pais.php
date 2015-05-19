<?php

namespace Zage\Adm;

/**
 * Gerenciar as cidades
 * 
 * @package: Cidade
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Pais {

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
			$qb->select('p')
			->from('\Entidades\ZgadmPais','p')
			->where(
					$qb->expr()->like( 
							$qb->expr()->upper('p.nome'), ':nome' 
							)
			)
			->orderBy('p.nome','ASC')
			->addOrderBy('p.nome','ASC')
			->setParameter('nome', '%'.strtoupper($nome).'%');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
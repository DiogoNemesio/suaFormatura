<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * Gerenciar as rifas
 * 
 * @package: Rifa
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Rifa {

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
	
	/**
	 * Retorna o próximo número da Rifa
	 *
	 * @param integer $codRifa
	 * @return integer
	 */
	public static function numeroAtual ($codRifa) {
		global $em;

		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('max(n.numero)')
			->from('\Entidades\ZgfmtRifaNumero','n')
			->where($qb->expr()->andX(
				$qb->expr()->eq('n.codRifa'	, ':codRifa')
			))
			->setParameter('codRifa', $codRifa);
		
			$query 		= $qb->getQuery();
			$query->setLockMode(LockMode::PESSIMISTIC_WRITE);

			$numeroAtual = $query->getSingleScalarResult();
			
			if (!$numeroAtual)	$numeroAtual = 0;
			return($numeroAtual);
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	
	/**
	 * Retorna o próximo número da Rifa
	 *
	 * @param integer $codRifa
	 * @return integer
	 */
	public static function proximoNumero ($codRifa) {
		$numeroAtual  	= self::numeroAtual($codRifa);
		$proximo		= $numeroAtual + 1;
			
		return($proximo);
	}
	

}
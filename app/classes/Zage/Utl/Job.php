<?php

namespace Zage\Utl;

/**
 * Job
 * 
 * @package: Job
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Job {

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
     * Listar os jobs a serem executados
     *
     * @return array
     */
	public static function listaJobsAexecutar() {
		global $em;
	
		$qb 	= $em->createQueryBuilder();
		
		$agora	= new \DateTime("now");
			
		try {
			$qb->select('j')
			->from('\Entidades\ZgutlJob','j')
			->where($qb->expr()->andX(
				$qb->expr()->eq('j.indAtivo'				, ':ativo'),
				$qb->expr()->gte('j.dataProximaExecucao'	, ':data')
			))
			->setParameter('ativo', 1)
			->setParameter('data', $agora, \Doctrine\DBAL\Types\Type::DATE);
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}


}
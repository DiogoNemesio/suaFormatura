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
		global $em,$log;
	
		$qb 	= $em->createQueryBuilder();
		
		$agora	= new \DateTime("now");
			
		try {
			$qb->select('j')
			->from('\Entidades\ZgutlJob','j')
			->where($qb->expr()->andX(
				$qb->expr()->eq('j.indAtivo'				, ':ativo'),
				$qb->expr()->lte('j.dataProximaExecucao'	, ':data')
			))
			->setParameter('ativo', 1)
			->setParameter('data', $agora, \Doctrine\DBAL\Types\Type::DATETIME);
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Desabilitar um Job
	 * @param number $codJob
	 */
	public static function desabilitaJob($codJob) {
		global $em,$log;
		
		#################################################################################
		## Verifica se o job existe
		#################################################################################
		$oJob	= $em->getRepository('\Entidades\ZgutlJob')->findOneBy(array('codigo' => $codJob));
		if (!$oJob)	exit;

		try {
			$oJob->setIndAtivo(0);
			$oJob->setIndExecutando(0);
			
			$em->persist($oJob);
			$em->flush();
			$em->detach($oJob);
		
		} catch (\Exception $e) {
			$log->err("Erro ao desabilitar o job: (".$codJob.") ".$e->getMessage());
			die($e->getMessage());
		}
	
	}


}
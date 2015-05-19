<?php

namespace Zage\Adm;

use \Doctrine\DBAL\LockMode;

/**
 * Parâmetros do sistema
 * 
 * @package: Semaforo
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Sequencial {

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
     * Resgata o próximo valor do sequencial
     *
     * @param string $sequence
     * @return integer
     */
    public static function proximoValor ($sequence) {
		global $em,$system;
		
		$em->getConnection()->beginTransaction(); // suspend auto-commit
		try {
			
			switch (strtolower($system->config["database"]["driver"])) {
				case "mysqli":
				case "pdo_mysql":
					$classe	= "\\Entidades\\".$sequence;
					$seq		= new $classe;
					$em->persist($seq);
					$em->flush();
					$em->getConnection()->commit();
					$val	= $seq->getCodigo();
					break;
				default:
					\Zage\App\Erro::halt("Driver: ".$system->config["database"]["driver"]." ainda não implementado !!! (".__FILE__.")");
			}

			return ($val);
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			$em->close();
			throw $e;
		}
    }
    
    /**
     * Resgata o valor atual do sequencial
     *
     * @param string $sequence
     * @return integer
     */
    public static function valorAtual ($sequence) {
		global $em,$system,$log;
		
		$em->getConnection()->beginTransaction(); // suspend auto-commit
		try {
			
			switch (strtolower($system->config["database"]["driver"])) {
				case "mysqli":
				case "pdo_mysql":
					
					$classe	= "Entidades\\".$sequence;
					
					$qb = $em->createQueryBuilder();
					
					$qb->select('max(s.codigo) as valor');
					$qb->from($classe,'s');
					
					$val = $qb->getQuery()->getSingleScalarResult();
					break;
				default:
					\Zage\App\Erro::halt("Driver: ".$system->config["database"]["driver"]." ainda não implementado !!! (".__FILE__.")");
			}

			return ($val);
		} catch (\Exception $e) {
			$log->debug("FILE: ".__FILE__.": ".$e->getMessage());
			$em->getConnection()->rollback();
			$em->close();
			throw $e;
		}
    }
    
}
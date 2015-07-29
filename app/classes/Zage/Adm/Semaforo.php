<?php

namespace Zage\Adm;

use Doctrine\DBAL\LockMode;
//use Doctrine\ORM\PessimisticLockException;

/**
 * Parâmetros do sistema
 * 
 * @package: Semaforo
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Semaforo {

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
     * Imcrementa o valor do semáforo e retorna o valor
     *
     * @param integer $codOrganizacao
     * @param string $semaforo
     * @return integer
     */
    public static function proximoValor ($codOrganizacao, $semaforo) {
		global $em,$system,$log;
		
		$em->getConnection()->beginTransaction(); // suspend auto-commit
		try {

			$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codOrganizacao' => $codOrganizacao,'parametro' => $semaforo),array(),LockMode::PESSIMISTIC_WRITE);
			
			if (!$sem) {
				return (self::criar($codOrganizacao, $semaforo));
			}else{
				$valor		= (int) $sem->getValor() + 1;
			}

			$sem->setValor($valor);
			$em->persist($sem);
			$em->flush();
			$em->getConnection()->commit();
			
			return ($valor);
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			$em->close();
			throw $e;
		}
		
    }
    
    /**
     * Cria um semáforo
     *
     * @param integer $codOrganizacao
     * @param string $semaforo
     * @return integer
     */
    public static function criar ($codOrganizacao, $semaforo) {
    	global $em,$system,$log;
    
    	try {
    		
    		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codOrganizacao' => $codOrganizacao,'parametro' => $semaforo));
    			
    		if (!$sem) {
    			$sem		= new \Entidades\ZgadmSemaforo();
    
    			/** Busca o obj da organização **/
    			$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));

    			if (!$oOrg) {
    				return null;
    			}else{
    				$valor = 1;
    				$sem->setCodOrganizacao($oOrg);
    				$sem->setParametro($semaforo);
    				$sem->setValor($valor);
    				$em->persist($sem);
    				$em->flush();
    				return $valor;
    			}
    		}
    	} catch (\Exception $e) {
    		throw $e;
    	}
    
    }
    
    /**
     * Resgata o valor atual do parâmetro
     * @param unknown $codOrganizacao
     * @param unknown $semaforo
     * @return array
     */
    public static function valorAtual ($codOrganizacao, $semaforo) {
		global $system,$em;
		
		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codOrganizacao' => $codOrganizacao,'parametro' => $semaforo));
		
		if (!$sem) return null;
		
		return ($sem->getValor());
   	
    }

    /**
     * Definir um valor no semáforo
     *
     * @param integer $codOrganizacao
     * @param string $semaforo
     * @param number $valor
     */
    public static function setValor ($codOrganizacao, $semaforo, $valor) {
    	global $em,$system,$log;
    
    	try {
    		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codOrganizacao' => $codOrganizacao,'parametro' => $semaforo));
    		 
    		if (!$sem) {
    			$sem		= new \Entidades\ZgadmSemaforo();
    		}
    
    		/** Busca o obj da Organização **/
    		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
			
			if (!$oOrg) {
    			return null;
			}else{
				$log->debug("Definindo semáforo (".$semaforo."): ".$valor);
				$sem->setCodOrganizacao($oOrg);
				$sem->setParametro($semaforo);
				$sem->setValor($valor);
				$em->persist($sem);
				$em->flush();
			}
    	} catch (\Exception $e) {
    		throw $e;
    	}
    
    }
}
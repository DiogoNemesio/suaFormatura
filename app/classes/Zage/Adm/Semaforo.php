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
     * @param integer $codEmpresa
     * @param string $semaforo
     * @return integer
     */
    public static function proximoValor ($codEmpresa, $semaforo) {
		global $em,$system;
		
		$em->getConnection()->beginTransaction(); // suspend auto-commit
		try {

			$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codEmpresa' => $codEmpresa,'parametro' => $semaforo),array(),LockMode::PESSIMISTIC_WRITE);
			
			if (!$sem) {
				return (self::criar($codEmpresa, $semaforo));
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
     * @param integer $codEmpresa
     * @param string $semaforo
     * @return integer
     */
    public static function criar ($codEmpresa, $semaforo) {
    	global $em,$system;
    
    	try {
    		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codEmpresa' => $codEmpresa,'parametro' => $semaforo));
    			
    		if (!$sem) {
    			$sem		= new \Entidades\ZgadmSemaforo();
    
    			/** Busca o obj da empresa **/
    			$emp		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $codEmpresa));
    
    			if (!$emp) {
    				return null;
    			}else{
    				$valor = 1;
    				$sem->setCodEmpresa($emp);
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
     * Resgata o valor do parâmetro
     *
     * @param varchar $parametro
     * @return array
     */
    public static function valorAtual ($codEmpresa, $semaforo) {
		global $system,$em;
		
		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codEmpresa' => $codEmpresa,'parametro' => $semaforo));
		
		if (!$sem) return null;
		
		return ($sem->getValor());
   	
    }

    /**
     * Definir um valor no semáforo
     *
     * @param integer $codEmpresa
     * @param string $semaforo
     * @param number $valor
     */
    public static function setValor ($codEmpresa, $semaforo, $valor) {
    	global $em,$system,$log;
    
    	try {
    		$log->debug("Semáforo 1");
    		$sem	= $em->getRepository('Entidades\ZgadmSemaforo')->findOneBy(array('codEmpresa' => $codEmpresa,'parametro' => $semaforo));
    		 
    		if (!$sem) {
    			$sem		= new \Entidades\ZgadmSemaforo();
    		}
    
    		/** Busca o obj da empresa **/
    		$emp		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $codEmpresa));
			
			if (!$emp) {
    			return null;
			}else{
				$log->debug("Definindo semáforo (".$semaforo."): ".$valor);
				$sem->setCodEmpresa($emp);
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
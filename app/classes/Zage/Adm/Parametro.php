<?php

namespace Zage\Adm;

/**
 * Parâmetros do sistema
 * 
 * @package: Parametro
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Parametro {

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
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($parametro = null) {
		global $db;
		
    	return (
    		$db->extraiTodos("
				SELECT	P.*
				FROM	ZGADM_PARAMETRO P
				WHERE	P.PARAMETRO LIKE '%".$parametro."%'
				ORDER	BY PARAMETRO
			")
   		);
    }
    

    /**
	 * Salva o valor de um parâmetro
     */
    public function salva($parametro,$valor) {
		global $log,$db;
		$log->debug("Parametro: ".$parametro.' Valor: '.$valor);
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE ZGADM_PARAMETRO P SET P.VALOR = ? WHERE PARAMETRO = ?",
				array($valor,$parametro)
			);
			$db->con->commit();
			return null;
		}catch (\Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
     * Resgata o valor do parâmetro
     *
     * @param varchar $parametro
     * @return array
     */
    public static function getValor ($parametro,$indValorPadrao = false) {
		global $system,$em;
		
		#################################################################################
		## Buscar os parâmetros na seguinte ordem:
		##
		## 1 -> Parâmetro do sistema
		## 2 -> Parâmetro por Organização
		## 3 -> Parâmetro por Usuário
		#################################################################################
		
		
		#################################################################################
		## Busca o parâmetro do sistema
		#################################################################################
		$valor	= self::getValorSistema($parametro,$indValorPadrao);
		
		if ($valor !== false)	return $valor;
   	
		#################################################################################
		## Busca o parâmetro por Organização
		#################################################################################
		$valor	= self::getValorOrganizacao($parametro,$system->getCodOrganizacao(),$indValorPadrao);
		
		if ($valor !== false)	return $valor;
		
		#################################################################################
		## Busca o parâmetro por Usuário
		#################################################################################
		$valor	= self::getValorUsuario($parametro,$system->getCodUsuario(),$indValorPadrao);
		
		return $valor;
		
    }
    
    
    /**
     * Resgata o valor de um parâmetro do sistema
     * @param string $parametro
     */
    public static function getValorSistema ($parametro,$indValorPadrao = true) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro do sistema
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('ps')
    		->from('\Entidades\ZgadmParametroSistema','ps')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'	, ':parametro')
    		))
    		->setParameter('parametro', $parametro);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    			
    	    if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    
    
    /**
     * Resgata o valor de um parâmetro por organização
     * @param string $parametro
     */
    public static function getValorOrganizacao ($parametro,$codOrganizacao,$indValorPadrao = false) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro por organização
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('po')
    		->from('\Entidades\ZgadmParametroOrganizacao','po')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'po.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'		, ':parametro'),
    			$qb->expr()->eq('po.codOrganizacao'	, ':codOrganizacao')
    		))
    		->setParameter('parametro'		, $parametro)
    		->setParameter('codOrganizacao'	, $codOrganizacao);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    		 
			if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    

    /**
     * Resgata o valor de um parâmetro por usuário
     * @param string $parametro
     */
    public static function getValorUsuario ($parametro,$codUsuario,$indValorPadrao = false) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro por Usuário
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('pu')
    		->from('\Entidades\ZgadmParametroUsuario','pu')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'pu.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'		, ':parametro'),
    			$qb->expr()->eq('pu.codUsuario'		, ':codUsuario')
    		))
    		->setParameter('parametro'		, $parametro)
    		->setParameter('codUsuario'		, $codUsuario);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    		 
    	    if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    		 
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    

    /**
     * Resgata o valor padrão de um parâmetro
     * @param string $parametro
     */
    public static function getValorPadrao ($parametro) {
    	global $system,$em;
    
    	try {
	    	$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('parametro' => $parametro));
	    
			if (!$oParametro) {
				return false;
			}else{
				return $oParametro->getValorPadrao();
			}

    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    
    
}
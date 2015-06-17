<?php

namespace Zage\Seg;

/**
 * Menu
 *
 * @package Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Menu {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
    /**
     * Atualiza o dicionário
     */
    public static function atualizaMenu () {
    	global $em;
    	 
    	try {
	    	return null;
    	} catch (\Exception $e) {
			return ($e->getMessage());
		}
    }
    
    
    /**
     * Lista os menus já associados ao perfil
     */
    public static function listaAssociados ($codPerfil,$codMenuPai,$codTipoOrg) {
    	global $em,$system;
    
    	$qb 	= $em->createQueryBuilder();
    	
    	if (!$codMenuPai) {
    		$codMenuPai	= null;
    	}
    	 
    	$qb->select('m')
    	->from('\Entidades\ZgappMenu','m')
    	->leftJoin('\Entidades\ZgappMenuPerfil', 'mp', \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo = mp.codMenu')
    	->where($qb->expr()->andX(
   			$qb->expr()->eq('mp.codPerfil'				, ':codPerfil'),
   			$qb->expr()->eq('mp.codTipoOrganizacao'		, ':codTipoOrg'),
   			$qb->expr()->eq('m.indFixo'					, '0')
    	))
    	
    	->addOrderBy('m.nivel', 'ASC')
    	->addOrderBy('m.codMenuPai', 'ASC')
    	->addOrderBy('mp.ordem', 'ASC')
    	
    	->setParameter('codPerfil'	, $codPerfil)
    	->setParameter('codTipoOrg', $codTipoOrg);
    	
    	if (!$codMenuPai) {
    		$qb->andWhere(
    			$qb->expr()->isNull('m.codMenuPai')
    		);
    	}else{
    		$qb->andWhere('m.codMenuPai = :codMenuPai');
    		$qb->setParameter('codMenuPai', $codMenuPai);
    	}
    
    	$query 		= $qb->getQuery();
    	return($query->getResult());
    }
    
    /**
     * Lista os menus disponíveis (não associados ao perfil)
     */
    public static function listaDisponiveis ($codPerfil,$codMenuPai,$codTipoOrg) {
    	global $em,$system,$log;
    
    	$qb 	= $em->createQueryBuilder();
    	$qb2 	= $em->createQueryBuilder();
    	
    	if (!$codMenuPai) {
    		$codMenuPai	= null;
    	}
    	 
    	
    	/** Sub Query para retirar os menus já associados **/
    	$qb2->select('m2.codigo')
    		->from('\Entidades\ZgappMenuPerfil','mp')
    		->leftJoin('\Entidades\ZgappMenu', 'm2', \Doctrine\ORM\Query\Expr\Join::WITH, 'mp.codMenu = m2.codigo')
    		->where($qb2->expr()->eq('mp.codPerfil',':codPerfil'));
    	$qb2->andWhere($qb2->expr()->eq('mp.codTipoOrganizacao',':codTipoOrg'));
    	
    	$qb->select('m')
    	->from('\Entidades\ZgappMenu','m')
    	->where($qb->expr()->andX(
   			$qb->expr()->eq('m.indFixo'			, '0'),
   			$qb->expr()->notIn('m.codigo', $qb2->getDQL())
    	))
    	->orderBy('m.nome', 'ASC')
    	->setParameter('codPerfil'	, $codPerfil)
    	->setParameter('codTipoOrg', $codTipoOrg);
    	 
    	if (!$codMenuPai) {
    		$qb->andWhere('m.codMenuPai is null');
    	}else{
    		$qb->andWhere('m.codMenuPai = :codMenuPai');
    		$qb->setParameter('codMenuPai', $codMenuPai);
    	}
    	
    	$query 		= $qb->getQuery();
    	
    	return($query->getResult());
    
    }
    
    
    /**
     * Verifica se o menu está associado ao perfil
     * @param integer $codMenu
     * @param integer $codPerfil
     * @return boolean
     */
    public function estaAssociado($codMenu,$codPerfil,$codTipoOrg) {
    	global $em,$system;
    	 
    	$qb 	= $em->createQueryBuilder();
    
    	$qb->select($qb->expr()->count('m.codigo'))
    	->from('\Entidades\ZgappMenu','m')
    	->leftJoin('\Entidades\ZgappMenuPerfil'			,'mp'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo 		= mp.codMenu')
    	->where($qb->expr()->andX(
   			$qb->expr()->eq('mp.codPerfil'			, ':codPerfil'),
   			$qb->expr()->eq('mp.codTipoOrganizacao'	, ':codTipoOrg'),
   			$qb->expr()->eq('mp.codMenu'			, ':codMenu')
    	))
    	->setParameter('codPerfil'		, $codPerfil)
    	->setParameter('codTipoOrg'		, $codTipoOrg)
    	->setParameter('codMenu'		, $codMenu);
    
    	$query 	= $qb->getQuery();
    	$return = $query->getSingleScalarResult();
    
    	if ($return > 0) return true;
    	 
    	return false;
    }
    
    /**
     * Verifica se o menu está associado ao perfil
     * @param integer $codMenu
     * @param integer $codPerfil
     * @return boolean
     */
    public function desassociaFilhos($codMenu,$codPerfil,$codTipoOrg) {
    	global $em,$system;
    	
    	
    	/** Verifica se o menu está associado **/
    	$oMenu	= $em->getRepository('Entidades\ZgappMenuPerfil')->findOneBy(array('codMenu' => $codMenu, 'codPerfil' => $codPerfil,'codTipoOrganizacao' => $codTipoOrg));

    	if ($oMenu) {
    		try {
    			$em->remove($oMenu);
    			$em->flush();
    		} catch (\Exception $e) {
    			return "Não foi possível desassociar o menu: ".$menus[$i]->getNome()." Erro: ".$e->getMessage();
    		}
    		
    		$filhos		= $em->getRepository('Entidades\ZgappMenu')->findBy(array('codMenuPai' => $codMenu));

    		for ($i = 0; $i < sizeof($filhos); $i++) {
    			$erro = self::desassociaFilhos($filhos[$i]->getCodigo(), $codPerfil,$codTipoOrg);
    			if ($erro) return $erro;
    		}
    	}else{
    		return false;
    	}
    }
    
    /**
     * Excluir um Menu
     */
    public static function exclui ($codMenu) {
    	global $em,$system,$log,$tr;
    
    	#################################################################################
    	## Verificar se o Menu existe e excluir
    	#################################################################################
    	$em->getConnection()->beginTransaction();
    	try {
    
    		
    		if (!isset($codMenu) || (!$codMenu)) {
    			return ($tr->trans('Falta de Parâmetros'));
			}
    
    		$oMenu	= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));
			    
    		if (!$oMenu) {
    			return ($tr->trans('Menu não encontrado'));
			}
    
			/** Remoção em cascata **/
    								
			/** Histórico de acesso **/
			$qb 	= $em->createQueryBuilder();
			$qb->delete('Entidades\ZgappMenuHistAcesso', 'h');
			$qb->andWhere($qb->expr()->eq('h.codMenu', ':codMenu'));
			$qb->setParameter(':codMenu', $codMenu);
			$numDeleted = $qb->getQuery()->execute();
    
			$em->remove($oMenu);
			$em->flush();
			$em->getConnection()->commit();
    								
			return null;
    
    	} catch (\Exception $e) {
    		$em->getConnection()->rollback();
    		return $e->getMessage();
    	}
    }
    
    
    
}

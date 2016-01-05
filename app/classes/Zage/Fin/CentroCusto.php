<?php

namespace Zage\Fin;


/**
 * Centro de Custo
 *
 * @package CentroCusto
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class CentroCusto extends \Entidades\ZgfinCentroCusto {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
    
    /**
     * Lista Categorias para montar a combo
     */
    public static function listaCombo ($codOrganizacao,$indDebito = false, $indCredito = false,$listaApenasAtivos	= true) {
    	global $em,$system;
    
    	#################################################################################
    	## Resgata as informações da Organização
    	#################################################################################
    	$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
    	$codTipoOrg		= $oOrg->getCodTipo()->getCodigo();
    	 
    	
    	$qb 	= $em->createQueryBuilder();
    	 
    	try {
    		$qb->select('cc')
    		->from('\Entidades\ZgfinCentroCusto','cc')
    		->where($qb->expr()->orX(
    			$qb->expr()->andX(
    				$qb->expr()->eq('cc.codOrganizacao'	, ':codOrganizacao')
    			),
    			$qb->expr()->andX(
    				$qb->expr()->isNull('cc.codOrganizacao'),
    				$qb->expr()->eq('cc.codTipoOrganizacao'	, ':codTipoOrg')
    			),
    			$qb->expr()->andX(
    				$qb->expr()->isNull('cc.codOrganizacao'),
    				$qb->expr()->isNull('cc.codTipoOrganizacao')
    			)
    		))
    		->orderBy('cc.descricao', 'ASC')
    		->setParameter('codOrganizacao'	, $codOrganizacao)
    		->setParameter('codTipoOrg'		, $codTipoOrg);

    		if ($listaApenasAtivos === true) {
    			$qb->andWhere($qb->expr()->eq('cc.indAtivo'	, '1'));
    		}
    		
    		if ($indCredito			=== true) {
    			$qb->andWhere($qb->expr()->eq('cc.indCredito'	, '1'));
    		}
    		
    		if ($indDebito			=== true) {
    			$qb->andWhere($qb->expr()->eq('cc.indDebito'	, '1'));
    		}
    		
    		$query 		= $qb->getQuery();
    		return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
    
}

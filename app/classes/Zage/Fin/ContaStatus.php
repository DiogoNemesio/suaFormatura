<?php

namespace Zage\Fin;

/**
 * Gerenciar status de conta
 * 
 * @package: ContaStatus
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaStatus {

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
	 * Montar um array com as possíveis ações por status
	 */
	public static function getArrayStatusAcao () {
		global $em;
		
		#################################################################################
		## Resgatar os registros na tabela
		#################################################################################
		$oStatusAcao	= $em->getRepository('Entidades\ZgfinContaStatusAcao')->findBy(array()); 

		#################################################################################
		## Montar um array que facilite a pesquisa, indexado pelo status
		#################################################################################
		$return		= array();
		for ($i = 0; $i < sizeof($oStatusAcao); $i++) {
			$return[$oStatusAcao[$i]->getCodStatus()->getCodigo()][$oStatusAcao[$i]->getCodAcao()->getCodigo()]		= 1;
		}
		
		return $return;
	}

}
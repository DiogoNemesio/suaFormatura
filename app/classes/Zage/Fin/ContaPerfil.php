<?php

namespace Zage\Fin;

/**
 * Gerenciar Perfis de conta
 * 
 * @package: ContaPerfil
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaPerfil {

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
	public static function getArrayPerfilAcao () {
		global $em;
		
		#################################################################################
		## Resgatar os registros na tabela
		#################################################################################
		$oPerfilAcao	= $em->getRepository('Entidades\ZgfinContaPerfilAcao')->findBy(array()); 

		#################################################################################
		## Montar um array que facilite a pesquisa, indexado pelo status
		#################################################################################
		$return		= array();
		for ($i = 0; $i < sizeof($oPerfilAcao); $i++) {
			$return[$oPerfilAcao[$i]->getCodContaPerfil()->getCodigo()][$oPerfilAcao[$i]->getCodAcao()->getCodigo()]		= 1;
		}
		
		return $return;
	}

}
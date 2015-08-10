<?php

namespace Zage\Adm;

/**
 * Gerenciar os contratos
 * 
 * @package: Contrato
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Contrato {

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
	 * Calcular o valor final do contrato, já aplicado os descontos
	 * @param int $codOrganizacao
	 */
	public static function getValor ($codOrganizacao) {
		global $em,$system;
		
		#################################################################################
		## Resgata as informações do contrato
		#################################################################################
		$contrato		= $em->getRepository('\Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $codOrganizacao));
		if (!$contrato)	throw new \Exception($tr->trans("Contrato não localizado para a organização: ".$codOrganizacao));
		
		#################################################################################
		## Calcula o valor
		#################################################################################
		
		
		
		try {
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
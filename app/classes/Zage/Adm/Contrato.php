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
	 * Calcular o valor por licença do contrato, já aplicado os descontos
	 * @param int $codOrganizacao
	 */
	public static function getValorLicenca ($codOrganizacao) {
		global $em,$system;
		
		#################################################################################
		## Resgata as informações do contrato
		#################################################################################
		$contrato		= $em->getRepository('\Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $codOrganizacao));
		if (!$contrato)	throw new \Exception($tr->trans("Contrato não localizado para a organização: ".$codOrganizacao));
		
		#################################################################################
		## Resgata o valor do Plano
		#################################################################################
		//$planoValor		= $em->getRepository('\Entidades\ZgadmPlanoValor')->findOneBy(array('codPlano' => $contrato->getCodPlano()->getCodigo()),array('dataBase' => 'DESC'));
		//if (!$planoValor)	throw new \Exception($tr->trans("Configurações do plano não localizadas para o plano ".$contrato->getCodPlano()->getCodigo()));
		
		#################################################################################
		## Calcula o valor
		#################################################################################
		$valorDoPlano		= $contrato->getValorPlano();
		$valorDesconto		= $contrato->getValorDesconto();
		$pctDesconto		= $contrato->getPctDesconto();
		
		if ($pctDesconto)	{
			return ($valorDoPlano * (100 - $pctDesconto) / 100);
		}
		
		if ($valorDesconto)	{
			return ($valorDoPlano - $valorDesconto);
		}
		
		return ($valorDoPlano);
	}

}
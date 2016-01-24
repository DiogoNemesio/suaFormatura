<?php

namespace Zage\Fmt;

/**
 * Gerenciar os contratos da formatura
 * 
 * @package: Contrato
 * @Author: Daniel Cassela
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
	 * Calcular o valor do contrato
	 * @param unknown $codContrato
	 */
	public static function getValor($codContrato) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em;
	
		#################################################################################
		## O Valor do contrato é a soma dos valores das suas parcelas
		#################################################################################
		$oContrato			= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codigo' => $codContrato));
		if (!$oContrato)	return null;
		

		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
		
		try {
				
			#################################################################################
			## Somatório das parcelas
			#################################################################################
			$qb1->select('SUM(p.valor) as total')
			->from('\Entidades\ZgfmtContratoFormandoParcela','p')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('p.codContrato'	, ':codContrato')
			))
			->setParameter('codContrato'	, $codContrato);
		
			$query 				= $qb1->getQuery();
			$valor				= round(\Zage\App\Util::to_float($query->getSingleScalarResult()),2);
			
			return ($valor);
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
	
}

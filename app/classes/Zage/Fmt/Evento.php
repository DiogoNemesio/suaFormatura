<?php

namespace Zage\Fmt;

/**
 * Gerenciar os eventos da formatura
 * 
 * @package: Evento
 * @Author: Daniel Cassela
 * @version: 1.0.1
 * 
 */

class Evento {

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
	 * Calcular o valor do evento
	 * @param unknown $codEvento
	 */
	public static function getValor($codEvento) {
		global $em,$system;
	
		
		#################################################################################
		## O Valor do evento pode ser um valor fixo cadastrado na tabela,
		## ou um percentual do orçamento aceito.
		#################################################################################
		$evento				= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
		if (!$evento)		return null;
		
		#################################################################################
		## Se o campo valor estiver definido retornar ele
		#################################################################################
		$valor				= $evento->getValorAvulso();
		if ($valor)			return \Zage\App\Util::to_float($valor);
		
		#################################################################################
		## Caso contrário, calcular o pct do orçamento aceite
		#################################################################################
		$pct				= \Zage\App\Util::to_float($evento->getPctValorOrcamento());
		if (!$pct)			return 0;
		
		#################################################################################
		## Resgatar o valor por formando
		#################################################################################
		$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $evento->getCodFormatura()->getCodigo()));
		
		if (!$oOrgFmt) return 0;
		
		if ($oOrgFmt->getValorPrevistoTotal() && $oOrgFmt->getQtdePrevistaFormandos()){
			$valorFormatura = \Zage\App\Util::to_float((\Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal())/\Zage\App\Util::to_float($oOrgFmt->getQtdePrevistaFormandos())));
		}else{
			$valorFormatura	= 0;
		}
		
		return round($valorFormatura * $pct / 100,2);
		
	}
	
}

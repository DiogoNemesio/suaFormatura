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
		$orcAceite			= \Zage\Fmt\Orcamento::getVersaoAceita($evento->getCodFormatura()->getCodigo());
		if (!$orcAceite)	return 0;
		$valorOrc			= \Zage\App\Util::to_float(\Zage\Fmt\Orcamento::calculaValorTotal($orcAceite->getCodigo()));
		if (!$valorOrc)		return 0;
		
		return ($valorOrc * $pct / 100);
		
	}
	
}

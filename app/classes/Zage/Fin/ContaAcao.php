<?php

namespace Zage\Fin;

/**
 * Gerenciar Ações de conta
 * 
 * @package: ContaAcao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaAcao {

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
	 * Montar um array com as possíveis ações, de acordo com o perfil e o status
	 * @param integer $codPerfil
	 * @param string $codStatus
	 * @param array $aStatusAcao
	 * @param array $aPerfilAcao
	 */
	public static function getArrayAcoes ($codPerfil,$codStatus, $aStatusAcao = null, $aPerfilAcao = null) {
		global $em;
		
		#################################################################################
		## Caso não seja informado o perfil, usar o perfil padrão "0" 
		#################################################################################
		if (!$codPerfil)	$codPerfil	= 0;
		
		#################################################################################
		## Resgatar as ações que podem ser feitas por status
		#################################################################################
		if (!is_array($aStatusAcao))	$aStatusAcao	= \Zage\Fin\ContaStatus::getArrayStatusAcao();
		
		#################################################################################
		## Resgatar as ações que podem ser feitas por Perfil de conta
		#################################################################################
		if (!is_array($aPerfilAcao))	$aPerfilAcao	= \Zage\Fin\ContaPerfil::getArrayPerfilAcao();
		
		#################################################################################
		## Resgatar as ações no banco
		#################################################################################
		$oAcoes			= $em->getRepository('Entidades\ZgfinContaAcaoTipo')->findBy(array());
		
		#################################################################################
		## Montar o array de retorno indexado por ação 
		#################################################################################
		$return		= array();
		for ($i = 0; $i < sizeof($oAcoes); $i++) {
			$codAcao			= $oAcoes[$i]->getCodigo();
			$pode				= ( (isset($aStatusAcao[$codStatus][$codAcao])) && (isset($aPerfilAcao[$codPerfil][$codAcao])) ) ? 1 : 0; 
			$return[$codAcao]	= $pode;
		}
		
		return $return;
	}
	
	/**
	 * Verificar se a ação informada é permitida para o Perfil / Status
	 * @param integer $codPerfil
	 * @param string $codStatus
	 * @param string $codAcao
	 */
	public static function verificaAcaoPermitida($codPerfil,$codStatus,$codAcao) {


		#################################################################################
		## Ajustar o codPerfil para 0, caso não seja informado
		#################################################################################
		$codPerfil	= (!isset($codPerfil) || !$codPerfil) ? 0 : $codPerfil;
		
		#################################################################################
		## Resgatar as ações que podem ser feitas por status
		#################################################################################
		$aStatusAcao	= \Zage\Fin\ContaStatus::getArrayStatusAcao();
		
		#################################################################################
		## Resgatar as ações que podem ser feitas por Perfil de conta
		#################################################################################
		$aPerfilAcao	= \Zage\Fin\ContaPerfil::getArrayPerfilAcao();
		
		#################################################################################
		## Fazer a verificação da seguinte forma:
		## 
		## Para a ação ser permitida, precisa existir no array de Status 
		## e no array de perfil
		##
		#################################################################################
		$pode				= ( (isset($aStatusAcao[$codStatus][$codAcao])) && (isset($aPerfilAcao[$codPerfil][$codAcao])) ) ? 1 : 0;
		return ($pode);
		
	}

}
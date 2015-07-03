<?php

namespace Zage\Fin;

/**
 * Gerenciar as Datas financeiras
 * 
 * @package: Data
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Data {

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
	 * Verificar se uma determinada data é dia útil
	 * @param number $filial
	 * @param date $data
	 */
	public static function ehDiaUtil($data,$filial = null) {
		global $em,$system;
		
		$dateObj		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
		$diaSemana		= $dateObj->format("N");  
		$diaMes			= $dateObj->format("d");
		$mes			= $dateObj->format("m");
		
		//echo "Dia da Semana: ".$diaSemana."<BR>";
		
		/** Checando se é sábado ou domingo **/
		if ($diaSemana == 6 || $diaSemana == 7) return false;
		
		
		/** Checando se a data está cadastrada como feriado nacional Fixo **/
		try {
			$feriadoNacional = $em->getRepository ( 'Entidades\ZgfinFeriadoNacional' )->findOneBy (array ('codOrganizacao' => $system->getCodOrganizacao(), 'dia' => $diaMes,'mes' => $mes));
		} catch ( \Exception $e ) {
			\Zage\App\Erro::halt ( $e->getMessage () );
		}
		
		if ($feriadoNacional) {
			return false;
		}
		
		/** Checando se a data está cadastrada como feriado nacional Variável **/
		try {
			$feriadoNacional = $em->getRepository ( 'Entidades\ZgfinFeriadoNacionalVariavel' )->findOneBy (array ('codOrganizacao' => $system->getCodOrganizacao(), 'data' => $dateObj));
		} catch ( \Exception $e ) {
			\Zage\App\Erro::halt ( $e->getMessage () );
		}
		
		if ($feriadoNacional) {
			return false;
		}
		
		/** Se a filial não for informada retornar **/
		if (!$filial) return true;
		
		/** Checando se é feriado fixo na filial informada **/
		try {
			$feriadoFilial = $em->getRepository ( 'Entidades\ZgfinFeriadoFilial' )->findOneBy (array ('codFilial' => $filial,'dia' => $diaMes,'mes' => $mes));
		} catch ( \Exception $e ) {
			\Zage\App\Erro::halt ( $e->getMessage () );
		}
		
		if ($feriadoFilial) {
			return false;
		}
		
		/** Checando se é feriado Variável na filial informada **/
		try {
			$feriadoFilial = $em->getRepository ( 'Entidades\ZgfinFeriadoFilialVariavel' )->findOneBy (array ('codFilial' => $filial,'data' => $dateObj));
		} catch ( \Exception $e ) {
			\Zage\App\Erro::halt ( $e->getMessage () );
		}

		if ($feriadoFilial) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Resgata o próximo dia útil, a partir de uma data base
	 * @param date $data
	 * @param number $filial
	 */
	public static function proximoDiaUtil($data,$filial = null) {
		global $system;
		$dateObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
		$dateObj->modify('+1 day');
		$diaUtil	= $dateObj->format($system->config["data"]["dateFormat"]);
		$maxLoop	= 30;
		$i			= 0;
		while (self::ehDiaUtil($diaUtil,$filial) == false) {
			if ($i > $maxLoop) die('Número máximo de loops alcançada na função: '.__FUNCTION__);			
			$dateObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $diaUtil);
			$dateObj->modify('+1 day');
			$diaUtil	= $dateObj->format($system->config["data"]["dateFormat"]); 
			
			$i++;
		}
		
		return($diaUtil);
	}
	
	/**
	 * Resgata o dia útil anterior a uma data base
	 * @param date $data
	 * @param number $filial
	 */
	public static function diaUtilAnterior($data,$filial = null) {
		global $system;
		$dateObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
		$dateObj->modify('-1 day');
		$diaUtil	= $dateObj->format($system->config["data"]["dateFormat"]);
		$maxLoop	= 30;
		$i			= 0;
		while (self::ehDiaUtil($diaUtil,$filial) == false) {
			if ($i > $maxLoop) die('Número máximo de loops alcançada na função: '.__FUNCTION__);			
			$dateObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $diaUtil);
			$dateObj->modify('-1 day');
			$diaUtil	= $dateObj->format($system->config["data"]["dateFormat"]); 
			
			$i++;
		}
		
		return($diaUtil);
	}
	
	/**
	 * Soma a quantidade de dias úteis a uma data
	 * @param date $data
	 * @param number $dias
	 * @param number $filial
	 */
	public static function somaDiasUteis($data,$dias,$filial = null) {
		global $system;
		
		/**
		 * Verifica se é para calcular dias úteis posterior ou anterior
		 */
		if ($dias == 0) {
			return $data;
		}elseif ($dias > 0) {
			$numDias	= $dias;
			$somador	= 1;
		}else{
			$numDias	= $dias * -1;
			$somador	= -1;
		}
		
		$i			= 0;
		$dia		= $data;
		$maxLoop	= 300;
		$n			= 0;
		
		while ($i < $numDias) {
			if ($n > $maxLoop)	die('Número máximo de loops alcançada na função: '.__FUNCTION__);
			$dateObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dia);
			$dateObj->modify($somador.' day');
			$dia		= $dateObj->format($system->config["data"]["dateFormat"]);
			
			if (self::ehDiaUtil($dia,$filial) == true) {
				$i++;
			}
			$n++;
		}
		
		if (self::ehDiaUtil($dia,$filial) == false) {
			return self::proximoDiaUtil($dia,$filial);
		}else{
			return ($dia);
		}
	}

	/**
	 * Calcula a quantidade de dias entre o próximo dia útil de uma dataBase e hoje
	 * @param date $data
	 * @param number $filial
	 */
	public static function numDiasAtraso($vencimento,$filial = null,$dataReferencia = null) {
		global $system;
	
		
		/**
		 * Criar o objeto Datetime de hoje
		 */
		if ($dataReferencia	== null) {
			//$hojeObj		= \DateTime::createFromFormat($system->config["data"]["dateFormat"].' H:i:s',date($system->config["data"]["dateFormat"].' H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))));
			$hojeObj		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], date($system->config["data"]["dateFormat"]));
		}else{
			$hojeObj		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataReferencia);
		}
		
		/**
		 * Verifica se a data base é um dia útil, senão busca o próximo dia útil
		 */
		if (self::ehDiaUtil($vencimento,$filial) == true) {
			$dataBase		= $vencimento;
		}else{
			$dataBase		= self::proximoDiaUtil($vencimento,$filial);
		}
		
		/**
		 * Criar o objeto Datetime da dataBase 
		 */
		$dateBaseObj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataBase);
		

		/**
		 * Verificar se a database não é dia útil, para não contar os dias até o proximo dia útil, 
		 * caso a database esteja entre o vencimento e o proximo dia útil  
		 */
		if ($vencimento != $dataBase) {
			$dateObj		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $vencimento);
			
			
			if ($hojeObj <= $dateBaseObj) {
				return 0; 
			}else{
				$interval 		= date_diff($dateObj, $hojeObj);
			}
			
		}else{
			$interval 		= date_diff($dateBaseObj, $hojeObj);
		}
		
		
		$numDias		= ((int) $interval->format('%R%a'));
		
		return ($numDias);
	
	}
	
}
<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$log;

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_POST['dataRef']))	$dataRef	= \Zage\App\Util::antiInjection($_POST["dataRef"]);
if (isset($_POST['dataIni']))	$dataIni	= \Zage\App\Util::antiInjection($_POST["dataIni"]);

#################################################################################
## Data Inicial deve ser hoje caso o parâmetro não seja informado
#################################################################################
if (!isset($dataIni))			$dataIni		= date($system->config["data"]["dateFormat"]);


#################################################################################
## Cria o array de retorno
#################################################################################
$array				= array();
$array["MESES"]		= 0;

#################################################################################
## Validar o parâmetro
#################################################################################
if (!isset($dataRef) || \Zage\App\Util::validaData($dataRef, $system->config["data"]["dateFormat"]) == false) {
	_getNumMesesAteDataReturn();
}else if (\Zage\App\Util::validaData($dataIni, $system->config["data"]["dateFormat"]) == false) {
	_getNumMesesAteDataReturn();
}else{
	$oDataIni		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataIni); 
	$oDataRec		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataRef);
}

#################################################################################
## Calcula a diferença de meses
#################################################################################
//$hoje					= new DateTime('now');
if ($oDataIni > $oDataRec) {
	$array["MESES"]		= 0;
}else{
	$interval			= $oDataRec->diff($oDataIni);
	$array["MESES"]		= (($interval->format('%y') * 12) + $interval->format('%m'));
}


_getNumMesesAteDataReturn();

function _getNumMesesAteDataReturn() {
	global $array;
	echo json_encode($array);
	exit;
}
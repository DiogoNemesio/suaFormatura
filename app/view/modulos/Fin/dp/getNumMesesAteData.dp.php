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
}else{
	$oDataRec		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataRef); 
}

#################################################################################
## Calcula a diferença de meses
#################################################################################
$hoje					= new DateTime('now');
if ($hoje > $oDataRec) {
	$array["MESES"]		= 0;
}else{
	$interval			= $oDataRec->diff($hoje);
	$array["MESES"]		= (($interval->format('%y') * 12) + $interval->format('%m'));
}


_getNumMesesAteDataReturn();

function _getNumMesesAteDataReturn() {
	global $array;
	echo json_encode($array);
	exit;
}
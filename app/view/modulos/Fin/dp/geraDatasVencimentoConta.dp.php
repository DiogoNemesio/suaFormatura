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
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['dataVenc']))		$dataVenc			= \Zage\App\Util::antiInjection($_GET["dataVenc"]);
if (isset($_GET['codPeriodoRec']))	$codPeriodoRec		= \Zage\App\Util::antiInjection($_GET["codPeriodoRec"]);
if (isset($_GET['intervaloRec']))	$intervaloRec		= \Zage\App\Util::antiInjection($_GET["intervaloRec"]);
if (isset($_GET['numParcelas']))	$numParcelas		= \Zage\App\Util::antiInjection($_GET["numParcelas"]);

$array		= array();

if (!isset($dataVenc)) {
	echo json_encode($array);
	exit;
}

if (!isset($numParcelas) || (!$numParcelas)) {
	echo json_encode($array);
	exit;
}

if (!\Zage\App\Util::ehNumero($numParcelas)) {
	echo json_encode($array);
	exit;
}

if (($numParcelas > 1) && ( (!isset($intervaloRec)) || (!isset($codPeriodoRec)) )  ) {
	echo json_encode($array);
	exit;
}

if (\Zage\App\Util::validaData($dataVenc, $system->config["data"]["dateFormat"]) == false) {
	echo json_encode($array);
	exit;
}

list ($dia, $mes, $ano) = split ('[/.-]', $dataVenc);

for ($i = 0; $i < $numParcelas; $i++) {
	if ($codPeriodoRec	== "D") {
		$date	= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes, $dia + ($i * $intervaloRec) , $ano));
	}elseif ($codPeriodoRec	== "S") {
		$date	= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes, $dia + ($i * $intervaloRec * 7) , $ano));
	}elseif ($codPeriodoRec	== "M") {
		$date	= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes + ($i * $intervaloRec), $dia , $ano));
	}elseif ($codPeriodoRec	== "A") {
		$date	= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes, $dia , $ano + ($i * $intervaloRec)));
	}else{
		echo json_encode(array());
		exit;
	}
	
	$array[$i]["dataVenc"]	= $date;
}

echo json_encode($array);



<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codConta']))	$codConta			= \Zage\App\Util::antiInjection($_GET["codConta"]);
if (isset($_GET['dia']))		$dia				= \Zage\App\Util::antiInjection($_GET["dia"]);

$array				= array();
$array["saldo"]		= null;


if (!isset($codConta) || empty($codConta)) {
	echo json_encode($array);
	exit;
}

if (isset($dia)) {
	$saldo			= \Zage\Fin\Conta::getSaldoDia($codConta,$dia);
}else{
	$saldo			= \Zage\Fin\Conta::getSaldo($codConta);
}



if ($saldo) {
	$array["saldo"]		= $saldo["saldo"];
}

echo json_encode($array);
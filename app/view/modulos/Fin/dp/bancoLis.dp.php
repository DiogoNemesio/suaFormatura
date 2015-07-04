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
if (isset($_GET['q']))			$q			= \Zage\App\Util::antiInjection($_GET["q"]);

$bancos		= \Zage\Fin\Banco::busca($q);
$array		= array();
$numItens	= \Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS');


for ($i = 0; $i < sizeof($bancos); $i++) {
	
	$array[$i]["id"]		= $bancos[$i]->getCodigo();
	$array[$i]["name"]		= $bancos[$i]->getCodBanco() . ' / '.$bancos[$i]->getNome();
	
	if ($i > $numItens) break;
}

echo json_encode($array);
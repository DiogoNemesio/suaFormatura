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

$subgrupos		= \Zage\Est\Subgrupo::busca($q);
$array			= array();

for ($i = 0; $i < sizeof($subgrupos); $i++) {

	$array[$i]["id"]		= $subgrupos[$i]->getCodigo();
	$array[$i]["name"]		= $subgrupos[$i]->getDescricao();
}

//echo json_encode($arr);
echo json_encode($array);
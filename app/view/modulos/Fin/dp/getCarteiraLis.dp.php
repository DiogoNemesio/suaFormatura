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
if (isset($_GET['q']))				$q				= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codAgencia']))		$codAgencia		= \Zage\App\Util::antiInjection($_GET["codAgencia"]);
if (isset($_GET['codCarteira']))	$codCarteira	= \Zage\App\Util::antiInjection($_GET["codCarteira"]);


if (isset($codCarteira)) {
	$carteiras		= $em->getRepository('Entidades\ZgfinCarteira')->findBy(array('codigo' => $codCarteira));
}else{
	$carteiras		= \Zage\Fin\Banco::buscaCarteirasPorAgencia($codAgencia,$q);
}

$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS');
for ($i = 0; $i < sizeof($carteiras); $i++) {
	
	$array[$i]["id"]		= $carteiras[$i]->getCodigo();
	$array[$i]["text"]		= $carteiras[$i]->getCodCarteira();
}

echo json_encode($array);
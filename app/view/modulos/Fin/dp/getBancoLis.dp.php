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
if (isset($_GET['q']))			$q			= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codBanco']))	$codBanco	= \Zage\App\Util::antiInjection($_GET["codBanco"]);


if (isset($codBanco)) {
	$bancos		= $em->getRepository('Entidades\ZgfinBanco')->findBy(array('codigo' => $codBanco));
}else{
	$bancos		= \Zage\Fin\Banco::busca($q);
}

$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS');
for ($i = 0; $i < sizeof($bancos); $i++) {
	
	$array[$i]["id"]		= $bancos[$i]->getCodigo();
	$array[$i]["text"]		= $bancos[$i]->getCodBanco() . ' / '.$bancos[$i]->getNome();
	$array[$i]["codBanco"]	= $bancos[$i]->getCodBanco();
	
	//if ($i > $numItens) break;
}

echo json_encode($array);
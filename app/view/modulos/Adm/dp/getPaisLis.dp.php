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
if (isset($_GET['q']))					$q			= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codPais']))			$codPais	= \Zage\App\Util::antiInjection($_GET["codPais"]);

if (isset($codPais)) {
	$paises		= $em->getRepository('Entidades\ZgadmPais')->findBy(array('codigo' => $codPais));
}else{
	$paises		= \Zage\Adm\Pais::busca($q);
}


$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($paises); $i++) {
	$array[$i]["id"]		= $paises[$i]->getCodigo();
	$array[$i]["text"]		= $paises[$i]->getSigla() . ' / '.$paises[$i]->getNome();
	//if ($i > $numItens) break;
}

echo json_encode($array);
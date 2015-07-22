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
if (isset($_GET['q']))				$q			    = \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codOrg']))			$codOrg	= \Zage\App\Util::antiInjection($_GET["codOrg"]);

if (isset($codOrg)) {
	$org		= $em->getRepository('Entidades\ZgadmOrganizacao')->findBy(array('codigo' => $codOrg));
}else{
	$org		= \Zage\Adm\Organizacao::buscaOrganizacaoParceiro($q);
}


$array		= array();

for ($i = 0; $i < sizeof($org); $i++) {
	$array[$i]["id"]		= $org[$i]->getCodigo();
	$array[$i]["text"]		= $org[$i]->getNome();
}

echo json_encode($array);
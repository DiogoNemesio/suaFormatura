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
if (isset($_GET['q']))				$q		    = \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codOrg']))			$codOrg		= \Zage\App\Util::antiInjection($_GET["codOrg"]);

if (isset($codOrg)) {
	$org		= $em->getRepository('Entidades\ZgadmOrganizacao')->findBy(array('codigo' => $codOrg));
}else{
	$org		= \Zage\Adm\Organizacao::buscaOrganizacaoParceiro($q);
}

$array		= array();

for ($i = 0; $i < sizeof($org); $i++) {
	$array[$i]["id"]			= $org[$i]->getCodigo();
	$array[$i]["name"]			= $org[$i]->getNome();
	
	$array[$i]["cep"]			= $org[$i]->getCep();
	$array[$i]["endereco"]		= $org[$i]->getEndereco();
	$array[$i]["bairro"]		= $org[$i]->getBairro();
	$array[$i]["numero"]		= $org[$i]->getNumero();
	$array[$i]["complemento"]	= $org[$i]->getComplemento();
}

echo json_encode($array);
<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . '/includeNoAuth.php');
}else{
	include_once(DOC_ROOT . '/includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codRifa']))			$codRifa			= \Zage\App\Util::antiInjection($_GET["codRifa"]);

$array				= array();

if (!$codRifa) {
	
	$array["codRifa"]			= null;
	$array["rifaNome"]			= null;
	$array["vencedorNome"]		= null;
	$array["vencedorEmail"]		= null;
	$array["vencedorTelefone"]	= null;
	$array["vencedorNumero"]	= null;
	$array["vencedorData"]		= null;
	$array["vendedorNome"]		= null;
	$array["vendedorUsuario"]	= null;
	
	echo json_encode($array);
	exit;
}

$ret = $em->getRepository('Entidades\ZgfmtRifa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codRifa));

if ($ret && (sizeof($ret) > 0)) {
	
	$array["codRifa"]			= $ret[0]->getCodigo();
	$array["rifaNome"]			= $ret[0]->getNome();
	$array["vencedorNome"]		= $ret[0]->getNumeroVencedor()->getNome();
	$array["vencedorEmail"]		= $ret[0]->getNumeroVencedor()->getEmail();
	$array["vencedorTelefone"]	= $ret[0]->getNumeroVencedor()->getTelefone();
	$array["vencedorNumero"]	= $ret[0]->getNumeroVencedor()->getNumero();
	$array["vencedorData"]		= $ret[0]->getNumeroVencedor()->getData()->format($system->config["data"]["datetimeSimplesFormat"]);
	$array["vendedorNome"]		= $ret[0]->getNumeroVencedor()->getCodFormando()->getNome();
	$array["vendedorUsuario"]	= $ret[0]->getNumeroVencedor()->getCodFormando()->getUsuario();
	
	$log->debug($ret[0]->getNumeroVencedor()->getCodFormando()->getUsuario());
		
}else{
	
	$array["codRifa"]			= null;
	$array["rifaNome"]			= null;
	$array["vencedorNome"]		= null;
	$array["vencedorEmail"]		= null;
	$array["vencedorTelefone"]	= null;
	$array["vencedorNumero"]	= null;
	$array["vencedorData"]		= null;
	$array["vendedorNome"]		= null;
	$array["vendedorUsuario"]	= null;

}

echo json_encode($array);
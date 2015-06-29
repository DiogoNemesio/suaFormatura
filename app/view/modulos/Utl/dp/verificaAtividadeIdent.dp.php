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
if (isset($_GET['ident']))				$ident				= \Zage\App\Util::antiInjection($_GET["ident"]);
if (isset($_GET['codAtividade']))		$codAtividade		= \Zage\App\Util::antiInjection($_GET["codAtividade"]);

$array				= array();

if (!$ident) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

$oAtividade	= $em->getRepository('Entidades\ZgutlAtividade')->findOneBy(array('identificacao' => $ident));

if ($oAtividade != null && $oAtividade->getCodigo() != $codAtividade){
	$array["existe"]	= 1;
}else{
	$array["existe"]	= 0;
}

echo json_encode($array);
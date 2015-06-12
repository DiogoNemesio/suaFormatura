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
if (isset($_GET['codModulo']))	$codModulo			= \Zage\App\Util::antiInjection($_GET["codModulo"]);

$array				= array();

if (!isset($codModulo) || empty($codModulo)) {
	echo json_encode($array);
	exit;
}else{
	$secao = $em->getRepository('Entidades\ZgappParametroSecao')->findBy(array('codModulo' => $codModulo), array('nome' => 'ASC'));	
}

if ($secao) {
	for ($i = 0; $i < sizeof($secao); $i++) {
		$array[$i]["id"]	= $secao[$i]->getCodigo();
		$array[$i]["text"]	= $secao[$i]->getNome();
	}
}




echo json_encode($array);
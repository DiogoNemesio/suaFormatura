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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['q']))			$q				= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codModulo']))	$codModulo		= \Zage\App\Util::antiInjection($_GET["codModulo"]);
if (isset($_GET['codSecao']))	$codSecao		= \Zage\App\Util::antiInjection($_GET["codSecao"]);


$array				= array();

if (!isset($codModulo) || empty($codModulo)) {
	echo json_encode($array);
	exit;
}

if (isset($codSecao)) {
	$secoes		= $em->getRepository('Entidades\ZgappParametroSecao')->findBy(array('codigo' => $codSecao));
}else{
	$secoes		= \Zage\Adm\Parametro::buscaSecao($codModulo,$q);
}

for ($i = 0; $i < sizeof($secoes); $i++) {
	$array[$i]["id"]	= $secoes[$i]->getCodigo();
	$array[$i]["text"]	= $secoes[$i]->getNome();
}



echo json_encode($array);

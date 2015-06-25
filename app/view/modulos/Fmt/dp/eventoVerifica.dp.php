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
if (isset($_GET['codTipo']))			$codTipo		= \Zage\App\Util::antiInjection($_GET["codTipo"]);
if (isset($_GET['codEvento']))			$codEvento		= \Zage\App\Util::antiInjection($_GET["codEvento"]);

$array				= array();

if (!$codTipo) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oEvento	= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codFormatura' => $system->getCodOrganizacao(), 'codTipoEvento' => $codTipo));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oEvento != null && ($oEvento->getCodigo() != $codEvento)) {
	$array["existe"]	= 1;
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
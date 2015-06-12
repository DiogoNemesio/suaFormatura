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
if (isset($_GET['parametro']))			$parametro		= \Zage\App\Util::antiInjection($_GET["parametro"]);
if (isset($_GET['codParametro']))		$codParametro	= \Zage\App\Util::antiInjection($_GET["codParametro"]);
if (isset($_GET['codModulo']))			$codModulo		= \Zage\App\Util::antiInjection($_GET["codModulo"]);

$array				= array();

if (!$parametro) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('parametro' => $parametro));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oParametro != null && ($oParametro->getCodigo() != $codParametro)) {
	$array["existe"]	= 1;
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
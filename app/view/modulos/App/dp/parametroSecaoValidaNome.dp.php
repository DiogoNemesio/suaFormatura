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
if (isset($_GET['codSecao']))			$codSecao		= \Zage\App\Util::antiInjection($_GET["codSecao"]);
if (isset($_GET['codModulo']))			$codModulo		= \Zage\App\Util::antiInjection($_GET["codModulo"]);
if (isset($_GET['nome']))				$nome			= \Zage\App\Util::antiInjection($_GET["nome"]);

$array				= array();

if (!$nome) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oSecao		= $em->getRepository('Entidades\ZgappParametroSecao')->findOneBy(array('nome' => $nome,'codModulo' => $codModulo));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oSecao != null && ($oSecao->getCodigo() != $codSecao)) {
	$array["existe"]	= 1;
}else{
	$array["existe"]	= 0;
}

echo json_encode($array);
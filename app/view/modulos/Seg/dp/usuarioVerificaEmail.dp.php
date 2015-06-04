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
if (isset($_GET['email']))				$email				= \Zage\App\Util::antiInjection($_GET["email"]);
if (isset($_GET['codUsuario']))			$codUsuario			= \Zage\App\Util::antiInjection($_GET["codUsuario"]);

$array				= array();

if (!$email) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $email));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oUsuario != null && ($oUsuario->getCodigo() != $codUsuario)) {
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
}

echo json_encode($array);
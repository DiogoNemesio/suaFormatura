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
if (isset($_GET['codBanco']))			$codBanco		= \Zage\App\Util::antiInjection($_GET["codBanco"]);

$array				= array();

if (!$banco) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$infoBanco		= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($infoBanco == null) {
	$array["existe"]	= 0;
	
}else{
	$array["existe"]	= 1;	
}

echo json_encode($array);
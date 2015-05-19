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
if (isset($_GET['cargo']))					$cargo			= \Zage\App\Util::antiInjection($_GET["cargo"]);
if (isset($_GET['codCargo']))				$codCargo		= \Zage\App\Util::antiInjection($_GET["codCargo"]);

$array				= array();

if (!$cargo) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oCargo	= $em->getRepository('Entidades\ZgrhuFuncionarioCargo')->findOneBy(array('descricao' => $cargo));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oCargo != null && ($oCargo->getCodigo() != $codCargo)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
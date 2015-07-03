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
if (isset($_GET['descricao']))				$descricao		= \Zage\App\Util::antiInjection($_GET["descricao"]);
if (isset($_GET['codCentro']))				$codCentro		= \Zage\App\Util::antiInjection($_GET["codCentro"]);

$array				= array();

if (!$descricao) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oDescricao	= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'descricao' => $descricao));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oDescricao != null && ($oDescricao->getCodigo() != $codCentro)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
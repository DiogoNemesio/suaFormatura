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
if (isset($_GET['cnpj']))				$cnpj			= \Zage\App\Util::antiInjection($_GET["cnpj"]);
if (isset($_GET['codEmpresa']))			$codEmpresa		= \Zage\App\Util::antiInjection($_GET["codEmpresa"]);

$array				= array();

$cnpj = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->retiraMascara($cnpj);

if (!$cnpj) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oEmpresa	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'cnpj' => $cnpj));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oEmpresa != null && ($oEmpresa->getCodigo() != $codEmpresa)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
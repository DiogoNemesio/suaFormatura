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
if (isset($_GET['cnpj']))			$cnpj			= \Zage\App\Util::antiInjection($_GET["cnpj"]);
if (isset($_GET['codParceiro']))	$codParceiro	= \Zage\App\Util::antiInjection($_GET["codParceiro"]);

$array				= array();

$cnpj = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->retiraMascara($cnpj);

if (!$cnpj) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oCnpj	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('cgc' => $cnpj));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oCnpj != null && ($oCnpj->getCodigo() != $codParceiro)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
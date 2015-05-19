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
if (isset($_GET['codSindicato']))		$codSindicato	= \Zage\App\Util::antiInjection($_GET["codSindicato"]);

$array				= array();

$cnpj = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->retiraMascara($cnpj);

if (!$cnpj) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oSindicato	= $em->getRepository('Entidades\ZgrhuSindicato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'cnpj' => $cnpj));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oSindicato != null && ($oSindicato->getCodigo() != $codSindicato)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
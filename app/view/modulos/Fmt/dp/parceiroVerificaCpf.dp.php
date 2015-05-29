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
if (isset($_GET['cpf']))			$cpf			= \Zage\App\Util::antiInjection($_GET["cpf"]);
if (isset($_GET['codParceiro']))	$codParceiro	= \Zage\App\Util::antiInjection($_GET["codParceiro"]);

$array				= array();

$cpf = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->retiraMascara($cpf);

if (!$cpf) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oCpf	= $em->getRepository('Entidades\ZgfmtOrganizacao')->findOneBy(array('cgc' => $cpf));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oCpf != null && ($oCpf->getCodigo() != $codParceiro)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
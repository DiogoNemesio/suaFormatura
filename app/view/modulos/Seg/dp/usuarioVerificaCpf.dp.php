<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
//include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['cpf']))			$cpf			= \Zage\App\Util::antiInjection($_GET["cpf"]);
if (isset($_GET['codUsuario']))		$codUsuario		= \Zage\App\Util::antiInjection($_GET["codUsuario"]);

$array				= array();

if (!$cpf) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

$cpf = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->retiraMascara($cpf);

try {
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => $cpf));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oUsuario != null && ($oUsuario->getCodigo() != $codUsuario)) {
	$array["existe"]	= 1;
	$array["nome"]		= $oUsuario->getNome();
	$array["usuario"]	= $oUsuario->getUsuario();
	$array["cpf"]		= $oUsuario->getCpf();
}else{
	$array["existe"]	= 0;
}

echo json_encode($array);
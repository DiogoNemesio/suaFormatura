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
if (isset($_GET['funcao']))					$funcao			= \Zage\App\Util::antiInjection($_GET["funcao"]);
if (isset($_GET['codCargo']))				$codCargo		= \Zage\App\Util::antiInjection($_GET["codCargo"]);
if (isset($_GET['codFuncao']))				$codFuncao		= \Zage\App\Util::antiInjection($_GET["codFuncao"]);

//if (!isset($funcao))						$funcao			= array();


$array				= array();

if (!$funcao) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oFuncao	= $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findOneBy(array('descricao' => $funcao));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oFuncao != null && ($oFuncao->getCodigo() != $codFuncao)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
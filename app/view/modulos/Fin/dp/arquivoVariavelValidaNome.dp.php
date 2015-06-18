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
if (isset($_GET['variavel']))			$variavel			= \Zage\App\Util::antiInjection($_GET["variavel"]);
if (isset($_GET['codVariavel']))		$codVariavel		= \Zage\App\Util::antiInjection($_GET["codVariavel"]);

$array				= array();

if (!$variavel) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oNome	= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findOneBy(array('variavel' => $variavel));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oNome != null && ($oNome->getCodigo() != $codVariavel)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
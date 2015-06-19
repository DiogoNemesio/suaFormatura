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
if (isset($_GET['nome']))			$nome			= \Zage\App\Util::antiInjection($_GET["nome"]);
if (isset($_GET['codLayout']))		$codLayout		= \Zage\App\Util::antiInjection($_GET["codLayout"]);

$array				= array();

if (!$nome) {
	$array["existe"]	= 0;
	echo json_encode($array);
	exit;
}

try {
	$oNome	= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('nome' => $nome));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if ($oNome != null && ($oNome->getCodigo() != $codLayout)) {
	
	$array["existe"]	= 1;
	
}else{
	$array["existe"]	= 0;
	
}

echo json_encode($array);
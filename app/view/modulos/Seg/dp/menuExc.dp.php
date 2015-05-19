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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codMenu'])) 		$codMenu		= \Zage\App\Util::antiInjection($_POST['codMenu']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {
	
	$erro = \Zage\Seg\Menu::exclui($codMenu);

	if ($erro) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro)));
	}

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$codMenu.'|'.htmlentities($tr->trans("Menu excluído com sucesso")));

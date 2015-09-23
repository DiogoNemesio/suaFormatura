<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
        include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
        include_once('../../includeNoAuth.php');
}

global $log,$em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
$codOrganizacao 	= $system->getCodOrganizacao();

#################################################################################
## Resgatar a logomarca
#################################################################################
try {
	
	$oLogo	= $em->getRepository('\Entidades\ZgadmOrganizacaoLogo')->findOneBy(array('codOrganizacao' => $codOrganizacao));
	if (!$oLogo) exit;

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-type: '.$oLogo->getMimetype());
header('Content-disposition: inline; filename="'.$oLogo->getNome().'"');
header('Content-Length: ' . $oLogo->getTamanho());

echo stream_get_contents($oLogo->getLogomarca());
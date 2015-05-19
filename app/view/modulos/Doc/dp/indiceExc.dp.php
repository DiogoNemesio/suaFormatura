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
if (isset($_POST['codIndice'])) 		$codIndice		= \Zage\App\Util::antiInjection($_POST['codIndice']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codIndice) || (!$codIndice)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro %s não informado',array('%s' => "codIndice")))));
	}
	
	$oIndice	= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('codigo' => $codIndice));

	if (!$oIndice) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Índice não encontrado'))));
	}
	
	$em->remove($oIndice);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oIndice->getCodigo().'|'.htmlentities($tr->trans("Índice excluído com sucesso")));

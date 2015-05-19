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
if (isset($_POST['codTipoDoc'])) 		$codTipoDoc		= \Zage\App\Util::antiInjection($_POST['codTipoDoc']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codTipoDoc) || (!$codTipoDoc)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro codTipoDoc não informado'))));
	}
	
	$tipo	= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipoDoc));

	if (!$tipo) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Tipo de Documento não encontrado'))));
	}
	
	$em->remove($tipo);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$tipo->getCodigo().'|'.htmlentities($tr->trans("Tipo de Documento excluído com sucesso")));

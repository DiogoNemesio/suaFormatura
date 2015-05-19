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
if (isset($_POST['codPasta'])) 		$codPasta		= \Zage\App\Util::antiInjection($_POST['codPasta']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codPasta) || (!$codPasta)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro pasta não informado'))));
	}
	
	$oPasta	= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPasta));

	if (!$oPasta) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Pasta não encontrada'))));
	}
	
	$em->remove($oPasta);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oPasta->getCodigo().'|'.htmlentities($tr->trans("Pasta excluída com sucesso")));

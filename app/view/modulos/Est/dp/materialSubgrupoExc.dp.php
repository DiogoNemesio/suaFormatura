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
if (isset($_POST['codSubgrupo'])) 		$codSubgrupo		= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codSubgrupo) || (!$codSubgrupo)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro SubGrupo não informado'))));
	}
	
	$oSubgrupo	= $em->getRepository('Entidades\ZgestSubgrupoMaterial')->findOneBy(array('codigo' => $codSubgrupo));

	if (!$oSubgrupo) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('SubGrupo não encontrado'))));
	}
	
	$em->remove($oSubgrupo);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oSubgrupo->getCodigo().'|'.htmlentities($tr->trans("SubGrupo excluído com sucesso")));

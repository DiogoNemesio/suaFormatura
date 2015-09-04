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
if (isset($_POST['codGrupo'])) 		$codGrupo		= \Zage\App\Util::antiInjection($_POST['codGrupo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codGrupo) || (!$codGrupo)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro grupo não informado'))));
	}
	
	$oGrupo	= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codigo' => $codGrupo));

	if (!$oGrupo) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Grupo não encontrada'))));
	}
	
	$em->remove($oGrupo);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oGrupo->getCodigo().'|'.htmlentities($tr->trans("Grupo excluído com sucesso")));

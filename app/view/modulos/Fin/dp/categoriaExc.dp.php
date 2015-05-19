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
if (isset($_POST['codCategoria'])) 		$codCategoria		= \Zage\App\Util::antiInjection($_POST['codCategoria']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codCategoria) || (!$codCategoria)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro CATEGORIA não informado'))));
	}
	
	$oCat	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria,'codEmpresa' => $system->getCodMatriz()));

	if (!$oCat) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Categoria não encontrada'))));
	}
	
	$em->remove($oCat);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oCat->getCodigo().'|'.htmlentities($tr->trans("Categoria excluída com sucesso")));

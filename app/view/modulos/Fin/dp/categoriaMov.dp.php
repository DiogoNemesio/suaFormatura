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
if (isset($_POST['codCategoria']))	 		$codCategoria			= \Zage\App\Util::antiInjection($_POST['codCategoria']);
if (isset($_POST['codCategoriaDest'])) 		$codCategoriaDest		= \Zage\App\Util::antiInjection($_POST['codCategoriaDest']);

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
	
	$oCat	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria,'codOrganizacao' => $system->getCodOrganizacao()));

	if (!$oCat) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Categoria não encontrada'))));
	}
	
	$oCatDest	= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoriaDest,'codOrganizacao' => $system->getCodOrganizacao(), 'codCategoriaPai' => null));
	
	if (!$oCatDest) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Categoria de Destino não encontrada'))));
	}
	
	if (!$oCat->getCodCategoriaPai()) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Categoria não pode ser movida pois ela não é uma SubCategoria'))));
	} 
	
	if ($oCat->getCodCategoriaPai()->getCodigo() == $oCatDest->getCodigo()) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('SubCategoria já está na categoria: '.$oCatDest->getDescricao()))));
	}
	
	
	$oCat->setCodCategoriaPai($oCatDest);
	$em->persist($oCat);
	$em->flush();
	$em->detach($oCat);
	
} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oCat->getCodigo().'|'.htmlentities($tr->trans("Categoria movida com sucesso")));

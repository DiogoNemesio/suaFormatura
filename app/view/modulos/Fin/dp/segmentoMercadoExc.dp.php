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
if (isset($_POST['codSegmento'])) 		$codSegmento		= \Zage\App\Util::antiInjection($_POST['codSegmento']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codSegmento) || (!$codSegmento)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oSeg	= $em->getRepository('Entidades\ZgfinSegmentoMercado')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codSegmento));

	if (!$oSeg) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Segmento de Mercado não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oSeg);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Segmento de Mercado excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSeg->getCodigo().'|');
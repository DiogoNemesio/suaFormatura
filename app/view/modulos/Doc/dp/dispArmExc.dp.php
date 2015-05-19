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
if (isset($_POST['codDisp'])) 		$codDisp		= \Zage\App\Util::antiInjection($_POST['codDisp']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codDisp) || (!$codDisp)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oDisp	= $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codigo' => $codDisp));

	if (!$oDisp) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Dispositivo não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oDisp);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Dispositivo excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oDisp->getCodigo().'|');
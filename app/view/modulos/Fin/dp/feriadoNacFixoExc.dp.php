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
if (isset($_POST['codFeriadoNac'])) 		$codFeriadoNac		= \Zage\App\Util::antiInjection($_POST['codFeriadoNac']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codFeriadoNac) || (!$codFeriadoNac)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oFeriadoNac	= $em->getRepository('Entidades\ZgfinFeriadoNacional')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codFeriadoNac));

	if (!$oFeriadoNac) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Feriado Nacional não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oFeriadoNac);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Feriado Nacional excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoNac->getCodigo().'|');
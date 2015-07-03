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
if (isset($_POST['codCentro'])) 		$codCentro		= \Zage\App\Util::antiInjection($_POST['codCentro']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codCentro) || (!$codCentro)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oConta	= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codCentro));

	if (!$oConta) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Centro de Custo não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oConta);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Centro de Custo excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConta->getCodigo().'|');
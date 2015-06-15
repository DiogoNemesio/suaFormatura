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
if (isset($_POST['codSecao'])) 		$codSecao		= \Zage\App\Util::antiInjection($_POST['codSecao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codSecao) || (!$codSecao)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oSecao	= $em->getRepository('Entidades\ZgappParametroSecao')->findOneBy(array('codigo' => $codSecao));

	if (!$oSecao) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Seção não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oSecao);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Seção excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSecao->getCodigo().'|');
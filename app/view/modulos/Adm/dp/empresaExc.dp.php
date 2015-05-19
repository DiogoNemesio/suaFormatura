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
if (isset($_POST['codEmpresa'])) 		$codEmpresa		= \Zage\App\Util::antiInjection($_POST['codEmpresa']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se existe e excluir
#################################################################################
try {

	if (!isset($codEmpresa) || (!$codEmpresa)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oEmpresa = 	$em->getRepository ( 'Entidades\ZgadmEmpresa' )->findOneBy (array ('codigo' => $codEmpresa));
	
	if (!$oEmpresa) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Empresa não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oEmpresa);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Empresa excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEmpresa->getCodigo().'|');
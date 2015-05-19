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
if (isset($_POST['codSindicato'])) 		$codSindicato		= \Zage\App\Util::antiInjection($_POST['codSindicato']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se existe e excluir
#################################################################################
try {

	if (!isset($codSindicato) || (!$codSindicato)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oSindicato = 	$em->getRepository ( 'Entidades\ZgrhuSindicato' )->findOneBy (array ('codigo' => $codSindicato));
	
	if (!$oSindicato) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Sindicato não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oSindicato);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Sindicato excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSindicato->getCodigo().'|');
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
if (isset($_POST['codFeriadoFilialVar'])) 		$codFeriadoFilialVar		= \Zage\App\Util::antiInjection($_POST['codFeriadoFilialVar']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codFeriadoFilialVar) || (!$codFeriadoFilialVar)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oFeriadoFilialVar	= $em->getRepository('Entidades\ZgfinFeriadoFilialVariavel')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codFeriadoFilialVar));

	if (!$oFeriadoFilialVar) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Feriado Filial não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oFeriadoFilialVar);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Feriado Filial excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoFilialVar->getCodigo().'|');
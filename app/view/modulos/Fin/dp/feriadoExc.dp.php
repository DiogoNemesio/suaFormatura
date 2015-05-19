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
if (isset($_POST['codFeriado'])) 		$codFeriado		= \Zage\App\Util::antiInjection($_POST['codFeriado']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codFeriado) || (!$codFeriado)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oFeriado	= $em->getRepository('Entidades\ZgfinFeriadoVariavel')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(), 'codigo' => $codFeriado));

	if (!$oFeriado) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Feriado não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oFeriado);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Feriado excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriado->getCodigo().'|');
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
if (isset($_POST['codFeriadoNacVar'])) 		$codFeriadoNacVar		= \Zage\App\Util::antiInjection($_POST['codFeriadoNacVar']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codFeriadoNacVar) || (!$codFeriadoNacVar)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oFeriadoNacVar	= $em->getRepository('Entidades\ZgfinFeriadoNacionalVariavel')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codFeriadoNacVar));

	if (!$oFeriadoNacVar) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Feriado Nacional não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oFeriadoNacVar);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Feriado Nacional excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoNacVar->getCodigo().'|');
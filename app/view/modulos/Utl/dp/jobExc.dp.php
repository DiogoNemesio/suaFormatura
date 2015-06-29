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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codJob'])) 		$codJob	= \Zage\App\Util::antiInjection($_POST['codJob']);

#################################################################################
## Verificar se a Job existe e excluir
#################################################################################
try {

	if (!isset($codJob) || (!$codJob)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado"))));
	}
	
	#################################################################################
	## Verificar se o Job existe
	#################################################################################
	$oJob 	 = $em->getRepository('Entidades\ZgutlJob')->findOneBy(array('codigo' => $codJob));
	
	if (!$oJob) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Job não encontrado'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Job não encontrado"))));
	}
	
	$em->remove($oJob);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Job excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('||'.$tr->trans(htmlentities("Job excluído com sucesso!")));
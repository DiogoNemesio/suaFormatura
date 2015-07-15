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
if (isset($_POST['codChip'])) 		$codChip	= \Zage\App\Util::antiInjection($_POST['codChip']);

#################################################################################
## Verificar se a Chip existe e excluir
#################################################################################
try {

	if (!isset($codChip) || (!$codChip)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado"))));
	}
	
	#################################################################################
	## Verificar se a Chip existe
	#################################################################################
	$oChip 	 = $em->getRepository('Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
	
	if (!$oChip) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Chip não encontrado'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Chip não encontrado"))));
	}
	
	#################################################################################
	## Verificar se a Chip está em uso
	#################################################################################
	if ($oChip->getCodStatus()->getCodigo() != "R") {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans($tr->trans('Chip "%s" está ativo e não pode ser excluído (STATUS)',array('%s' => $oChip->getIdentificacao()))));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Chip "%s" está ativo e não pode ser excluído (STATUS)',array('%s' => $oChip->getIdentificacao())))));
	}
	
	$em->remove($oChip);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Chip excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('||'.$tr->trans(htmlentities("Chip excluído com sucesso!")));
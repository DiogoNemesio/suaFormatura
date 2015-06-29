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
if (isset($_POST['codAtividade'])) 		$codAtividade	= \Zage\App\Util::antiInjection($_POST['codAtividade']);

#################################################################################
## Verificar se a Atividade existe e excluir
#################################################################################
try {

	if (!isset($codAtividade) || (!$codAtividade)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado"))));
	}
	
	#################################################################################
	## Verificar se a Atividade existe
	#################################################################################
	$oAtividade 	 = $em->getRepository('Entidades\ZgutlAtividade')->findOneBy(array('codigo' => $codAtividade));
	
	if (!$oAtividade) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Atividade não encontrada'));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Atividade não encontrada"))));
	}
	
	#################################################################################
	## Verificar se a Atividade está em uso
	#################################################################################
	$jobs			= $em->getRepository('\Entidades\ZgutlJob')->findOneBy(array('codAtividade' => $codAtividade));
	$fila			= $em->getRepository('\Entidades\ZgappFilaImportacao')->findOneBy(array('codAtividade' => $codAtividade));
	
	if (!empty($jobs)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans($tr->trans('Atividade "%s" está em uso e não pode ser excluída (JOBS)',array('%s' => $oAtividade->getIdentificacao()))));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Atividade "%s" está em uso e não pode ser excluída (JOBS)',array('%s' => $oAtividade->getIdentificacao())))));
	}elseif (!empty($fila)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans($tr->trans('Atividade "%s" está em uso e não pode ser excluída (FILA)',array('%s' => $oAtividade->getIdentificacao()))));
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Atividade "%s" está em uso e não pode ser excluída (FILA)',array('%s' => $oAtividade->getIdentificacao())))));
	}
	
	$em->remove($oAtividade);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Atividade excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('||'.$tr->trans(htmlentities("Atividade excluída com sucesso!")));
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
global $em,$system,$_codMenu_;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);


#################################################################################
## Resgata os dados de previsão orcamentária
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$contrato	= $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

	if ($oOrgFmt)	{
		$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
		$valorArrecadado		= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorArrecadadoFormatura($system->getCodOrganizacao()));
		$valorGasto				= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorGastoFormatura($system->getCodOrganizacao()));
		$pctArrecadado			= ($valorOrcado) ? round(($valorArrecadado * 100) / $valorOrcado,2) : 0; 
		$pctGasto				= ($valorOrcado) ? round(($valorGasto * 100) / $valorOrcado,2) : 0;
		$diffPct				= round($pctArrecadado - $pctGasto,2);
		$viewPrevOrc			= null;
	}else{
		$valorOrcado			= 0;
		$valorArrecadado		= 0;
		$valorGasto				= 0;
		$pctArrecadado			= 0;
		$pctGasto				= 0;
		$diffPct				= 0;
		$viewPrevOrc			= "hidden";
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}




#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['mesFiltro']))		$mesFiltro			= \Zage\App\Util::antiInjection($_POST['mesFiltro']);
if (isset($_POST['dataAvancar']))	$dataAvancar		= \Zage\App\Util::antiInjection($_POST['dataAvancar']);
if (isset($_POST['dataVoltar']))	$dataVoltar			= \Zage\App\Util::antiInjection($_POST['dataVoltar']);

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (!isset($mesFiltro) && empty($mesFiltro)) $mesFiltro	= date('m/Y');


#################################################################################
## Verifica se é pra avançar ou retroceder a data
#################################################################################
if (isset($dataAvancar) && $dataAvancar == 1) {
	$offset	= 1;
}elseif (isset($dataVoltar) && $dataVoltar == 1) {
	$offset	= -1;
}else{
	$offset	= 0;
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MES_FILTRO'			,$mesFiltro);
$tpl->set('FILTER_URL'			,$urlFiltro);
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());
$tpl->set('TEXTO_FILTRO'		,$texto);
$tpl->set('VIEW_PREV_ORC'		,$viewPrevOrc);


$tpl->set('VALOR_ORCADO'		,\Zage\App\Util::to_money($valorOrcado));
$tpl->set('VALOR_ARRECADADO'	,\Zage\App\Util::to_money($valorArrecadado));
$tpl->set('VALOR_GASTO'			,\Zage\App\Util::to_money($valorGasto));
$tpl->set('PCT_ARRECADADO'		,$pctArrecadado);
$tpl->set('PCT_GASTO'			,$pctGasto);
$tpl->set('PCT_DIFF'			,$diffPct);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

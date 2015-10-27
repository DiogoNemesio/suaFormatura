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
$tpl->set('URL_RESULTADO'		,$urlResultado);
$tpl->set('URL_SALDO'			,$urlSaldo);
$tpl->set('URL_CONTA_PAG'		,$urlContaPag);
$tpl->set('URL_CONTA_REC'		,$urlContaRec);
$tpl->set('URL_FLUXO_CAIXA'		,$urlFluxoCaixa);
$tpl->set('URL_TRANSFERENCIA'	,$urlTransf);
$tpl->set('URL_DESP_CAT'		,$urlDespCat);
$tpl->set('URL_REC_CAT'			,$urlRecCat);
$tpl->set('URL_DESP_CENTRO'		,$urlDespCentro);
$tpl->set('URL_REC_CENTRO'		,$urlRecCentro);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

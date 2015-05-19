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
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
$codTipoFiltro	= (isset($codTipoFiltro)) 	? $codTipoFiltro		: 'D';
$dataFiltro		= (isset($dataFiltro)) 		? $dataFiltro			: date($system->config["data"]["dateFormat"]);
$mesFiltro		= (isset($mesFiltro)) 		? $mesFiltro			: date('m/Y');
$dataIniFiltro	= (isset($dataIniFiltro)) 	? $dataIniFiltro		: date($system->config["data"]["dateFormat"]);
$dataFimFiltro	= (isset($dataFimFiltro)) 	? $dataFimFiltro		: date($system->config["data"]["dateFormat"]);


#################################################################################
## Calcula o texto do filtro
#################################################################################
if ($codTipoFiltro == "D")	{
	$texto			=	($dataFiltro) ? $dataFiltro : $tr->trans("Todos os dias");
}elseif ($codTipoFiltro	== "M") {
	if ($mesFiltro) {
		$mes			= substr($mesFiltro,0,2);
		$texto			= $mesFiltro . " (".ucfirst(strftime("%B",mktime(0,0,0,$mes,1,null))).")";
	}else{
		$texto			= $tr->trans("Todos os meses");
	}
}else{
	if ($dataIniFiltro || $dataFimFiltro) {
		$texto			= $dataIniFiltro . ' - '.$dataFimFiltro;
	}else{
		$texto			= $tr->trans("Todos os dias");
	}
}

#################################################################################
## Gerar a url de filtro
#################################################################################
$urlFiltro		= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('COD_TIPO_FILTRO'		,$codTipoFiltro);
$tpl->set('DATA_FILTRO'			,$dataFiltro);
$tpl->set('MES_FILTRO'			,$mesFiltro);
$tpl->set('DATA_INI_FILTRO'		,$dataIniFiltro);
$tpl->set('DATA_FIM_FILTRO'		,$dataFimFiltro);
$tpl->set('FILTER_URL'			,$urlFiltro);
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());
$tpl->set('TEXTO_FILTRO'		,$texto);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
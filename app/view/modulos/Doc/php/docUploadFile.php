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
if (isset($_GET['codTipoDoc'])) 	{
	$codTipoDoc	= \Zage\App\Util::antiInjection($_GET['codTipoDoc']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (codTipoDoc)');
}


#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL."/Doc/".basename(__FILE__)."?id=".$id;

#################################################################################
## Lista as extensoes suportadas
#################################################################################
$extensoes		= \Zage\Adm\Parametro::getValor('DOC_EXT_PERMITIDAS');

#################################################################################
## Resgata o parâmetro de tamanho máximo de arquivo
#################################################################################
$maxFileSize	= \Zage\Adm\Parametro::getValor('DOC_MAX_FILE_SIZE_UPLOAD');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('URL'					,$url);
$tpl->set('COD_TIPO_DOC'		,$codTipoDoc);
$tpl->set('EXTENSOES'			,$extensoes);
$tpl->set('MAX_FILE_SIZE'		,$maxFileSize);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


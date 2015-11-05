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
global $em,$system,$tr,$log;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codVersaoOrc'])) 		$codVersaoOrc			= \Zage\App\Util::antiInjection($_GET['codVersaoOrc']);


#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);


#################################################################################
## Definindo título da Modal
#################################################################################
$titulo			= "Enviar o Orçamento por e-mail";


#################################################################################
## Url do script
#################################################################################
$urlReload			= ROOT_URL."/Fmt/orcamento.php?id=".$id;
$urlMidia			= ROOT_URL."/Fmt/orcamentoPdf.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('TITULO'					,$titulo);
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URL_RELOAD'				,$urlReload);
$tpl->set('URL_MIDIA'				,$urlMidia);
$tpl->set('ID'						,$id);

$tpl->set('COD_ORGANIZACAO'			,$system->getCodOrganizacao());
$tpl->set('COD_VERSAO_ORC'			,$codVersaoOrc);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
## Verificar parâmetros obrigatórios
#################################################################################
if (!isset($codOrganizacao)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
$urlVoltar			= ROOT_URL . "/Seg/usuarioLis.php?id=".$uid;
$urlNovo			= ROOT_URL . "/Seg/usuarioCad.php?id=".$uid;

#################################################################################
## Select de perfil
#################################################################################

try {
	$aPerfil	= \Zage\Seg\Perfil::listaPerfilOrganizacao($codOrganizacao);
	$oPerfil	= $system->geraHtmlCombo($aPerfil, 'CODIGO', 'NOME', ''	, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('ID'					,$id);
$tpl->set('COD_USUARIO'			,$codUsuario);
$tpl->set('COD_ORGANIZACAO'		,$codOrganizacao);
$tpl->set('USUARIO'				,$usuario);
$tpl->set('NOME'				,$nome);
$tpl->set('EMAIL'				,$email);
$tpl->set('PERFIL'				,$oPerfil);

$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


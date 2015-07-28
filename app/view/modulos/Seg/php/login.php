<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}

#################################################################################
## Resgata variáveis do formulário 
#################################################################################
if (!isset($usuario)) {
	$usuario	= null;
}

if (!isset($mensagem) || (!$mensagem) ) {
	$hidden		= "hidden";
	$mensagem	= null;
}else{
	$hidden		= null;
}

#################################################################################
## Verifica a url para onde deve ir, após o login
#################################################################################
if (!isset($url) || empty($url)) {
	$url = 	$_SERVER['REQUEST_URI'];
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MENSAGEM'		,$mensagem);
$tpl->set('URL_FORM'		,$url);
$tpl->set('USUARIO'			,$usuario);
$tpl->set('HIDDEN'			,$hidden);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


?>
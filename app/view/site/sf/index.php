<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

global $system;

#################################################################################
## Definir URL de troca de senha
#################################################################################
$urlTrocaSenha		= ROOT_URL . "/App/alteraSenha.php?id=".$id;

#################################################################################
## Definir o Data Processor de reenviar a senha perdida
#################################################################################
$dpSenha		= ROOT_URL . "/Seg/login.dp.php";


#################################################################################
## Carregando o template html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();

$tplHeader->load(SITE_PATH 	. '/html/header.html');
$tplMain->load(SITE_PATH 	. '/html/index.html');
$tplFooter->load(SITE_PATH 	. '/html/footer.html');

$tplHeader->set('DIVCENTRAL'		,$system->getDivCentral());
$tplHeader->set('URLINICIAL'		,null);
$tplHeader->set('IND_TROCAR_SENHA'	,null);
$tplHeader->set('TROCA_SENHA_URL'	,$urlTrocaSenha);
$tplHeader->set('MASCARAS'			,null);

$tplFooter->set('DP_SENHA'			,$dpSenha);

$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();

echo $html;
?>
<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

#################################################################################
## Carregando os templates html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();
$tplHeader->load(SITE_PATH 	. '/html/sfHeader.html');
$tplMain->load(SITE_PATH 	. '/html/sfMain.html');
$tplFooter->load(SITE_PATH 	. '/html/sfFooter.html');


$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();

echo $html;
?>
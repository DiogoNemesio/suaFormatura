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
## Gera o código javascript das máscaras
#################################################################################
$mascaras	= $em->getRepository('Entidades\ZgappMascara')->findAll();
$htmlMask		= "";
for ($i = 0; $i < sizeof($mascaras); $i++) {
	if ($mascaras[$i]->getIndReversa() == 1) {
		$reverse	= ",reverse: true";
	}else{
		$reverse	= "";
	}

	if ($mascaras[$i]->getIndTamanhoFixo() === 0) {
		$maxLen	= ",maxlength: false";
	}else{
		$maxLen	= "";
	}

	$htmlMask	.= "'".strtolower($mascaras[$i]->getNome())."': { mascara: '".$mascaras[$i]->getMascara()."' $reverse $maxLen},";
}
$htmlMask = substr($htmlMask, 0 , -1);

#################################################################################
## Carregando os templates html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();

$tplHeader->load(SITE_PATH 	. '/html/header.html');
$tplMain->load(SITE_PATH 	. '/html/cadTurma.html');
$tplFooter->load(SITE_PATH 	. '/html/footer.html');

$tplHeader->set('MASCARAS'		,$htmlMask);
$tplMain->set('DP'				,SITE_URL . 'dp/cadTurma.dp.php');
$tplMain->set('SITE_URL'		,SITE_URL);

$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();


echo $html;
?>
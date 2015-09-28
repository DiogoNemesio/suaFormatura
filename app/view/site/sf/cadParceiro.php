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
## Resgata as informaões do tipo PF/PJ
#################################################################################
try {
	$aTipo	= $em->getRepository('Entidades\ZgadmOrganizacaoPessoaTipo')->findAll();
	$oTipo	= $system->geraHtmlCombo($aTipo,'CODIGO', 'DESCRICAO', $tipo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgata as informaões da atividade
#################################################################################
try {
	$aAtividade	= $em->getRepository('Entidades\ZgadmOrganizacaoPrecadastroAtividade')->findBy(array('indAtivo' => '1'), array('descricao' => ASC));
	$oAtividade	= $system->geraHtmlCombo($aAtividade,'CODIGO', 'DESCRICAO', null, '');

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Carregando os templates html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();

$tplHeader->load(SITE_PATH 	. '/html/header.html');
$tplMain->load(SITE_PATH 	. '/html/cadParceiro.html');
$tplFooter->load(SITE_PATH 	. '/html/footer.html');

$tplHeader->set('MASCARAS'		,$htmlMask);
$tplMain->set('DP'				,SITE_URL . 'dp/cadParceiro.dp.php');
$tplMain->set('TIPO'			,$oTipo);
$tplMain->set('ATIVIDADE'		,$oAtividade);
$tplMain->set('SITE_URL'		,SITE_URL);

$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();


echo $html;
?>
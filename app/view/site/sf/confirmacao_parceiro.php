<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

if (isset($_GET['codOrgPre'])){
	$codOrgPre		= \Zage\App\Util::antiInjection($_GET['codOrgPre']);
}

#################################################################################
## Resgata as informações da formatura
#################################################################################
if ($codOrgPre){
	$oUsuOrg	= $em->getRepository('Entidades\ZgadmOrganizacaoPrecadastro')->findOneBy(array('codigo' => $codOrgPre));

	$tipo		= $oUsuOrg->getCodTipoPessoa()->getDescricao();
	$nome 		= $oUsuOrg->getNome();
	$cgc 		= $oUsuOrg->getCgc();
	$razao		= $oUsuOrg->getRazao();
	$email		= $oUsuOrg->getEmail();
	$telCom		= $oUsuOrg->getTelefoneComercial();
	$telCel		= $oUsuOrg->getTelefoneCelular();
	
	if ($oUsuOrg->getCodTipoPessoa()->getCodigo() == 'J'){
		$cgcTitulo = 'CNPJ';
	}else{
		$cgcTitulo = 'CPF';
	}
	
}

#################################################################################
## Carregando os templates html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();

$tplHeader->load(SITE_PATH 	. '/html/header.html');
$tplMain->load(SITE_PATH 	. '/html/confirmacao_parceiro.html');
$tplFooter->load(SITE_PATH 	. '/html/footer.html');

$tplHeader->set('MASCARAS'			,$htmlMask);
$tplMain->set('NOME'				,$nome);
$tplMain->set('TIPO'				,$tipo);
$tplMain->set('CGC_TITULO'			,$cgcTitulo);
$tplMain->set('CGC'					,$cgc);
$tplMain->set('EMAIL'				,$email);


$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();


echo $html;
?>
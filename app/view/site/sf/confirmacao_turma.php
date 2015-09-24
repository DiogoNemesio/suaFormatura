<?php 
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

if (isset($_GET['codUsuOrg'])){
	$codUsuOrg		= \Zage\App\Util::antiInjection($_GET['codUsuOrg']);
}

#################################################################################
## Resgata as informações da formatura
#################################################################################
if ($codUsuOrg){
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codigo' => $codUsuOrg));

	$nome 		= $oUsuOrg->getCodOrganizacao()->getNome();
	$ident		= $oUsuOrg->getCodOrganizacao()->getIdentificacao();
	$dataCad	= $oUsuOrg->getCodOrganizacao()->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]);
	$nomeUsu	= $oUsuOrg->getCodUsuario()->getNome();
	$emailUsu	= $oUsuOrg->getCodUsuario()->getUsuario();
	
	$oOrgFmt = $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $oUsuOrg->getCodOrganizacao()->getCodigo()));
	
	$dataCon 		= $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]);
	$instituicao 	= '('.$oOrgFmt->getCodInstituicao()->getSigla().') '.$oOrgFmt->getCodInstituicao()->getNome();
	$curso		 	= $oOrgFmt->getCodCurso()->getNome();
	$cidade		 	= $oOrgFmt->getCodCidade()->getNome(). ' - ' .$oOrgFmt->getCodCidade()->getCodUf()->getNome() ;
	
	$oContrato = $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $oUsuOrg->getCodOrganizacao()->getCodigo()));
	
	$plano = $oContrato->getCodPlano()->getNome();
	
	$taxaPorFormando		= \Zage\App\Util::formataDinheiro(\Zage\Adm\Contrato::getValorLicenca($oUsuOrg->getCodOrganizacao()->getCodigo()));
}

#################################################################################
## Carregando os templates html
#################################################################################
$tplHeader	= new \Zage\App\Template();
$tplMain	= new \Zage\App\Template();
$tplFooter	= new \Zage\App\Template();

$tplHeader->load(SITE_PATH 	. '/html/header.html');
$tplMain->load(SITE_PATH 	. '/html/confirmacao_turma.html');
$tplFooter->load(SITE_PATH 	. '/html/footer.html');

$tplHeader->set('MASCARAS'			,$htmlMask);
$tplMain->set('NOME'				,$nome);
$tplMain->set('IDENTIFICACAO'		,$ident);
$tplMain->set('DATA_CAD'			,$dataCad);
$tplMain->set('DATA_CONCLUSAO'		,$dataCon);
$tplMain->set('INSTITUICAO'			,$instituicao);
$tplMain->set('CURSO'				,$curso);
$tplMain->set('CIDADE'				,$cidade);
$tplMain->set('VALOR_PLANO'			,$taxaPorFormando);

$tplMain->set('NOME_FORMANDO'		,$nomeUsu);
$tplMain->set('USUARIO_FORMANDO'	,$emailUsu);

$tplMain->set('PLANO'				,$plano);

$html	= $tplHeader->getHtml();
$html	.= $tplMain->getHtml();
$html	.= $tplFooter->getHtml();


echo $html;
?>
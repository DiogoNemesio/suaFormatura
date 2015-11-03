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
global $em,$system;

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
## Resgata as informações da organização
#################################################################################
$oOrg 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Verifica se as configurações do cerimonial estão OK
#################################################################################
if ($oOrg->getCodTipo()->getCodigo() == "CER") {
	$orgCer		= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	if (!$orgCer) \Zage\App\Erro::halt('Sua Organização, não está configurada para cadastrar Formaturas, entre em contato com o SuaFormatura.com através do e-mail: '.$system->config["mail"]["admin"]);
	$hidden		= "hidden";
}else{
	$hidden		= "";
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oContrato	= $em->getRepository('\Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$ident			= $oOrg->getIdentificacao();
$nome			= $oOrg->getNome();
$instituicao	= $oOrgFmt->getCodInstituicao()->getCodigo();
$curso			= $oOrgFmt->getCodCurso()->getCodigo();
$cidade			= $oOrgFmt->getCodCidade()->getCodigo();
$dataConclusao	= ($oOrgFmt->getDataConclusao() != null) ? $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]) : null;

if ($oContrato) {
	$codPlano			= ($oContrato->getCodPlano()) ? $oContrato->getCodPlano()->getCodigo() : null;
	$valorDesconto		= \Zage\App\Util::formataDinheiro($oContrato->getValorDesconto());
	$pctDesconto		= \Zage\App\Util::formataDinheiro($oContrato->getPctDesconto());
	$formaDesc			= ($valorDesconto > 0) ? "V" : "P";
}else{
	$codPlano			= null;
	$formaDesc			= "V";
	$valorDesconto		= 0;
	$pctDesconto		= 0;
}


#################################################################################
## Taxas  
#################################################################################
$taxaAdmin				= \Zage\App\Util::to_float($oOrgFmt->getValorPorFormando());
$taxaBoleto				= \Zage\App\Util::to_float($oOrgFmt->getValorPorBoleto());
$taxaUso				= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));


#################################################################################
## Versões do orçamento
#################################################################################
$aVersoesOrc			= $em->getRepository('Entidades\ZgfmtOrcamento')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('versao' => 'DESC'));
try {
	$oVersoesOrc		= $system->geraHtmlCombo($aVersoesOrc, 'CODIGO', 'VERSAO', $codVersaoOrc, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

if ($aVersoesOrc) {
	$infoVersao			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));
	$codPlanoVersao		= $infoVersao->getCodPlanoVersao()->getCodigo();
}else{
	$codPlanoVersao		= null;
}


#################################################################################
## Buscar o cerimonial que está administrando
#################################################################################
$oFmtAdm				= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

if ($oFmtAdm)	{
	$aPlanoOrc	= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('versao' => 'ASC'));
}else{
	$aPlanoOrc	= null;
}

try {
	$oPlanoOrc		= $system->geraHtmlCombo($aPlanoOrc, 'CODIGO', 'VERSAO', $codPlanoVersao, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fmt/formaturaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormatura=');
$urlNovo			= ROOT_URL."/Fmt/formaturaAlt.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLNOVO'					,$urlNovo);
$tpl->set('ID'						,$id);

$tpl->set('COD_ORGANIZACAO'			,$system->getCodOrganizacao());
$tpl->set('IDENT'					,$ident);
$tpl->set('NOME'					,$nome);
$tpl->set('INSTITUICAO'				,$instituicao);
$tpl->set('CURSO'					,$curso);
$tpl->set('CIDADE'					,$cidade);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao);


$tpl->set('VERSOES_ORC'				,$oVersoesOrc);
$tpl->set('PLANO_ORC'				,$oPlanoOrc);
$tpl->set('COD_PLANO'				,$codPlano);
$tpl->set('VALOR_DESCONTO'			,$valorDesconto);
$tpl->set('PCT_DESCONTO'			,$pctDesconto);
$tpl->set('FORMA_DESCONTO'			,$formaDesc);
$tpl->set('HIDDEN'					,$hidden);

$tpl->set('TAXA_ADMIN'				,\Zage\App\Util::formataDinheiro($taxaAdmin));
$tpl->set('TAXA_BOLETO'				,\Zage\App\Util::formataDinheiro($taxaBoleto));
$tpl->set('TAXA_SISTEMA'			,\Zage\App\Util::formataDinheiro($taxaUso));

$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
//$tpl->set('DP'						,ROOT_URL."/Fmt/orcamentoPdf.php?id=".$uid);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

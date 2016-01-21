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
## Verificar se já existe algum orçamento aceite
#################################################################################
$orcAceite			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codOrganizacao' => $system->getCodorganizacao(),'indAceite' => 1));
if (!isset($codVersaoOrc) && $orcAceite) {
	$codVersaoOrc	= $orcAceite->getCodigo();
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
## Resgata as informações da organização
#################################################################################
$oOrg 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Verifica se as configurações do cerimonial estão OK
## e Verifica o perfil do usuário para fazer as limitações do vendedor
#################################################################################
$souVendedor			= \Zage\Seg\Usuario::ehVendedor($system->getCodOrganizacao(), $system->getCodUsuario());
$indVenAceite			= 1;

if ($oOrg->getCodTipo()->getCodigo() == "FMT") {
	$oFmtAdm	= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
	if ($oFmtAdm) {
		$orgCer				= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $oFmtAdm->getCodigo()));
		if (!$orgCer) \Zage\App\Erro::halt('Sua Organização, não está configurada para cadastrar Formaturas, entre em contato com o SuaFormatura.com através do e-mail: '.$system->config["mail"]["admin"]);
		if ($souVendedor == true) {
			$indVenAceite		= ($orgCer->getIndVendedorAceite() 			=== 0) ? 0 : 1;
		}
	}
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$ident			= $oOrg->getIdentificacao();
$nome			= $oOrg->getNome();
$instituicao	= $oOrgFmt->getCodInstituicao()->getCodigo();
$curso			= $oOrgFmt->getCodCurso()->getCodigo();
$cidade			= $oOrgFmt->getCodCidade()->getCodigo();
$dataConclusao	= ($oOrgFmt->getDataConclusao() != null) ? $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]) : null;

#################################################################################
## Taxas  
#################################################################################
$indRepTaxaSistema		= ($oOrgFmt->getIndRepassaTaxaSistema() !== null) ? $oOrgFmt->getIndRepassaTaxaSistema() : 1;
$taxaUso				= ($indRepTaxaSistema) ? \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao())) : 0;

#################################################################################
## Versões do orçamento
#################################################################################
$aVersoesOrc			= $em->getRepository('Entidades\ZgfmtOrcamento')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('versao' => 'DESC'));
//$oVersoesOrc			= "";
if (!$orcAceite)			{
	$oVersoesOrc			= '<option value="">'.$tr->trans("Novo Orçamento").'</option>';	
	$roPlano				= "";
}else{
	$oVersoesOrc			= "";
	$roPlano				= "readonly";
}

for ($i = 0; $i < sizeof($aVersoesOrc); $i++) {
	
	if ($aVersoesOrc[$i]->getIndAceite() == 1) {
		$icon	= 'data-icon=\"fa-check-circle green\"';
	}else{
		$icon	= null;
	}
	
	$selected 		= ($codVersaoOrc == $aVersoesOrc[$i]->getCodigo()) ?  "selected=\"true\"" : null;
	$dataVersao		= ($aVersoesOrc[$i]->getDataCadastro()) ? $aVersoesOrc[$i]->getDataCadastro()->format($system->config["data"]["dateFormat"]) : null;
	$oVersoesOrc	.= "<option ".$icon." value=\"".$aVersoesOrc[$i]->getCodigo()."\" $selected>".$tr->trans("Versão").": ".$aVersoesOrc[$i]->getVersao().' ('.$dataVersao.')'.'</option>';
}
//if (!$oVersoesOrc)	$oVersoesOrc	= '<option value="">'.$tr->trans("Novo Orçamento").'</option>';

if ($aVersoesOrc && $codVersaoOrc) {
	$infoVersao			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));
	$codPlanoOrc		= $infoVersao->getCodPlanoOrc()->getCodigo();
	$numFormandos		= $infoVersao->getQtdeFormandos();
	$numConvidados		= $infoVersao->getQtdeConvidados();
	$indAceite			= $infoVersao->getIndAceite();
}else{
	$codPlanoOrc		= null;
	$numFormandos		= null;
	$numConvidados		= null;
	$indAceite			= 0;
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
	$oPlanoOrc		= $system->geraHtmlCombo($aPlanoOrc, 'CODIGO', 'VERSAO', $codPlanoOrc, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Buscar as configurações da formatura, onde será gravado os valores de previsão
#################################################################################
$oFmt				= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

#################################################################################
## Url do script
#################################################################################
$urlReload			= ROOT_URL."/Fmt/orcamento.php?id=".$id;
$urlMidia			= ROOT_URL."/Fmt/orcamentoPdf.php?id=".$id;
$urlMail			= ROOT_URL."/Fmt/orcamentoMail.php?id=".$id;
$urlAceite			= ROOT_URL."/Fmt/orcamentoAceite.dp.php";

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URL_RELOAD'				,$urlReload);
$tpl->set('URL_MIDIA'				,$urlMidia);
$tpl->set('URL_MAIL'				,$urlMail);
$tpl->set('DP_ACEITE'				,$urlAceite);
$tpl->set('ID'						,$id);

$tpl->set('VALOR_PREVISTO'			,round($oFmt->getValorPrevistoTotal(),2));
$tpl->set('COD_ORGANIZACAO'			,$system->getCodOrganizacao());
$tpl->set('IDENT'					,$ident);
$tpl->set('NOME'					,$nome);
$tpl->set('INSTITUICAO'				,$instituicao);
$tpl->set('CURSO'					,$curso);
$tpl->set('CIDADE'					,$cidade);
$tpl->set('DATA_CONCLUSAO'			,$dataConclusao);
$tpl->set('NUM_FORMANDOS'			,$numFormandos);
$tpl->set('NUM_CONVIDADOS'			,$numConvidados);
$tpl->set('IND_ACEITE'				,$indAceite);
$tpl->set('COD_VERSAO'				,$codVersaoOrc);
$tpl->set('IND_VENDEDOR_ACEITE'		,$indVenAceite);

$tpl->set('RO_PLANO'				,$roPlano);
$tpl->set('VERSOES_ORC'				,$oVersoesOrc);
$tpl->set('PLANO_ORC'				,$oPlanoOrc);

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

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
global $em,$system,$_codMenu_;

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
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
		
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$contrato	= $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Verificar se existe contrato 
#################################################################################
if (!$contrato)	\Zage\App\Erro::halt('Não foi localizado o contrato !!!');

	
$taxaAdministracao		= \Zage\App\Util::formataDinheiro($oOrgFmt->getTaxaAdministracao());
$taxaPorFormando		= \Zage\App\Util::formataDinheiro(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$diaVencimento			= $oOrgFmt->getDiaVencimento();
$pctJurosTurma			= $oOrgFmt->getPctJurosTurma();
$pctMoraTurma			= $oOrgFmt->getPctMoraTurma();
$pctConviteTurma		= $oOrgFmt->getPctConviteExtraTurma();
$pctDevolucao			= $oOrgFmt->getPctDevolucao();
$indRepTaxaSistema		= (($oOrgFmt->getIndRepassaTaxaSistema() === null)|| $oOrgFmt->getIndRepassaTaxaSistema() == 1) ? "checked" : null;

if ($taxaAdministracao	< 0) 	$taxaAdministracao	= 0;
if ($taxaPorFormando	< 0)	$taxaPorFormando	= 0;
if (!$diaVencimento)			$diaVencimento		= 5;
if ($pctJurosTurma		< 0)	$pctJurosTurma		= 0;
if ($pctMoraTurma		< 0)	$pctMoraTurma		= 0;
if ($pctConviteTurma	< 0)	$pctConviteTurma	= 0;
if ($pctDevolucao		< 0)	$pctDevolucao		= 0;



#################################################################################
## Caso os percentuais não estejam definidos, usar 100 % para a turma
#################################################################################
if ($pctJurosTurma		=== null)		$pctJurosTurma		= 100;
if ($pctMoraTurma		=== null)		$pctMoraTurma		= 100;
if ($pctConviteTurma	=== null)		$pctConviteTurma	= 100;
if ($pctDevolucao		=== null)		$pctDevolucao		= 100;

#################################################################################
## Montar o select do dia de vencimento
#################################################################################
$oDiaVenc	= "";
for ($i = 1; $i <= 31; $i++) {
	$selected = ($i == $diaVencimento) ? " selected " : "";
	$dia	= str_pad($i, 2, "0",STR_PAD_LEFT);
	if ($i > 28) {
		$dia	.= " * (ou o último dia do mês)"; 
	}
	
	$oDiaVenc	.= "<option value='".$i."' $selected>".$dia."</option>"; 
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('COD_ORGANIZACAO'			,$system->getCodOrganizacao());
$tpl->set('TAXA_ADMINISTRACAO'		,$taxaAdministracao);
$tpl->set('TAXA_FORMANDO'			,$taxaPorFormando);
$tpl->set('DIAS_VENC'				,$oDiaVenc);
$tpl->set('PCT_JUROS_TURMA'			,$pctJurosTurma);
$tpl->set('PCT_MORA_TURMA'			,$pctMoraTurma);
$tpl->set('PCT_CONVITE_TURMA'		,$pctConviteTurma);
$tpl->set('PCT_DEVOLUCAO'			,$pctDevolucao);
$tpl->set('IND_REPASSA_TAXA_SISTEMA',$indRepTaxaSistema);

$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
global $em,$log,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codOrganizacao']))		$codOrganizacao			= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['taxaAdministracao']))		$taxaAdministracao		= \Zage\App\Util::antiInjection($_POST['taxaAdministracao']);
if (isset($_POST['diaVencimento']))			$diaVencimento			= \Zage\App\Util::antiInjection($_POST['diaVencimento']);
if (isset($_POST['pctJurosTurma']))			$pctJurosTurma			= \Zage\App\Util::antiInjection($_POST['pctJurosTurma']);
if (isset($_POST['pctMoraTurma']))			$pctMoraTurma			= \Zage\App\Util::antiInjection($_POST['pctMoraTurma']);
if (isset($_POST['pctConviteExtraTurma']))	$pctConviteExtraTurma	= \Zage\App\Util::antiInjection($_POST['pctConviteExtraTurma']);
if (isset($_POST['pctDevolucao']))			$pctDevolucao			= \Zage\App\Util::antiInjection($_POST['pctDevolucao']);
if (isset($_POST['indRepassaTaxaSistema']))	$indRepTaxaSistema		= \Zage\App\Util::antiInjection($_POST['indRepassaTaxaSistema']);

$err	= null;

#################################################################################
## Validação dos campos
#################################################################################
if (!isset($codOrganizacao) || empty($codOrganizacao)) {
	$err = $tr->trans("Falta de parâmetros (COD_ORGANIZACAO)");
}else{
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	if (!$oOrg) {
		$err = $tr->trans("Organização (%s) não encontrada !!",array('%s' => $codOrganizacao));
	}else{
		if ($oOrg->getCodTipo()->getCodigo() !== "FMT") {
			$err = $tr->trans("Organização (%s) não é uma FORMATURA!!",array('%s' => $codOrganizacao));
		}
	}
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $codOrganizacao));
if (!$oOrgFmt) {
	$err = $tr->trans("Formatura (%s) sem configuração!!",array('%s' => $codOrganizacao));
}

if ($err) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}


#################################################################################
## Ajuste dos valores
#################################################################################
$taxaAdministracao		= \Zage\App\Util::to_float($taxaAdministracao);
$pctJurosTurma			= (int) $pctJurosTurma;
$pctMoraTurma			= (int) $pctMoraTurma;
$pctConviteExtraTurma	= (int) $pctConviteExtraTurma;
$pctDevolucao			= (int) $pctDevolucao;
$indRepTaxaSistema		= (isset($indRepTaxaSistema) || $indRepTaxaSistema) ? 1 : 0;


#################################################################################
## Salvar no banco
#################################################################################
try {
	$oOrgFmt->setTaxaAdministracao($taxaAdministracao);
	$oOrgFmt->setDiaVencimento($diaVencimento);
	$oOrgFmt->setPctJurosTurma($pctJurosTurma);
	$oOrgFmt->setPctMoraTurma($pctMoraTurma);
	$oOrgFmt->setPctConviteExtraTurma($pctConviteExtraTurma);
	$oOrgFmt->setPctDevolucao($pctDevolucao);
	$oOrgFmt->setIndRepassaTaxaSistema($indRepTaxaSistema);
	$em->persist($oOrgFmt);
	
	$em->flush();
	$em->clear();
	
	$mensagem	= $tr->trans("Configurações salvas com sucesso");
	
} catch (\Exception $e) {
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
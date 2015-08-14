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
if (isset($_POST['valorPorFormando']))		$valorPorFormando		= \Zage\App\Util::antiInjection($_POST['valorPorFormando']);
if (isset($_POST['valorPorBoleto']))		$valorPorBoleto			= \Zage\App\Util::antiInjection($_POST['valorPorBoleto']);
if (isset($_POST['diaVencimento']))			$diaVencimento			= \Zage\App\Util::antiInjection($_POST['diaVencimento']);


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
$valorPorFormando	= \Zage\App\Util::toMysqlNumber($valorPorFormando);
$valorPorBoleto		= \Zage\App\Util::toMysqlNumber($valorPorBoleto);


#################################################################################
## Salvar no banco
#################################################################################
try {
	$oOrgFmt->setValorPorFormando($valorPorFormando);
	$oOrgFmt->setValorPorBoleto($valorPorBoleto);
	$oOrgFmt->setDiaVencimento($diaVencimento);
	$em->persist($oOrgFmt);
	
	$em->flush();
	$em->clear();
	
	$mensagem	= $tr->trans("Configurações salvas com sucesso");
	
} catch (\Exception $e) {
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
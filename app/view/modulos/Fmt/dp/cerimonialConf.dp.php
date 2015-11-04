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
global $em,$log,$system;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codOrganizacao']))		$codOrganizacao			= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['codPlano']))				$codPlano				= \Zage\App\Util::antiInjection($_POST['codPlano']);
if (isset($_POST['codFormaDesconto']))		$codFormaDesconto				= \Zage\App\Util::antiInjection($_POST['codFormaDesconto']);
if (isset($_POST['valorDesconto']))			$valorDesconto				= \Zage\App\Util::antiInjection($_POST['valorDesconto']);
if (isset($_POST['pctDesconto']))			$pctDesconto				= \Zage\App\Util::antiInjection($_POST['pctDesconto']);


$err	= null;

if (!isset($codOrganizacao) || empty($codOrganizacao)) {
	$err = $tr->trans("Falta de parâmetros (COD_ORGANIZACAO)");
}else{
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	if (!$oOrg) {
		$err = $tr->trans("Organização (%s) não encontrada !!",array('%s' => $codOrganizacao));
	}else{
		if ($oOrg->getCodTipo()->getCodigo() !== "CER") {
			$err = $tr->trans("Organização (%s) não é um CERIMONIAL!!",array('%s' => $codOrganizacao));
		}
	}
}

if (!isset($codPlano) || empty($codPlano)) {
	$err = $tr->trans("Falta de parâmetros (COD_PLANO)");
}else{
	$oPlano		= $em->getRepository('Entidades\ZgadmPlano')->findOneBy(array('codigo' => $codPlano));
	if (!$oPlano) {
		$err = $tr->trans("Plano (%s) não encontrado !!",array('%s' => $codPlano));
	}else{
		if ($oPlano->getCodTipoLicenca()->getCodigo() != "F") {
			$err = $tr->trans("Plano (%s) não é para FORMATURA!!",array('%s' => $codPlano));
		}
	}
}

/** DESCONTO **/
if ($codFormaDesconto == V){
	if (isset($valorDesconto) || !empty($valorDesconto)) {
		$valorDesconto = \Zage\App\Util::toMysqlNumber($valorDesconto);
		$pctDesconto = null;
	}else{
		$valorDesconto = null;
		$pctDesconto = null;	
	}
}elseif ($codFormaDesconto == P){
	if (isset($pctDesconto) || !empty($pctDesconto)) {
		$pctDesconto 	=\Zage\App\Util::toMysqlNumber(str_replace("%", "", $pctDesconto));
		$valorDesconto 	= null;
	}else{
		$pctDesconto = null;
		$valorDesconto = null;
	}
}


if ($err) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$oOrgCer	= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $codOrganizacao));
if (!$oOrgCer) {
	$oOrgCer	= new \Entidades\ZgfmtOrganizacaoCerimonial();
}


#################################################################################
## Salvar no banco
#################################################################################
try {
	$oOrgCer->setCodOrganizacao($oOrg);
	$oOrgCer->setCodPlanoFormatura($oPlano);
	$oOrgCer->setValorDesconto($valorDesconto);
	$oOrgCer->setPctDesconto($pctDesconto);
	$em->persist($oOrgCer);
	
	$em->flush();
	$em->clear();
	
	$mensagem	= $tr->trans("Configurações salvas com sucesso");
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
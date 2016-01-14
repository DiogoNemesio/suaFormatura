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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codVendaPlano']))		$codVendaPlano		= \Zage\App\Util::antiInjection($_POST['codVendaPlano']);
if (isset($_POST['codOrganizacao'])) 	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificações
#################################################################################

try {
	/*** Verificar parâmetros ***/
	if (!isset($codVendaPlano) || (!$codVendaPlano)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_VENDA_PLANO"))));
		$err	= 1;
	}
	
	if (!isset($codOrganizacao) || (!$codOrganizacao)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_ORGANIZACAO"))));
		$err	= 1;
	}
	
	/*** Verificar o Venda Plano existe ***/
	$oVendaPlano	= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findOneBy(array('codigo' => $codVendaPlano));

	if (!$oVendaPlano) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Ops! não conseguimentos encontratar o plano selecionado. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte."))));
		$err	= 1;
	}
	
	/*** Verificar se a organização existe ***/
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	
	if (!$oOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Ops! não conseguimentos encontratar a organização selecionada. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte."))));
		$err = 1;
	}
	
	if ($err != null) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## HABILITAR OU DESABILITAR
	#################################################################################	
	if ($oVendaPlano->getIndHabilitado() == 1){
		$oVendaPlano->setIndHabilitado(0);
		$msg = 'Plano desabilitado com sucesso!';
	}else{
		$oVendaPlano->setIndHabilitado(1);
		$msg = 'Plano habilitado com sucesso!';
	}
	
	$em->persist($oVendaPlano);

	#################################################################################
	## Salvar as informações
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte.");
	}

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.$msg);
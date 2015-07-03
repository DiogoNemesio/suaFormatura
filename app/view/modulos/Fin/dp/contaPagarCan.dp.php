<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $em,$log,$system;


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['motivo']))			$motivo				= \Zage\App\Util::antiInjection($_POST['motivo']);

$err	= null;

if (!isset($codConta) || empty($codConta)) {
	$err = $tr->trans("Falta de parâmetros (COD_CONTA)");
}else{
	$aSelContas	= explode(",", $codConta);
}

if (!isset($motivo) || empty($motivo)) {
	$err = $tr->trans("Falta de parâmetros (MOTIVO)");
}

if ($err) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaPagar')->findBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $aSelContas));


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($contas); $i++) {
		
		$conta		= new \Zage\Fin\ContaPagar();
		$erro		= $conta->cancela($contas[$i], $motivo);
		
		if ($erro != false) {
			$em->getConnection()->rollback();
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
		}
		
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	
	if (sizeof($contas) > 1) {
		$mensagem	= $tr->trans("%s Contas canceladas com sucesso",array('%s' => sizeof($aSelContas)));
	}else{
		$mensagem	= $tr->trans("Conta cancelada com sucesso");
	}
	
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
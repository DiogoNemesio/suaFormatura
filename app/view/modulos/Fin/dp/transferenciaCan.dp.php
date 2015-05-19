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
if (isset($_POST['codTransferencia']))	$codTransferencia	= \Zage\App\Util::antiInjection($_POST['codTransferencia']);
if (isset($_POST['motivo']))			$motivo				= \Zage\App\Util::antiInjection($_POST['motivo']);

$err	= null;

if (!isset($codTransferencia) || empty($codTransferencia)) {
	$err = $tr->trans("Falta de parâmetros (COD_TRANSFERENCIA)");
}else{
	$aSelTransfs	= explode(",", $codTransferencia);
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
$transferencias		= $em->getRepository('Entidades\ZgfinTransferencia')->findBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $aSelTransfs));


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($transferencias); $i++) {
		
		$conta		= new \Zage\Fin\Transferencia();
		$erro		= $conta->cancela($transferencias[$i], $motivo);
		
		if ($erro != false) {
			$em->getConnection()->rollback();
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
		}
		
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	
	if (sizeof($transferencias) > 1) {
		$mensagem	= $tr->trans("%s Transferências canceladas com sucesso",array('%s' => sizeof($aSelTransfs)));
	}else{
		$mensagem	= $tr->trans("Transferência cancelada com sucesso");
	}
	
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
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
if (isset($_POST['codTransf']))			$codTransf			= \Zage\App\Util::antiInjection($_POST['codTransf']);

$err	= null;

if (!isset($codTransf) || empty($codTransf)) {
	$err = $tr->trans("Falta de parâmetros (COD_CONTA)");
}else{
	$aSelTransfs	= explode(",", $codTransf);
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
		
		$transf		= new \Zage\Fin\Transferencia();
		$erro		= $transf->exclui($transferencias[$i]);
		
		if ($erro != false) {
			$em->getConnection()->rollback();
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
		}
	}
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();	
	
	if (sizeof($aSelTransfs) > 1) {
		$mensagem	= $tr->trans("%s Transferências excluídas com sucesso",array('%s' => sizeof($aSelTransfs)));
	}else{
		$mensagem	= $tr->trans("Transferência excluída com sucesso");
	}
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
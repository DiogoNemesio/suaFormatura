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
if (isset($_POST['codTransfLote']))		$codTransf			= \Zage\App\Util::antiInjection($_POST['codTransfLote']);
if (isset($_POST['codFormaPagLote']))	$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPagLote']);
if (isset($_POST['dataTransfLote']))	$dataTransf			= \Zage\App\Util::antiInjection($_POST['dataTransfLote']);
if (isset($_POST['documentoLote']))		$documento			= \Zage\App\Util::antiInjection($_POST['documentoLote']);
if (isset($_POST['usaOrigFormaLote']))	$usaOrigForma		= \Zage\App\Util::antiInjection($_POST['usaOrigFormaLote']);
if (isset($_POST['usaOrigTransfLote']))	$usaOrigTransf		= \Zage\App\Util::antiInjection($_POST['usaOrigTransfLote']);

if (!isset($codTransf) || empty($codTransf)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (COD_TRANSF)"))));
}else{
	$aSelTransfs	= explode(",", $codTransf);
}

if (!isset($usaOrigTransf) || (empty($usaOrigTransf))) {
	if (!isset($dataTransf) || empty($dataTransf)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (DATA_TRANSF)"))));
	}else{
		$valData	= new \Zage\App\Validador\DataBR();
		if ($valData->isValid($dataTransf) == false) {
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo DATA DA TRANSFERÊNCIA inválida"))));
		}
	}
}

if (!isset($usaOrigForma) || (empty($usaOrigForma))) {
	if (!isset($codFormaPag) || empty($codFormaPag)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Forma de Pagamento deve ser selecionada !!!"))));
	}
}

#################################################################################
## Resgata as informações do banco
#################################################################################
$transferencias		= $em->getRepository('Entidades\ZgfinTransferencia')->findBy(array('codFilial' => $system->getcodOrganizacao(), 'codigo' => $aSelTransfs));


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($transferencias); $i++) {
		$transf		= new \Zage\Fin\Transferencia();
		
		#################################################################################
		## Verifica se vai usar a Forma de pagamento Original ou a informada
		#################################################################################
		if (isset($usaOrigForma) || (!empty($usaOrigForma))) {
			$codFormaPag	= ($transferencias[$i]->getCodFormaPagamento()) ? $transferencias[$i]->getCodFormaPagamento()->getCodigo() : null;
		}
		
		#################################################################################
		## Verifica se vai usar a Data de vencimento Original ou a Data de Recebimento informada
		#################################################################################
		if (isset($usaOrigTransf) || (!empty($usaOrigTransf))) {
			$dataTransf		= $transferencias[$i]->getDataTransferencia()->format($system->config["data"]["dateFormat"]);
		}
		
		#################################################################################
		## Resgatar o valor (Saldo da conta) que tem a receber
		#################################################################################
		$valor				= \Zage\App\Util::toPHPNumber($transf->getSaldoATransferir($transferencias[$i]->getCodigo()));
		
		$codContaOrig		= $transferencias[$i]->getCodContaOrigem()->getCodigo();
		$codContaDest		= $transferencias[$i]->getCodContaDestino()->getCodigo();
		
		#################################################################################
		## Realiza a transferência
		#################################################################################
		$erro		= $transf->realiza ($transferencias[$i],$codContaOrig,$codContaDest,$codFormaPag,$dataTransf,$valor,$documento);
		
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
		$mensagem	= $tr->trans("%s Transferências efetivadas com sucesso",array('%s' => sizeof($aSelTransfs)));
	}else{
		$mensagem	= $tr->trans("Transferência efetivada com sucesso");
	}
	
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
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
if (isset($_POST['codContaOrig']))		$codContaOrig		= \Zage\App\Util::antiInjection($_POST['codContaOrig']);
if (isset($_POST['codContaDest']))		$codContaDest		= \Zage\App\Util::antiInjection($_POST['codContaDest']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['dataTransf']))		$dataTransf			= \Zage\App\Util::antiInjection($_POST['dataTransf']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['documento']))			$documento			= \Zage\App\Util::antiInjection($_POST['documento']);


if (!isset($codTransf) || empty($codTransf)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (COD_TRANSF)"))));
}

if (!isset($codFormaPag) || empty($codFormaPag)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Forma de Pagamento não informado !!!"))));
}

if (!isset($dataTransf) || empty($dataTransf)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (DATA_REC)"))));
}

if (!isset($valor) || empty($valor)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (VALOR)"))));
}

if (!isset($codContaOrig) || empty($codContaOrig)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (COD_CONTA_ORIG)"))));
}

if (!isset($codContaDest) || empty($codContaDest)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (COD_CONTA_DEST)"))));
}

$valData	= new \Zage\App\Validador\DataBR();

if ($valData->isValid($dataTransf) == false) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo DATA DE TRANSFERÊNCIA inválida"))));
}

#################################################################################
## Resgata as informações da conta
#################################################################################
$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codTransf));

if (!$oTransf) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Transferência não encontrada (".$codTransf.")"))));
}

#################################################################################
## Validar o valor
#################################################################################
$saldo	= \Zage\Fin\Transferencia::getSaldoATransferir($codTransf);
if ($valor > $saldo) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Valor máximo da transferência deve ser: ".\Zage\App\Util::to_money($saldo)." !!!"))));
}

#################################################################################
## Ajustar os valores
#################################################################################
if (empty($valor))			$valor				= 0;


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	$transf		= new \Zage\Fin\Transferencia();
	$erro		= $transf->realiza ($oTransf,$codContaOrig,$codContaDest,$codFormaPag,$dataTransf,$valor,$documento);
	
	if ($erro != false) {
		$em->getConnection()->rollback();
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
		exit;
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities("Transferência realizada com sucesso"));
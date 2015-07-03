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
if (isset($_POST['codContaLote']))		$codConta			= \Zage\App\Util::antiInjection($_POST['codContaLote']);
if (isset($_POST['codContaDebLote']))	$codContaDeb		= \Zage\App\Util::antiInjection($_POST['codContaDebLote']);
if (isset($_POST['codFormaPagLote']))	$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPagLote']);
if (isset($_POST['dataPagLote']))		$dataPag			= \Zage\App\Util::antiInjection($_POST['dataPagLote']);
if (isset($_POST['documentoLote']))		$documento			= \Zage\App\Util::antiInjection($_POST['documentoLote']);
if (isset($_POST['usaOrigFormaLote']))	$usaOrigForma		= \Zage\App\Util::antiInjection($_POST['usaOrigFormaLote']);
if (isset($_POST['usaOrigContaLote']))	$usaOrigConta		= \Zage\App\Util::antiInjection($_POST['usaOrigContaLote']);
if (isset($_POST['usaOrigVencLote']))	$usaOrigVenc		= \Zage\App\Util::antiInjection($_POST['usaOrigVencLote']);

if (!isset($codConta) || empty($codConta)) {
	$err = $tr->trans("Falta de parâmetros (COD_CONTA)");
}else{
	$aSelContas	= explode(",", $codConta);
}

if (!isset($usaOrigVenc) || (empty($usaOrigVenc))) {
	if (!isset($dataPag) || empty($dataPag)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (DATA_PAG)"))));
	}else{
		$valData	= new \Zage\App\Validador\DataBR();
		if ($valData->isValid($dataPag) == false) {
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo DATA DE PAGAMENTO inválido"))));
		}
	}
}

if (!isset($usaOrigForma) || (empty($usaOrigForma))) {
	if (!isset($codFormaPag) || empty($codFormaPag)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Forma de Pagamento deve ser selecionada !!!"))));
	}
}

if (!isset($usaOrigConta) || (empty($usaOrigConta))) {
	if (!isset($codContaDeb) || empty($codContaDeb)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Conta de Pagamento deve ser selecionada !!!"))));
	}
}


#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaPagar')->findBy(array('codFilial' => $system->getcodOrganizacao(), 'codigo' => $aSelContas));


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($contas); $i++) {
		$conta		= new \Zage\Fin\ContaPagar();
		
		#################################################################################
		## Verifica se vai usar a Conta Original ou a informada
		#################################################################################
		if (isset($usaOrigConta) || (!empty($usaOrigConta))) {
			$codContaDeb	= ($contas[$i]->getCodConta()) ? $contas[$i]->getCodConta()->getCodigo() : null; 
		}
		
		#################################################################################
		## Verifica se vai usar a Forma de pagamento Original ou a informada
		#################################################################################
		if (isset($usaOrigForma) || (!empty($usaOrigForma))) {
			$codFormaPag	= ($contas[$i]->getCodFormaPagamento()) ? $contas[$i]->getCodFormaPagamento()->getCodigo() : null;
		}
		
		#################################################################################
		## Verifica se vai usar a Data de vencimento Original ou a Data de Pagamento informada
		#################################################################################
		if (isset($usaOrigVenc) || (!empty($usaOrigVenc))) {
			$dataPag		= $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
		}
		
		#################################################################################
		## Resgatar o valor (Saldo da conta) que tem a pagar
		#################################################################################
		$valor				= \Zage\App\Util::toPHPNumber($conta->getSaldoAPagar($contas[$i]->getCodigo()));
		
		#################################################################################
		## Efetiva o pagamento
		#################################################################################
		$erro		= $conta->paga($contas[$i],$codContaDeb,$codFormaPag,$dataPag,$valor,0,0,0,$documento);
		
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
		$mensagem	= $tr->trans("%s Contas efetivadas com sucesso",array('%s' => sizeof($aSelContas)));
	}else{
		$mensagem	= $tr->trans("Conta efetivada com sucesso");
	}
	
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.$mensagem);
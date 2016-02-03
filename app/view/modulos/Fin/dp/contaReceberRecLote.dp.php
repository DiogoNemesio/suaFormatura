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
if (isset($_POST['codContaCreLote']))	$codContaCre		= \Zage\App\Util::antiInjection($_POST['codContaCreLote']);
if (isset($_POST['codFormaPagLote']))	$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPagLote']);
if (isset($_POST['dataRecLote']))		$dataRec			= \Zage\App\Util::antiInjection($_POST['dataRecLote']);
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
	if (!isset($dataRec) || empty($dataRec)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (DATA_REC)"))));
	}else{
		$valData	= new \Zage\App\Validador\DataBR();
		if ($valData->isValid($dataRec) == false) {
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo DATA DE RECEBIMENTO inválida"))));
		}
	}
}

if (!isset($usaOrigForma) || (empty($usaOrigForma))) {
	if (!isset($codFormaPag) || empty($codFormaPag)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Forma de Pagamento deve ser selecionada !!!"))));
	}
}

if (!isset($usaOrigConta) || (empty($usaOrigConta))) {
	if (!isset($codContaCre) || empty($codContaCre)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Conta de Recebimento deve ser selecionada !!!"))));
	}
}



#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->findBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $aSelContas));

#################################################################################
## Validação
#################################################################################
$podePag	= true;
for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Resgata o perfil da conta
	#################################################################################
	$codPerfil	= ($contas[$i]->getCodContaPerfil()) ? $contas[$i]->getCodContaPerfil()->getCodigo() : 0;

	#################################################################################
	## Verifica se a conta pode ser confirmada
	#################################################################################
	if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $contas[$i]->getCodStatus()->getCodigo(), "CON")) {
		$podePag	= false;
	}
}

#################################################################################
## Verifica se a alguma conta não pode ser recebida
#################################################################################
if (!$podePag)	{
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Erro de violação de acesso, tentativa de confirmação indevida")));
	exit;
}


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($contas); $i++) {
		$conta		= new \Zage\Fin\ContaReceber();
		
		#################################################################################
		## Verifica se vai usar a Conta Original ou a informada
		#################################################################################
		if (isset($usaOrigConta) || (!empty($usaOrigConta))) {
			$codContaCre	= ($contas[$i]->getCodConta()) ? $contas[$i]->getCodConta()->getCodigo() : null; 
		}
		
		#################################################################################
		## Verifica se vai usar a Forma de pagamento Original ou a informada
		#################################################################################
		if (isset($usaOrigForma) || (!empty($usaOrigForma))) {
			$codFormaPag	= ($contas[$i]->getCodFormaPagamento()) ? $contas[$i]->getCodFormaPagamento()->getCodigo() : null;
		}
		
		#################################################################################
		## Verifica se vai usar a Data de vencimento Original ou a Data de Recebimento informada
		#################################################################################
		if (isset($usaOrigVenc) || (!empty($usaOrigVenc))) {
			$dataRec		= $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		#################################################################################
		## Calcula o valor pendente de recebimento
		#################################################################################
		$contaRec			= new \Zage\Fin\ContaReceber();
		$saldoDet			= $contaRec->getSaldoAReceberDetalhado($contas[$i]->getCodigo());
		if (!$contaRec->getValorJaRecebido($contas[$i]->getCodigo())) {
			$valor				= \Zage\App\Util::to_float($contas[$i]->getValor());
			$valorDesconto		= \Zage\App\Util::to_float($contas[$i]->getValorDesconto());
			$valorOutros		= \Zage\App\Util::to_float($contas[$i]->getValorOutros());
		}else{
			$valor				= \Zage\App\Util::to_float($saldoDet["PRINCIPAL"]);
			$valorDesconto		= 0;
			$valorOutros		= \Zage\App\Util::to_float($saldoDet["OUTROS"]);
		
			#################################################################################
			## Verificar se o outros valores foi pago no valor principal
			#################################################################################
			if (($valor < 0) && (($valor + $valorOutros) == 0)) {
				$valor			= 0;
				$valorOutros	= 0;
			}
		}
		
		#################################################################################
		## Verificar se a conta está atrasada e calcular o juros e mora caso existam
		#################################################################################
		if (\Zage\Fin\ContaReceber::estaAtrasada($contas[$i]->getCodigo(), $dataRec) == true) {
		
			#################################################################################
			## Calcula os valor através da data de referência
			#################################################################################
			$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($contas[$i]->getCodigo(), $dataRec);
			$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($contas[$i]->getCodigo(), $dataRec);
		
		}else{
			$valorJuros			= \Zage\App\Util::to_float($contas[$i]->getValorJuros());
			$valorMora			= \Zage\App\Util::to_float($contas[$i]->getValorMora());
		}
		
		#################################################################################
		## Atualiza o saldo a receber
		#################################################################################
		$valorJuros			+= $saldoDet["JUROS"];
		$valorMora			+= $saldoDet["MORA"];
		
		//$valor				= \Zage\App\Util::to_float($conta->getSaldoAReceber($contas[$i]->getCodigo()));
		
		#################################################################################
		## Efetiva o recebimento
		#################################################################################
		$erro		= $conta->recebe($contas[$i],$codContaCre,$codFormaPag,$dataRec,$valor,$valorJuros,$valorMora,$valorDesconto,$valorOutros,0,0,$documento,"MAN",null);
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
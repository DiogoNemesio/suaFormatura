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
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['codContaCre']))		$codContaCre		= \Zage\App\Util::antiInjection($_POST['codContaCre']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['dataRec']))			$dataRec			= \Zage\App\Util::antiInjection($_POST['dataRec']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['valorJuros']))		$valorJuros			= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora']))			$valorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['valorDesconto']))		$valorDesconto		= \Zage\App\Util::antiInjection($_POST['valorDesconto']);
if (isset($_POST['valorOutros']))		$valorOutros		= \Zage\App\Util::antiInjection($_POST['valorOutros']);
if (isset($_POST['documento']))			$documento			= \Zage\App\Util::antiInjection($_POST['documento']);
if (isset($_POST['flagPerdoa']))		$flagPerdoa			= \Zage\App\Util::antiInjection($_POST['flagPerdoa']);

$err	= null;

if (!isset($codConta) || empty($codConta)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (COD_CONTA)"))));
	$err	= 1;
}

if (!isset($codFormaPag) || empty($codFormaPag)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Forma de Pagamento não informado !!!"))));
	$err	= 1;
}

if (!isset($dataRec) || empty($dataRec)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros (DATA_REC)"))));
	$err	= 1;
}

if ( (!isset($valor) || empty($valor)) && (!isset($valorJuros) || empty($valorJuros)) && (!isset($valorMora) || empty($valorMora)) ) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pelo menos um dos valores deve ser informado !!"))));
	$err	= 1;
}

$valData	= new \Zage\App\Validador\DataBR();

if ($valData->isValid($dataRec) == false) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo DATA DE RECEBIMENTO inválida"))));
	$err	= 1;
}

#################################################################################
## Resgata as informações da conta
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codConta));

if (!$oConta) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Conta não encontrada (".$codConta.")"))));
	$err = 1;
}


if ($err) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Ajustar os valores
#################################################################################
if (empty($valor))			$valor				= 0;
if (empty($valorDesconto))	$valorDesconto		= 0;
if (empty($valorJuros))		$valorJuros			= 0;
if (empty($valorMora))		$valorMora			= 0;
if (empty($valorOutros))	$valorOutros		= 0;

$valor				= \Zage\App\Util::to_float($valor);
$valorDesconto		= \Zage\App\Util::to_float($valorDesconto);
$valorJuros			= \Zage\App\Util::to_float($valorJuros);
$valorMora			= \Zage\App\Util::to_float($valorMora);
$valorOutros		= \Zage\App\Util::to_float($valorOutros);

#################################################################################
## Ajustar valores das checkboxes
#################################################################################
$flagPerdoa		= (isset($flagPerdoa)) 		? 1 : 0;


#################################################################################
## Verificar se a conta está atrasada e calcular o júros e mora caso existam
#################################################################################
if (\Zage\Fin\ContaReceber::estaAtrasada($oConta->getCodigo(), $dataRec) == true) {

	#################################################################################
	## Calcula os valor através da data de referência
	#################################################################################
	$_valJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oConta->getCodigo(), $dataRec);
	$_valMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oConta->getCodigo(), $dataRec);
	
	#################################################################################
	## Verificar se foi dado desconto
	#################################################################################
	$valorDescJuros		= ($_valJuros > $valorJuros) 	? ($_valJuros	- $valorJuros)	: 0;
	$valorDescMora		= ($_valMora > $valorMora) 		? ($_valMora	- $valorMora)	: 0;
	
	#################################################################################
	## Verificar se foi perdoado o júros / mora
	#################################################################################
	$valorDescJuros		= ($flagPerdoa == 0)		? 0 : $valorDescJuros;
	$valorDescMora		= ($flagPerdoa == 0)		? 0 : $valorDescMora;
	
}else{
	$valorDescJuros		= 0;
	$valorDescMora		= 0;
}


#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	$conta		= new \Zage\Fin\ContaReceber();
	$erro		= $conta->recebe($oConta,$codContaCre,$codFormaPag,$dataRec,$valor,$valorJuros,$valorMora,$valorDesconto,$valorOutros,$valorDescJuros,$valorDescMora,$documento,"MAN",null);
	
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
 
echo '0'.\Zage\App\Util::encodeUrl('||'."Conta recebida com sucesso");
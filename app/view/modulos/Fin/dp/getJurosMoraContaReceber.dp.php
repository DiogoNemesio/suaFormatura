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
global $em,$system,$log,$tr;

#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codConta']))	$codConta			= \Zage\App\Util::antiInjection($_GET["codConta"]);
if (isset($_GET['dataRef']))	$dataRef			= \Zage\App\Util::antiInjection($_GET["dataRef"]);

#################################################################################
## Inicializar as variáveis
#################################################################################
$array					= array();
$array["valorJuros"]	= null;
$array["valorMora"]		= null;

#################################################################################
## Validação
#################################################################################
if (!isset($codConta) || empty($codConta)) _getJurosMoraContaReceberReturn();

#################################################################################
## Verificar se a data de referênciafoi informada, senão usar o dia de hoje
#################################################################################
if (!isset($dataRef) || empty($dataRef)) 	$dataRef	= date($system->config["data"]["dateFormat"]);

#################################################################################
## Validar a data de referência
#################################################################################
$valData	= new \Zage\App\Validador\DataBR();
if ($valData->isValid($dataRef) == false) _getJurosMoraContaReceberReturn();

#################################################################################
## Resgata as informaçoes da conta
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
if (!$oConta) _getJurosMoraContaReceberReturn();

#################################################################################
## Verificar se a conta está atrasada e calcular o júros e mora caso existam
#################################################################################
if (\Zage\Fin\ContaReceber::estaAtrasada($oConta->getCodigo(), $dataRef) == true) {

	#################################################################################
	## Calcula os valor através da data de referência
	#################################################################################
	$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oConta->getCodigo(), $dataRef);
	$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oConta->getCodigo(), $dataRef);

}else{
	$valorJuros		= 0;
	$valorMora		= 0;
}

#################################################################################
## Verificar se existe pendência de pagamento de júros / mora
#################################################################################
$saldoDet			= \Zage\Fin\ContaReceber::getSaldoAReceberDetalhado($oConta->getCodigo());

#################################################################################
## Atualiza o saldo a receber
#################################################################################
$valorJuros			+= $saldoDet["JUROS"];
$valorMora			+= $saldoDet["MORA"];

#################################################################################
## Atribui os valores de júros e mora ao array que será retornado 
#################################################################################
$array["valorJuros"]	= \Zage\App\Util::to_float(round($valorJuros,2));
$array["valorMora"]		= \Zage\App\Util::to_float(round($valorMora,2));


#################################################################################
## Retorna os valores calculados 
#################################################################################
_getJurosMoraContaReceberReturn();


#################################################################################
## Função para retornar o array no formato JSON 
#################################################################################
function _getJurosMoraContaReceberReturn() {
	global $array;
	echo json_encode($array);
	exit;
}
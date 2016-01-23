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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['tipo']))				$codTipo			= \Zage\App\Util::antiInjection($_POST['tipo']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['codAgencia']))		$codAgencia			= \Zage\App\Util::antiInjection($_POST['codAgencia']);
if (isset($_POST['saldoInicial']))	 	$saldoInicial		= \Zage\App\Util::antiInjection($_POST['saldoInicial']);
if (isset($_POST['dataInicial']))	 	$dataInicial		= \Zage\App\Util::antiInjection($_POST['dataInicial']);
if (isset($_POST['ccorrente']))	 		$ccorrente			= \Zage\App\Util::antiInjection($_POST['ccorrente']);
if (isset($_POST['ccorrenteDV'])) 		$ccorrenteDV		= \Zage\App\Util::antiInjection($_POST['ccorrenteDV']);
if (isset($_POST['ativa']))	 			$ativa				= \Zage\App\Util::antiInjection($_POST['ativa']);

if (isset($_POST['codCarteira'])) 		$codCarteira			= \Zage\App\Util::antiInjection($_POST['codCarteira']);
if (isset($_POST['valorBoleto'])) 		$valorBoleto			= \Zage\App\Util::antiInjection($_POST['valorBoleto']);

if (isset($_POST['codTipoMora'])) 		$codTipoMora		= \Zage\App\Util::antiInjection($_POST['codTipoMora']);
if (isset($_POST['codTipoJuros'])) 		$codTipoJuros		= \Zage\App\Util::antiInjection($_POST['codTipoJuros']);
if (isset($_POST['valorJuros'])) 		$valorJuros			= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora'])) 		$valorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['pctJuros'])) 			$pctJuros			= \Zage\App\Util::antiInjection($_POST['pctJuros']);
if (isset($_POST['pctMora'])) 			$pctMora			= \Zage\App\Util::antiInjection($_POST['pctMora']);
if (isset($_POST['instrucao'])) 		$instrucao			= \Zage\App\Util::antiInjection($_POST['instrucao']);
if (isset($_POST['codCedente'])) 		$codCedente			= \Zage\App\Util::antiInjection($_POST['codCedente']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** NOME **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'nome' => $nome ));

if (($oNome != null) && ($oNome->getCodigo() != $codConta)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe uma identificação cadastrada igual a informada. Por favor, informe outra para facilitar a utilização no sistema!"));
	$err 	= 1;
}

/** AGÊNCIA **/
if (($codTipo == "CC") && ( !isset($codAgencia) || empty($codAgencia))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione uma agência");
	$err	= 1;
}

/** CONTA **/
if ((!isset($ccorrente) || (empty($ccorrente)) && ($codTipo == 'CC'))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe atentamente o número da conta bancária.");
	$err	= 1;
}else{
	$oConta	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'ccorrente' => $ccorrente , 'codAgencia' => $codAgencia));
	
	if (($oConta != null) && ($oConta->getCodigo() != $codConta && $codTipo == 'CC')){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Esta conta já está cadastrada"));
		$err 	= 1;
	}
}

/** AJUSTANDO O VALOR DA MORA **/
if ($codTipoMora == 'V'){
	if ($valorMora){ 
		$valorMora	= \Zage\App\Util::to_float($valorMora);
	}else{
		$valorMora = 0;
	}
	$pctMora = 0;
}elseif ($codTipoMora == 'P'){
	if ($pctMora)	{
		$pctMora		= \Zage\App\Util::to_float(str_replace("%", "", $pctMora));
	}else{
		$pctMora = 0;
	}
	$valorMora = 0;
}

/** AJUSTANDO O VALOR DO JUROS **/
if ($codTipoJuros == 'V'){
	if ($valorJuros){
		$valorJuros		= \Zage\App\Util::to_float($valorJuros);
	}else{
		$valorJuros = 0;
	}
	$pctJuros = 0;
}elseif ($codTipoJuros == 'P'){
	if ($pctJuros)	{
		$pctJuros		= \Zage\App\Util::to_float(str_replace("%", "", $pctJuros));
	}else{
		$pctJuros = 0;
	}
	$valorJuros = 0;
}

/** SALDO INICIAL **/
if ($saldoInicial){
	$saldo		= \Zage\App\Util::to_float($saldoInicial);
}else{
	$saldo = 0;
}


/** Data 
if ( isset($dataInicial) &&  !empty($dataInicial)  ) {
	
	$valData	= new \Zage\App\Validador\DataBR();
	
	if ($valData->isValid($dataInicial) == false) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA INÍCIO inválido");
		$err	= 1;
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA INÍCIO é obrigatório");
	$err	= 1;
}
**/

if (isset($ativa) && (!empty($ativa))) {
	$ativa	= 1;
}else{
	$ativa	= 0;
}

/** Carteira **/
if (isset($codCarteira) && (!empty($codCarteira))) {
	$oCarteira	= $em->getRepository('Entidades\ZgfinCarteira')->findOneBy(array('codigo' => $codCarteira));
	if (!$oCarteira) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione uma Carteira válida");
		$err	= 1;
	}
}else{
	$oCarteira	= null;
}

/** VALOR BOLETO **/
if ($valorBoleto){
	$valorBoleto		= \Zage\App\Util::to_float($valorBoleto);
}else{
	$valorBoleto = null;
}

/** Apagar as informações de Boleto caso o tipo não seja CC **/
if ($codTipo	!== "CC") {
	$codAgencia		= null;
	$ccorrente		= null;
	$ccorrenteDV	= null;
	$instrucao		= null;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codConta) && (!empty($codConta))) {
 		$oConta	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codConta));
 		if (!$oConta) $oConta	= new \Entidades\ZgfinConta();
 	}else{
 		$oConta	= new \Entidades\ZgfinConta();
 	}
 	
 	if (!empty($dataInicial)) {
 		$dataInicial		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataInicial);
 	}else{
 		$dataInicial		= null;
 	}
 	
 	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
 	$oTipo		= $em->getRepository('Entidades\ZgfinContaTipo')->findOneBy(array('codigo' => $codTipo));
 	$oAge		= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codAgencia));
 	
 	
 	$oConta->setCodOrganizacao($oOrg);
 	$oConta->setCodTipo($oTipo);
 	$oConta->setNome($nome);
 	$oConta->setCodAgencia($oAge);
 	$oConta->setCcorrente($ccorrente);
 	$oConta->setCcorrenteDV($ccorrenteDV);
 	$oConta->setDataInicial($dataInicial);
 	$oConta->setSaldoInicial($saldo);
 	$oConta->setIndAtiva($ativa);
 	$oConta->setCodCarteira($oCarteira);
 	$oConta->setValorBoleto($valorBoleto);
 	$oConta->setValorJuros($valorJuros);
 	$oConta->setValorMora($valorMora);
 	$oConta->setPctJuros($pctJuros);
 	$oConta->setPctMora($pctMora);
 	$oConta->setInstrucao($instrucao);
 	$oConta->setCodigoCedente($codCedente);
 	
 	$em->persist($oConta);
 	$em->flush();
 	$em->detach($oConta);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConta->getCodigo());
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

if (isset($_POST['carteira'])) 			$carteira			= \Zage\App\Util::antiInjection($_POST['carteira']);
if (isset($_POST['valorJuros'])) 		$valorJuros			= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora'])) 		$valorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['pctJuros'])) 			$pctJuros			= \Zage\App\Util::antiInjection($_POST['pctJuros']);
if (isset($_POST['pctMora'])) 			$pctMora			= \Zage\App\Util::antiInjection($_POST['pctMora']);
if (isset($_POST['instrucao'])) 		$instrucao			= \Zage\App\Util::antiInjection($_POST['instrucao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** Agência **/
if ( ($codTipo == "CC") && ( !isset($codAgencia) || empty($codAgencia) )   ) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo AGÊNCIA é obrigatório");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'nome' => $nome ));

if (($oNome != null) && ($oNome->getCodigo() != $codConta)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("NOME da conta já existe"));
	$err 	= 1;
}

/** Ajustando os valores para o formato do banco **/
$saldo		= \Zage\App\Util::toMysqlNumber($saldoInicial);
$valorJuros	= \Zage\App\Util::toMysqlNumber($valorJuros);
$valorMora	= \Zage\App\Util::toMysqlNumber($valorMora);

if ($pctJuros)	{
	$pctJuros		= \Zage\App\Util::toMysqlNumber(str_replace("%", "", $pctJuros));
}else{
	$pctJuros		= 0;
}

if ($pctMora)	{
	$pctMora		= \Zage\App\Util::toMysqlNumber(str_replace("%", "", $pctMora));
}else{
	$pctMora		= 0;
}


if (!$saldo)		$saldo			= 0;
if (!$valorJuros)	$valorJuros		= 0;
if (!$valorMora)	$valorMora		= 0;


/** Data **/
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


if (isset($ativa) && (!empty($ativa))) {
	$ativa	= 1;
}else{
	$ativa	= 0;
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
 	
 	$oFil		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
 	$oTipo		= $em->getRepository('Entidades\ZgfinContaTipo')->findOneBy(array('codigo' => $codTipo));
 	$oAge		= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codAgencia));
 	
 	$oConta->setcodOrganizacao($oFil);
 	$oConta->setCodTipo($oTipo);
 	$oConta->setNome($nome);
 	$oConta->setCodAgencia($oAge);
 	$oConta->setCcorrente($ccorrente);
 	$oConta->setCcorrenteDV($ccorrenteDV);
 	$oConta->setDataInicial($dataInicial);
 	$oConta->setSaldoInicial($saldo);
 	$oConta->setIndAtiva($ativa);
 	$oConta->setCarteira($carteira);
 	$oConta->setValorJuros($valorJuros);
 	$oConta->setValorMora($valorMora);
 	$oConta->setPctJuros($pctJuros);
 	$oConta->setPctMora($pctMora);
 	$oConta->setInstrucao($instrucao);
 	
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
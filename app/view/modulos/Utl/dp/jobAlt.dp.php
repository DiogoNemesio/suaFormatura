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
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codJob']))				$codJob				= \Zage\App\Util::antiInjection($_POST['codJob']);
if (isset($_POST['nome']))					$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['codAtividade']))			$codAtividade		= \Zage\App\Util::antiInjection($_POST['codAtividade']);
if (isset($_POST['codModulo']))				$codModulo			= \Zage\App\Util::antiInjection($_POST['codModulo']);
if (isset($_POST['comando']))				$comando			= \Zage\App\Util::antiInjection($_POST['comando']);
if (isset($_POST['indAtivo']))				$indAtivo			= \Zage\App\Util::antiInjection($_POST['indAtivo']);
if (isset($_POST['dataPrxExe']))			$dataPrxExe			= \Zage\App\Util::antiInjection($_POST['dataPrxExe']);
if (isset($_POST['intervalo']))				$intervalo			= \Zage\App\Util::antiInjection($_POST['intervalo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codJob)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de parâmetros !!"));
	$err	= 1;
}

/** Nome **/
if (!isset($nome) || empty($nome)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Nome é obrigatório !!"));
	$err	= 1;
}elseif ((!empty($ident)) && (strlen($ident) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome não deve conter mais de 60 caracteres!"));
	$err	= 1;
}

/** Atividade **/
if (!isset($codAtividade) || empty($codAtividade)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Atividade é obrigatório !!"));
	$err	= 1;
}

$oAtividade	= $em->getRepository('Entidades\ZgutlAtividade')->findOneBy(array('codigo' => $codAtividade));
if (!$oAtividade) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Atividade não encontrada !!"));
	$err	= 1;
}

/** Módulo **/
if (!isset($codModulo) || empty($codModulo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Módulo é obrigatório !!"));
	$err	= 1;
}

$oModulo	= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $codModulo));
if (!$oModulo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Módulo não encontrado !!"));
	$err	= 1;
}

/** Comando **/
if (!isset($comando) || empty($comando)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Comando é obrigatório !!"));
	$err	= 1;
}

/** Verificar se o comando existe **/
if (!defined ( 'JOB_PATH' )) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Constante JOB_PATH não definida !!"));
	$err	= 1;
}else{
	$jobComand	= JOB_PATH . '/' . $comando;
	
	/** Verificar se o comando existe **/
	if (!file_exists($jobComand)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Arquivo não encontrado em ".JOB_PATH." !!"));
		$err	= 1;
	}
}

/** IndAtivo **/
if (isset($indAtivo) && (!empty($indAtivo))) {
	$indAtivo	= 1;
}else{
	$indAtivo	= 0;
}

/** Data Próxima execução **/
if ((!isset($dataPrxExe) || empty($dataPrxExe)) && ($indAtivo == 1)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Data Próxima execução é obrigatório !!"));
	$err	= 1;
}else{
	try{
		$oDataPrxExe		= DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $dataPrxExe);
	} catch (\Exception $e) {
	 	$system->criaAviso("Campo Data Próxima Execução inválido !!");
	 	$err	= 1;
	}
}


/** Intervalo **/
if (!isset($intervalo) || empty($intervalo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Intervalo é obrigatório !!"));
	$err	= 1;
}

try{
	$oInterval		= DateInterval::createFromDateString($intervalo);
} catch (\Exception $e) {
	$system->criaAviso("Campo Intervalo inválido !!");
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codJob) && (!empty($codJob))) {
 		$oJob	= $em->getRepository('Entidades\ZgutlJob')->findOneBy(array('codigo' => $codJob));
 		if (!$oJob) $oJob	= new \Entidades\ZgutlJob();
 	}else{
 		$oJob	= new \Entidades\ZgutlJob();
 	}
 	
 	$oJob->setCodAtividade($oAtividade);
 	$oJob->setNome($nome);
 	$oJob->setCodModulo($oModulo);
 	$oJob->setComando($comando);
 	$oJob->setDataProximaExecucao($oDataPrxExe);
 	$oJob->setIndAtivo($indAtivo);
 	$oJob->setIntervalo($intervalo);
 	
 	$em->persist($oJob);
 	$em->flush();
 	$em->detach($oJob);
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oJob->getCodigo());

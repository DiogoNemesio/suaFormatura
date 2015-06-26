<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	define('DOC_ROOT', realpath(dirname( __FILE__ ) . '/../') . "/" );
	include_once(DOC_ROOT . 'includeNoAuth.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$log;

#################################################################################
## Verifica se os argumentos passados são válidos
#################################################################################
if (!$argv[1]) exit;
if (!is_numeric($argv[1])) exit;

#################################################################################
## Salvar a data de início do job
#################################################################################
$dataInicio		= new \DateTime();
$dataProxima	= new \DateTime();

#################################################################################
## Verifica se o job existe
#################################################################################
$oJob	= $em->getRepository('\Entidades\ZgutlJob')->findOneBy(array('codigo' => $argv[1]));
if (!$oJob)	exit;

#################################################################################
## Verifica se o job já está em execução
#################################################################################
//if ($oJob->getIndExecutando() == 1) exit;

#################################################################################
## Verifica se o comando do Job está disponível
#################################################################################
$codJob			= $oJob->getCodigo();
$log->info("Job: ($codJob) iniciado");
$phpCmd		= JOB_PATH . "./" . $oJob->getComando();
if (!is_readable($phpCmd))	{
	$log->err("Job: ($codJob) não pode ser executado, pois o comando (".$phpCmd.") não foi encontrado !!!, o job será desabilitado");
	\Zage\Utl\Job::desabilitaJob($codJob);
	exit(1);
}

#################################################################################
## Altera o status do Job para "Em execução"
#################################################################################
$oStatus		= $em->getRepository('\Entidades\ZgutlJobStatusTipo')->findOneBy(array('codigo' => 'EE'));
$oJob->setIndExecutando(1);

#################################################################################
## Gerar o histórico de execução
#################################################################################
$hist			= new \Entidades\ZgutlJobHistorico();
$hist->setCodJob($oJob);
$hist->setDataInicio($dataInicio);
$hist->setCodStatus($oStatus);

try {
	$em->persist($oJob);
	$em->persist($hist);
	$em->flush();

} catch (\Exception $e) {
	$log->debug("Erro ao atualiza o status do job: (".$codJob.") ".$e->getMessage());
	die($e->getMessage());
}


#################################################################################
## Calcula a data da Próxima execução do Job
#################################################################################
try {
	$interval	= \DateInterval::createFromDateString($oJob->getIntervalo());
} catch (\Exception $e) {
	$log->err("Job: ($codJob) Erro ao calcular a data da próxima execução: ".$e->getMessage());
	\Zage\Utl\Job::desabilitaJob($codJob);
	die($e->getMessage());
}

#################################################################################
## Faz o Fork (inicia o processo com o PID diferente)
#################################################################################
/*$pid 	= pcntl_fork();
if ($pid == -1) {
	$log->err("Job: ($codJob) não pode iniciar o comando pcntl_fork() !!!, o job será desabilitado");
	\Zage\Utl\Job::desabilitaJob($codJob);
	exit(1);
}*/

#################################################################################
## Executa o comando
#################################################################################
$comando	= "php $phpCmd";
$log->info("Job: ($codJob) executando o comando: ".$comando);
exec ($comando, $saida,$codigoSaida);
$dataFim		= new \DateTime();

if ($codigoSaida == 0) {
	$oStatus		= $em->getRepository('\Entidades\ZgutlJobStatusTipo')->findOneBy(array('codigo' => 'OK'));
}else{
	$oStatus		= $em->getRepository('\Entidades\ZgutlJobStatusTipo')->findOneBy(array('codigo' => 'ER'));
	$numFalhas		= $oJob->getNumFalhas() + 1;
	$oJob->setNumFalhas($numFalhas);
}

$log->info("Job: ($codJob) codigo de saida: $codigoSaida");
#################################################################################
## Formata o retorno
#################################################################################
if (is_array($saida)) {
	$retorno	= implode(PHP_EOL,$saida);
}else{
	$retorno	= $saida;
}

$log->info("Job: ($codJob) Retorno:  $retorno");

#################################################################################
## Atualiza o histórico da execução
#################################################################################
$hist->setDataFim($dataFim);
$hist->setCodStatus($oStatus);
$hist->setRetorno($retorno);

#################################################################################
## Atualiza o job
#################################################################################
$numExe			= $oJob->getNumExecucoes() + 1;
$dataProxima->add($interval);
$oJob->setNumExecucoes($numExe);
$oJob->setIndExecutando(0);
$oJob->setDataUltimaExecucao($dataInicio);
$oJob->setDataProximaExecucao($dataProxima);

try {
	$em->persist($oJob);
	$em->persist($hist);
	$em->flush();
	$em->clear();

} catch (\Exception $e) {
	$log->debug("Erro ao atualiza o job: (".$codJob.") ".$e->getMessage());
	die($e->getMessage());
}

?>
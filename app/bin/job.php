#!/usr/bin/php
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
## Resgatar os jobs marcados para execução
#################################################################################
$log->debug("Job executado");
$jobs	= \Zage\Utl\Job::listaJobsAexecutar();
for ($i = 0; $i < sizeof($jobs); $i++)	{
	system('php '.EXE_PATH . '/jobRun.php '.$jobs[$i]->getCodigo() . ' > /dev/null &');
}

?>
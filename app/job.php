<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php.php');
}else{
	include_once('./includeNoAuth.php');
}

global $em,$system;


$jobs	= \Zage\Utl\Job::listaJobsAexecutar();


for ($i = 0; $i < sizeof($jobs); $i++)	{
	echo "JOB: ".$jobs[$i]->getComando();
}

?>
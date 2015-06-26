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
## Busca os arquivos que ainda não foram importados
#################################################################################
$oCodTipoArquivo	= "";
$oJob	= $em->getRepository('\Entidades\ZgutlJob')->findOneBy(array('codigo' => $argv[1]));

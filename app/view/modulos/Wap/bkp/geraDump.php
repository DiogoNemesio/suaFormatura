<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}
require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");


#################################################################################
## Parâmetros para resgate de números
#################################################################################
$servidores 	= array (3);
$chips			= array	(10);

$dir			= realpath ( dirname ( __FILE__ ));
$wn				= new \Zage\Wap\Numero();

foreach ($servidores as $servidor) {
	$oServ		= $em->getRepository('Entidades\ZgwapServidor')->findOneBy(array( 'codigo' => $servidor));
	
	$arquivo	= $dir . "/dump_".$servidor.".sql";
	$prefixos	= $wn->listaPrefixosServidor($servidor,$chips);
	
	$sqlPre		= "COD_PREFIXO IN (";
	if ($prefixos) {
		foreach ($prefixos as $prefixo) {
			$sqlPre .= $prefixo->getCodigo().",";
		}
		$sqlPre = substr($sqlPre, 0, -1);
	}
	
	$sqlPre		.= ")";
	$comando	= 'mysqldump DBApp ZGWAP_NUMERO --where="'.$sqlPre.'" --no-create-info -c > '.$arquivo;
	//echo "Comando: $comando\n";
	system($comando);
	
}

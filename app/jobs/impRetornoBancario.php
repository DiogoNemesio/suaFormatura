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
global $em,$system,$tr,$log;

#################################################################################
## Busca os arquivos que ainda não foram importados
#################################################################################
$codTipoArquivo		= "RTB";
$codStatus			= "A";
$atividade			= "IMP_RET_BANCARIO";
$codAtividade		= \Zage\Utl\Atividade::buscaPorIdentificacao($atividade);
if (!$codAtividade)	{
	$log->err("Atividade '".$atividade."' não encontrada !! (".__FILE__.")");
	exit;
}
$fila				= $em->getRepository('\Entidades\ZgappFilaImportacao')->findBy(array('codStatus' => $codStatus,'codTipoArquivo' => $codTipoArquivo ,'codAtividade' => $codAtividade),array('dataImportacao' => "ASC"));

for ($i = 0; $i < sizeof($fila); $i++) {
	
	$classe	= "\\Zage\\Fin\\Arquivos\\Layout\\".$fila[$i]->getVariavel();
	$file	= CLASS_PATH . "/Zage/Fin/Arquivos/Layout/".$fila[$i]->getVariavel().'.php';
	
	
	if (!file_exists($file)) {
		$log->err("Classe não encontrada (".$file."), código da fila: ".$fila[$i]->getCodigo());
	}else{
		$layout	= new $classe;
		$layout->loadFile($fila[$i]->getArquivo());
	}
	
}
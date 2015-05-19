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
if (isset($_POST['codTipoDoc'])) 		$codTipoDoc	= \Zage\App\Util::antiInjection($_POST['codTipoDoc']);


#################################################################################
## Checar se a versão existe
#################################################################################
if (!isset($codTipoDoc)) {
	$log->debug("Tipo de Documento não informado");
	die ("Falta de Parâmetros (codTipoDoc)");
}

#################################################################################
## Checar se o arquivo foi passado
#################################################################################
if (!isset($_FILES["userfile"])) {
	$log->debug("Variável de arquivo não informada");
	die ("Variável de arquivo não informada");
}

#################################################################################
## Resgatar os parâmetros do arquivo
#################################################################################
$fileName	= str_replace("#", "", $_FILES["userfile"]['name']);
$tempLoc	= $_FILES["userfile"]['tmp_name'];
$erro		= $_FILES["userfile"]['error'];
$type		= $_FILES["userfile"]['type'];

if ($erro) {
	die($erro);
}

if (!is_uploaded_file($tempLoc)) {
	$log->err($tr->trans('Arquivo não pode ser salvo, pois não foi transferido através de uma requisição POST HTTP'));
	echo $tr->trans('Arquivo não pode ser salvo, pois não foi transferido através de uma requisição POST HTTP');
	exit;
}


#################################################################################
## Verifica que tipo de arquivo foi transferido para fazer o tratamento correto
#################################################################################
if ($type == "application/zip") {
	$log->debug($tr->trans("Tipo de arquivo não implementado"));
	die ($tr->trans("Tipo de arquivo não implementado"));
}else {
	
	#################################################################################
	## Criar o documento
	#################################################################################
	$codDoc		= \Zage\Doc\Documento::cria($codTipoDoc);
	
	if (!is_numeric($codDoc)) {
		$log->debug("CodDoc: $codDoc");
		die ($codDoc);
	}
	
	//$log->debug("Associar o arquivo !!!");
	$codAssoc 	= \Zage\Doc\Documento::associaArquivo($codDoc, $tempLoc,$fileName,$type);

	if (!is_numeric($codAssoc)) {
		//$log->debug("Resultado: $codAssoc");
		die ($codAssoc);
	}
	
	//$log->debug("Arquivo associado com sucesso !!!");
	
}


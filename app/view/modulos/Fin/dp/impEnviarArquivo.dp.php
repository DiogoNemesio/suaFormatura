<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $em,$log,$system;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codTipoArquivoLayout']))	$codTipoArquivoLayout	= \Zage\App\Util::antiInjection($_POST['codTipoArquivoLayout']);

$log->debug(serialize($_FILES));

#################################################################################
## Checar se o Layout existe
#################################################################################
if (!isset($codTipoArquivoLayout)) exit;
$oTipoLayout	= $em->getRepository('Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $codTipoArquivoLayout));
if (!$oTipoLayout)	die('Layout não encontrado !!!');

#################################################################################
## Checar se o arquivo foi passado
#################################################################################
if (!isset($_FILES["userfile"])) exit;

#################################################################################
## Resgatar os parâmetros do arquivo
#################################################################################
$fileName	= str_replace("#", "", $_FILES["userfile"]['name']);
$tempLoc	= $_FILES["userfile"]['tmp_name'];
$erro		= $_FILES["userfile"]['error'];
$type		= $_FILES["userfile"]['type'];

#################################################################################
## Validar o nome do arquivo
#################################################################################
if ( (strpos($fileName, "..") !== false) || (strpos($fileName, "/") !== false)) { 
	die("Arquivo com nome inválido");
}

if ($erro) {
	die($erro);
}

#################################################################################
## Validar os parâmetros
#################################################################################
if (!isset($system->config["arquivos"]["caminho"])) die("Caminho temporário dos arquivos não configurado <arquivos><caminho></caminho></arquivos>");
$target_path	= DOC_ROOT . '/' .$system->config["arquivos"]["caminho"];
$target_path	= $target_path . basename($fileName);

if (move_uploaded_file($tempLoc,$target_path)) {
	$log->debug('Arquivo salvo com sucesso ('.$target_path.')');
} else {
	$log->err('Transferência do arquivo ('.$target_path.') falhou !!!');
	echo "Transferência falhou";
	exit;	
}


#################################################################################
## Validar o tipo do arquivo
#################################################################################
$types 	= explode("/", $type);

if (sizeof($types) != 2) {
	die('Tipo de arquivo inconsistente !!! ('.$type.') ('.$target_path.')');
}

#################################################################################
## Verifica que tipo de arquivo foi transferido para fazer o tratamento correto
#################################################################################
if (($type == "application/octet-stream") || ($type == "text/plain") || ($type == "application/download") || (($types[0] == "application"  && is_numeric($types[1])) )  ) {

	try {
		\Zage\App\Fila::cadastrar("Fin", $target_path, "RTB", "IMP_RET_BANCARIO");
		$em->flush();
		$em->clear();
	} catch (\Exception $e) {
		die ($e->getMessage());
	}
	
	$log->debug("Arquivo salvo na fila !!!");
}else{
	die('Tipo de arquivo não reconhecido !!! ('.$type.') ('.$target_path.')');
}


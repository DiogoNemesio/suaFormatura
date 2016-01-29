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
if (isset($_POST['codConta']))				$codConta				= \Zage\App\Util::antiInjection($_POST['codConta']);

#################################################################################
## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
#################################################################################
$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

#################################################################################
## Checar se a conta existe, e se o usuário tem acesso a essa conta
#################################################################################
$aOrg			= array($system->getCodOrganizacao());
if ($oFmtAdm)	$aOrg[]		= $oFmtAdm->getCodigo(); 
$oConta			= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $aOrg),array('nome' => 'ASC'));
$aConta			= array();
for ($i = 0; $i < sizeof($oConta); $i++) {
	$aConta[$oConta[$i]->getCodigo()]		= $oConta[$i]->getCodigo();
}
if (array_key_exists($codConta, $aConta) == false) die('Conta Corrente não encontrada !!!');


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
## Configurações para salvar na fila
#################################################################################
$codAtividade		= "IMP_RET_BANCARIO_BOLETO";
$codTipoArq			= "RTB";
$codModulo			= "Fin";

#################################################################################
## Validar os parâmetros
#################################################################################
if (!isset($system->config["arquivos"]["caminho"])) die("Caminho temporário dos arquivos não configurado <arquivos><caminho></caminho></arquivos>");
$baseDir		= DOC_ROOT . '/' .$system->config["arquivos"]["caminho"] . "/".$system->getCodOrganizacao() . "/";
$target_path	= $baseDir . basename($fileName);

#################################################################################
## Verificar se o diretório base existe
#################################################################################
if (\Zage\App\Util::existeDiretorio($baseDir) == false) {

	#################################################################################
	## Criar o diretório caso não exista
	#################################################################################
	mkdir($baseDir, 0755);
	
}

if (move_uploaded_file($tempLoc,$target_path)) {
	$log->debug('Arquivo salvo com sucesso ('.$target_path.') importação de arquivos financeiro da conta: '.$codConta);
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

	#################################################################################
	## Detectar o tipo de arquivo através da quantidade de caracteres da primeira linha
	#################################################################################
	if($f = fopen($target_path, 'r')){
		$primeiraLinha 	= fgets($f); // read until first newline
		$primeiraLinha	= str_replace(array("\n", "\r"), '', $primeiraLinha);
		fclose($f);
		$numChars		= mb_strlen($primeiraLinha,$system->config["database"]["charset"]);
		
		if ($numChars == 240) {
			$codTipoArquivoLayout	= "BOL_T240";	
		}else if ($numChars == 400) {
			$codTipoArquivoLayout	= "BOL_T400";
		}else{
			$log->err("0x7jamvb3H: Tipo de arquivo não detectado, número de caracteres da primeira linha: ".$numChars);
			die("0x7jamvb3H: Tipo de arquivo não detectado");
		}

		#################################################################################
		## Checar se o Layout existe
		#################################################################################
		if (!isset($codTipoArquivoLayout)) exit;
		$oTipoLayout	= $em->getRepository('Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $codTipoArquivoLayout));
		if (!$oTipoLayout)	die('Layout não encontrado !!!');
	}else{
		die('Não foi possível ler o arquivo transferido');
	}
	
	
	try {
		
		$variavel 	= $codTipoArquivoLayout . "|". $codConta;
		
		\Zage\App\Fila::cadastrar($codModulo, $target_path, $codTipoArq, $codAtividade,$variavel);
		$em->flush();
		$em->clear();
	} catch (\Exception $e) {
		die ($e->getMessage());
	}
	
	$log->debug("Arquivo salvo na fila !!!");
}else{
	die('Tipo de arquivo não reconhecido !!! ('.$type.') ('.$target_path.')');
}


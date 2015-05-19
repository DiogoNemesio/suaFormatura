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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}elseif (isset($fileId)) 	{
	$id = $fileId;
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (!isset($codArquivo)) \Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codArquivo')));

#################################################################################
## Resgatar o arquivo
#################################################################################
try {
	$file	= $em->getRepository('Entidades\ZgdocArquivo')->findOneBy(array('codArquivoInfo' => $codArquivo));
	
	if (!$file) {
		exit;
	}else{
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
header('Content-type: '.$file->getCodArquivoInfo()->getMimetype());
header('Content-disposition: attachment; filename="'.$file->getCodArquivoInfo()->getNome().'"');
header('Content-Length: ' . $file->getCodArquivoInfo()->getTamanho());
echo stream_get_contents($file->getArquivo());
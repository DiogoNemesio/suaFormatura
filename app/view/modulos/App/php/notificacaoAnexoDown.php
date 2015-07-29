<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log,$db;

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ($id);

#################################################################################
## Verifica se os parâmetros foram passados
#################################################################################
if (!isset($codNotificacao)) exit;
if (!isset($codAnexo)) exit;

#################################################################################
## Resgatar o arquivo
#################################################################################
try {
	$file	= $em->getRepository('\Entidades\ZgappNotificacaoAnexo')->findOneBy(array('codigo' => $codAnexo));
	
	if (!$file) {
		exit;
	}
	
	/*if (function_exists("mb_strlen")) {
		$tamanho	= mb_strlen($file->getAnexo());
	}else{
		$tamanho	= strlen($file->getAnexo());
	}*/
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
header('Content-type: application/octet-stream');
header('Content-disposition: attachment; filename="'.$file->getNome().'"');
//header('Content-Length: ' . $tamanho);
echo stream_get_contents($file->getAnexo());
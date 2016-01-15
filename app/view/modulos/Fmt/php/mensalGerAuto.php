<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;


#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;


#################################################################################
## Resgata a variável FID com a lista de formandos selecionados
#################################################################################
if (isset($_GET['fid']))	$fid = \Zage\App\Util::antiInjection($_GET["fid"]);
if (!isset($fid))			\Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Descompacta o FID
#################################################################################
\Zage\App\Util::descompactaId($fid);

if (!isset($aSelFormandos))			\Zage\App\Erro::halt('Falta de Parâmetros 3');

#################################################################################
## Gera o array de formandos selecionados a partir da string
#################################################################################
$aSelFormandos						= explode(",",$aSelFormandos);


$log->info("GET: ".serialize($_GET));
$log->info("aSelFormandos: ".serialize($aSelFormandos));
var_dump($aSelFormandos);


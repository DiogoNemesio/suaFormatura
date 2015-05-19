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
}elseif (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
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
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['codTipo']))		$codTipo		= \Zage\App\Util::antiInjection($_GET['codTipo']);
if (isset($_GET['codLocal']))		$codLocal		= \Zage\App\Util::antiInjection($_GET['codLocal']);
if (isset($_GET['rua']))	 		$rua			= \Zage\App\Util::antiInjection($_GET['rua']);
if (isset($_GET['estante']))	 	$estante		= \Zage\App\Util::antiInjection($_GET['estante']);
if (isset($_GET['prateleira']))	 	$prateleira		= \Zage\App\Util::antiInjection($_GET['prateleira']);
if (isset($_GET['coluna']))	 		$coluna			= \Zage\App\Util::antiInjection($_GET['coluna']);

if (!isset($codTipo) || !isset($codLocal)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


if (!isset($rua))			$rua		= null;
if (!isset($estante))		$estante	= null;
if (!isset($prateleira))	$prateleira	= null;
if (!isset($coluna))		$coluna		= null;

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/enderecoLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('TITULO'				,$tr->trans('Geração de Endereços'));
$tpl->set('ID'					,$id);
$tpl->set('COD_LOCAL'			,$codLocal);
$tpl->set('COD_TIPO'			,$codTipo);
$tpl->set('RUA'					,$rua);
$tpl->set('ESTANTE'				,$estante);
$tpl->set('PRATELEIRA'			,$prateleira);
$tpl->set('COLUNA'				,$coluna);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


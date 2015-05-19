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
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$_SESSION["_TRLIS_codFormaPagFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Origem
#################################################################################
try {
	$aConta			= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codFilial' => $system->getCodEmpresa()),array('nome' => 'ASC'));
	$oContaOrig		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$_SESSION["_TRLIS_codContaOrigFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Destino
#################################################################################
try {
	$oContaDest		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$_SESSION["_TRLIS_codContaDestFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$_SESSION["_TRLIS_codStatusFiltro"], null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Definir a URL do filtro
#################################################################################
$urlFiltro		= ROOT_URL . "/Fin/transferenciaLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'				,$id);
$tpl->set('TITULO'			,'Pesquisa de transferências');
$tpl->set('FILTER_URL'		,$urlFiltro);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('DP_MODAL'		,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('STATUS'			,$oStatus);
$tpl->set('FORMAS_PAG'		,$oFormaPag);
$tpl->set('CONTAS_ORIG'		,$oContaOrig);
$tpl->set('CONTAS_DEST'		,$oContaDest);
$tpl->set('VALOR_INI'		,$_SESSION["_TRLIS_valorIniFiltro"]);
$tpl->set('VALOR_FIM'		,$_SESSION["_TRLIS_valorFimFiltro"]);
$tpl->set('DESCRICAO'		,$_SESSION["_TRLIS_descricaoFiltro"]);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


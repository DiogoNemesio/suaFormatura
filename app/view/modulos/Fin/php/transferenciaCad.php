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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Select da Moeda
#################################################################################
try {
	$aMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findBy(array(),array('descricao' => 'ASC'));
	$oMoeda		= $system->geraHtmlCombo($aMoeda,	'CODIGO', 'DESCRICAO',	null, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	null, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Débito/Crédito
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Período de recorrência
#################################################################################
try {
	$aPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findBy(array(),array('descricao' => 'ASC'));
	$oPeriodo	= $system->geraHtmlCombo($aPeriodo,	'CODIGO', 'DESCRICAO',	"M", null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar		= ROOT_URL.'/Fin/transferenciaLis.php?id='.$id;
$urlAdd			= ROOT_URL.'/Fin/transferenciaCad.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'						,$id);
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('URL_ADD'					,$urlAdd);
$tpl->set('DATA_VENC'				,date($system->config["data"]["dateFormat"]));
$tpl->set('MOEDAS'					,$oMoeda);
$tpl->set('FORMAS_PAG'				,$oFormaPag);
$tpl->set('CONTAS'					,$oConta);
$tpl->set('PERIODOS_REC'			,$oPeriodo);
$tpl->set('FORMATO_DATA'			,$system->config["data"]["jsDateFormat"]);
$tpl->set('APP_BS_TA_MINLENGTH'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'			,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'						,ROOT_URL.'/Fin/transferenciaAlt.dp.php');

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

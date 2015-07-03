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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codTransf)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codTransf));

if (!$oTransf) {
	\Zage\App\Erro::halt('Transferência não encontrada');
	
}

#################################################################################
## Valida o status da conta
#################################################################################
switch ($oTransf->getCodStatus()->getCodigo()) {
	case "P":
	case "PA":
		$podePag	= true;
		break;
	default:
		$podePag	= false;
		break;
}

if (!$podePag) {
	\Zage\App\Erro::halt($tr->trans('Transferência não pode ser realizada, status não permitido (%s)',array('%s' => $oTransf->getCodStatus()->getCodigo())));
}

$codFormaPag		= ($oTransf->getCodFormaPagamento() != null) ? $oTransf->getCodFormaPagamento()->getCodigo() : null;
$codContaOrig		= ($oTransf->getCodContaOrigem() != null) ? $oTransf->getCodContaOrigem()->getCodigo() : null;
$codContaDest		= ($oTransf->getCodContaDestino() != null) ? $oTransf->getCodContaDestino()->getCodigo() : null;

$dataTransf			= ($oTransf->getDataTransferencia() != null) 	? $oTransf->getDataTransferencia()->format($system->config["data"]["dateFormat"]) : null;

$transf			= new \Zage\Fin\Transferencia();

if (!$transf->getValorJaTransferido($codTransf)) {
	$valor				= \Zage\App\Util::toPHPNumber($oTransf->getValor());
}else{
	$valor				= \Zage\App\Util::toPHPNumber($transf->getSaldoATransferir($codTransf));
}

$documento				= "";


if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/transferenciaLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$codFormaPag, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Origem
#################################################################################
try {
	$aConta			= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getcodOrganizacao()),array('nome' => 'ASC'));
	$oContaOrig		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaOrig, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Destino
#################################################################################
try {
	$oContaDest		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaDest, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Confirmação de transferência');
$tpl->set('COD_TRANSF'			,$codTransf);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS_ORIG'			,$oContaOrig);
$tpl->set('CONTAS_DEST'			,$oContaDest);
$tpl->set('DATA_TRANSF'			,$dataTransf);
$tpl->set('VALOR'				,$valor);
$tpl->set('DOCUMENTO'			,$documento);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
if (!empty($codTransf)) {
	try {
		$info = $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codTransf));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$codTransf		= $info->getCodigo();
	$numero			= $info->getNumero();
	$descricao		= $info->getDescricao();
	$parcela		= $info->getParcela();
	$codStatus		= ($info->getCodStatus() != null) ? $info->getCodStatus()->getCodigo() : null;
	$status			= ($info->getCodStatus() != null) ? $info->getCodStatus()->getDescricao() : null;
	$numParcelas	= $info->getNumParcelas();
	$codMoeda		= ($info->getCodMoeda() != null) ? $info->getCodMoeda()->getCodigo() : null;
	$valor			= \Zage\App\Util::toPHPNumber($info->getValor());
	$valorCancelado	= \Zage\App\Util::toPHPNumber($info->getValorCancelado());
	$dataEmissao	= ($info->getDataEmissao() != null) 		? $info->getDataEmissao()->format($system->config["data"]["dateFormat"]) : null;
	$dataRealizacao	= ($info->getDataRealizacao() != null) 		? $info->getDataRealizacao()->format($system->config["data"]["dateFormat"]) : null;
	$dataTransf		= ($info->getDataTransferencia() != null)	? $info->getDataTransferencia()->format($system->config["data"]["dateFormat"]) : null;
	$dataAut		= ($info->getDataAutorizacao() != null) 	? $info->getDataAutorizacao()->format($system->config["data"]["dateFormat"]) : null;
	$indAut			= ($info->getIndAutorizado()	== 1) ? "checked" : null;
	$documento		= $info->getDocumento();
	$codFormaPag	= ($info->getCodFormaPagamento() != null) ? $info->getCodFormaPagamento()->getCodigo() : null;
	$codGrupoTransf	= $info->getCodGrupoTransferencia();
	$codGrupoLanc	= $info->getCodGrupoLanc();
	$obs			= $info->getObservacao();
	$codTipoRec		= ($info->getCodTipoRecorrencia() != null) ? $info->getCodTipoRecorrencia()->getCodigo() : null;
	$codPeriodoRec	= ($info->getCodPeriodoRecorrencia() != null) ? $info->getCodPeriodoRecorrencia()->getCodigo() : null;
	$parcelaInicial	= $info->getParcelaInicial();
	$intervaloRec	= $info->getIntervaloRecorrencia();
	$codContaOrig	= ($info->getCodContaOrigem() != null) 	? $info->getCodContaOrigem()->getCodigo() : null;
	$codContaDest	= ($info->getCodContaDestino() != null)	? $info->getCodContaDestino()->getCodigo() : null;
	$indPagAuto		= $info->getIndTransferirAuto();

	
	if ($indPagAuto == 1) {
		$indPagAuto		= 'checked="checked"';
	}else{
		$indPagAuto		= "";
	}
	
	/** Cancelamento **/
	$hist			= $em->getRepository('Entidades\ZgfinTransferenciaHistCanc')->findOneBy(array('codTransferencia' => $codTransf));
	
	if ($hist) {
		$userCanc		= ($hist->getCodUsuario() != null) ? $hist->getCodUsuario()->getNome() : null;
		$motivoCanc		= $hist->getMotivo();
		$dataCanc		= ($info->getDataCancelamento() != null) 		? $info->getDataCancelamento()->format($system->config["data"]["dateFormat"]) : null;
		$valorCanc		= \Zage\App\Util::to_money($hist->getValor());
	}else{
		$userCanc		= null;
		$motivoCanc		= null;
		$dataCanc		= null;
		$valorCanc		= null;
	}
	
	
	$valorTotal			= \Zage\App\Util::to_money($info->getValor() - $info->getValorCancelado() );
	
	switch ($codStatus) {
		case "R":
			$view	= 1;
			break;
		default:
			if (!isset($view)) $view = 0;
			break;
			
	}

}else{
	$codTransf		= null;
	$numero			= null;
	$descricao		= null;
	$parcela		= 1;
	$codStatus		= null;
	$status			= null;
	$numParcelas	= 1;
	$codMoeda		= null;
	$valor			= null;
	$valorCancelado	= null;
	$dataEmissao	= null;
	$dataTransf		= null;
	$dataRealizacao	= null;
	$dataAut		= null;
	$indAut			= null;
	$documento		= null;
	$codFormaPag	= null;
	$codGrupoTransf	= null;
	$codGrupoLanc	= null;
	$obs			= null;
	$codTipoRec		= null;
	$codPeriodoRec	= 'M';
	$parcelaInicial	= 1;
	$intervaloRec	= null;
	$codContaOrig	= null;
	$codContaDest	= null;
	$indPagAuto		= 0;
	$chkU			= "checked";
	$chkP			= null;

	$userCanc		= null;
	$motivoCanc		= null;
	$dataCanc		= null;
	$valorCanc		= null;
	
	$valorTotal		= null;

}


#################################################################################
## Select da Moeda
#################################################################################
try {
	$aMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findBy(array(),array('descricao' => 'ASC'));
	$oMoeda		= $system->geraHtmlCombo($aMoeda,	'CODIGO', 'DESCRICAO',	$codMoeda, 		null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
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
	$aContaOrig		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oContaOrig		= $system->geraHtmlCombo($aContaOrig,	'CODIGO', 'NOME',	$codContaOrig, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Destino
#################################################################################
try {
	$aContaDest		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oContaDest		= $system->geraHtmlCombo($aContaDest,	'CODIGO', 'NOME',	$codContaDest, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Período de ocorrência
#################################################################################
try {
	$aPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findBy(array(),array('descricao' => 'ASC'));
	$oPeriodo	= $system->geraHtmlCombo($aPeriodo,	'CODIGO', 'DESCRICAO',	$codPeriodoRec, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Controle de edição
#################################################################################
if (isset($view) && $view != 0) {
	$tView	= "readonly";
	$bView	= "disabled";
	$sView	= "readonly";
	$hView	= "hidden";
}else{
	$tView	= null;
	$bView	= null;
	$sView	= null;
	$hView	= null;
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/transferenciaLis.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTransf='.$codTransf);

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTransf=');
$urlNovo			= ROOT_URL."/Fin/transferenciaAlt.php?id=".$uid;

#################################################################################
## Resgata a url desse script
#################################################################################
$url			= ROOT_URL . "/Fin/". basename(__FILE__);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('ID'					,$id);
$tpl->set('UID'					,$uid);
$tpl->set('COD_TRANSF'			,$codTransf);
$tpl->set('NUMERO'				,$numero);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('PARCELA'				,$parcela);
$tpl->set('COD_STATUS'			,$codStatus);
$tpl->set('NUM_PARCELAS'		,$numParcelas);
$tpl->set('COD_MOEDA'			,$codMoeda);
$tpl->set('MOEDAS'				,$oMoeda);
$tpl->set('VALOR'				,$valor);
$tpl->set('VALOR_CANCELADO'		,$valorCancelado);
$tpl->set('VALOR_TOTAL'			,$valorTotal);
$tpl->set('DATA_EMISSAO'		,$dataEmissao);
$tpl->set('DATA_REALIZACAO'		,$dataRealizacao);
$tpl->set('DATA_TRANSF'			,$dataTransf);
$tpl->set('DATA_AUT'			,$dataAut);
$tpl->set('IND_AUT'				,$indAut);
$tpl->set('DOCUMENTO'			,$documento);
$tpl->set('COD_FORMA_PAG'		,$codFormaPag);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('COD_GRUPO_TRANSF'	,$codGrupoTransf);
$tpl->set('COD_GRUPO_LANC'		,$codGrupoLanc);
$tpl->set('OBS'					,$obs);
$tpl->set('CONTAS_ORIG'			,$oContaOrig);
$tpl->set('CONTAS_DEST'			,$oContaDest);
$tpl->set('COD_PERIODO_REC'		,$codPeriodoRec);
$tpl->set('COD_TIPO_REC'		,$codTipoRec);
$tpl->set('INTERVALO_REC'		,$intervaloRec);
$tpl->set('PARCELA_INICIAL'		,$parcelaInicial);
$tpl->set('FLAG_PAGAR_AUTO'		,$indPagAuto);

$tpl->set('PERIODOS_REC'		,$oPeriodo);
$tpl->set('TEXT_VIEW'			,$tView);
$tpl->set('BUTTON_VIEW'			,$bView);
$tpl->set('SELECT_VIEW'			,$sView);
$tpl->set('ESCONDIDO'			,$hView);
$tpl->set('DESC_STATUS'			,$status);
$tpl->set('URL_EDITAR'			,$url);

$tpl->set('MOTIVO_CANC'			,$motivoCanc);
$tpl->set('VALOR_CANC'			,$valorCanc);
$tpl->set('DATA_CANC'			,$dataCanc);
$tpl->set('USER_CANC'			,$userCanc);

$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'					,$_icone_);
$tpl->set('COD_MENU'			,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


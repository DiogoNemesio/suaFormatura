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
## Variáveis globais
#################################################################################
global $em,$system,$log,$tr;

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
if (!isset($codConta))	\Zage\App\Erro::halt('Falta de Parâmetros 2');
if (empty($codConta)) 	\Zage\App\Erro::halt('Conta não encontrada !!!');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$info = $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$codConta		= $info->getCodigo();
$numero			= $info->getNumero();
$descricao		= $info->getDescricao();
$codPessoa		= ($info->getCodPessoa() != null) ? $info->getCodPessoa()->getCodigo() : null;
$parcela		= $info->getParcela();
$codStatus		= ($info->getCodStatus() != null) ? $info->getCodStatus()->getCodigo() : null;
$status			= ($info->getCodStatus() != null) ? $info->getCodStatus()->getDescricao() : null;
$numParcelas	= $info->getNumParcelas();
$codMoeda		= ($info->getCodMoeda() != null) ? $info->getCodMoeda()->getCodigo() : null;
$valor			= \Zage\App\Util::toPHPNumber($info->getValor());
$valorJuros		= \Zage\App\Util::toPHPNumber($info->getValorJuros());
$valorMora		= \Zage\App\Util::toPHPNumber($info->getValorMora());
$valorDesconto	= \Zage\App\Util::toPHPNumber($info->getValorDesconto());
$valorCancelado	= \Zage\App\Util::toPHPNumber($info->getValorCancelado());
$valorOutros	= \Zage\App\Util::toPHPNumber($info->getValorOutros());
$dataEmissao	= ($info->getDataEmissao() != null) 		? $info->getDataEmissao()->format($system->config["data"]["dateFormat"]) : null;
$dataLiq		= ($info->getDataLiquidacao() != null) 		? $info->getDataLiquidacao()->format($system->config["data"]["dateFormat"]) : null;
$dataVenc		= ($info->getDataVencimento() != null) 		? $info->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
$dataAut		= ($info->getDataAutorizacao() != null) 	? $info->getDataAutorizacao()->format($system->config["data"]["dateFormat"]) : null;
$indAut			= ($info->getIndAutorizado()	== 1) ? "checked" : null;
$documento		= $info->getDocumento();
$codFormaPag	= ($info->getCodFormaPagamento() != null) ? $info->getCodFormaPagamento()->getCodigo() : null;
$nossoNumero	= $info->getNossoNumero();
$codGrupoConta	= $info->getCodGrupoConta();
$codGrupoLanc	= $info->getCodGrupoLanc();
$obs			= $info->getObservacao();
$codTipoRec		= ($info->getCodTipoRecorrencia() != null) ? $info->getCodTipoRecorrencia()->getCodigo() : null;
$codPeriodoRec	= ($info->getCodPeriodoRecorrencia() != null) ? $info->getCodPeriodoRecorrencia()->getCodigo() : null;
$parcelaInicial	= $info->getParcelaInicial();
$intervaloRec	= $info->getIntervaloRecorrencia();
$codContaRec	= ($info->getCodConta() != null) 	? $info->getCodConta()->getCodigo() : null;
$indRecAuto		= $info->getIndReceberAuto();

if ($indRecAuto == 1) {
	$indRecAuto		= 'checked="checked"';
}else{
	$indRecAuto		= "";
}

if ($info->getCodPessoa() != null) {
	if ($info->getCodPessoa()->getCodTipoPessoa()->getCodigo() == "F") {
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($info->getCodPessoa()->getCgc());
	}else{
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($info->getCodPessoa()->getCgc());
	}
	$pessoa			= $info->getCodPessoa()->getNome() . " (".$infoCgc.")";
	
	$nomePessoa		= $info->getCodPessoa()->getNome();
	$datacadPessoa	= ($info->getCodPessoa()->getDataCadastro() != null) ? $info->getCodPessoa()->getDataCadastro()->format($system->config["data"]["dateFormat"]) : null;
	
	
}else{
	$pessoa			= null;
	$nomePessoa		= null;
	$datacadPessoa	= null;
	$infoCgc		= null;
}

/** Cancelamento **/
$hist			= $em->getRepository('Entidades\ZgfinContaRecHistCanc')->findOneBy(array('codConta' => $codConta));

if ($hist) {
	$userCanc		= ($hist->getCodUsuario() != null) ? $hist->getCodUsuario()->getNome() : null;
	$motivoCanc		= $hist->getMotivo();
	$dataCanc		= ($info->getDataCancelamento() != null) 		? $info->getDataCancelamento()->format($system->config["data"]["dateFormat"]) : null;
	$valorCanc		= \Zage\App\Util::to_money($info->getValorCancelado());
}else{
	$userCanc		= null;
	$motivoCanc		= null;
	$dataCanc		= null;
	$valorCanc		= null;
}


$valorTotal			= \Zage\App\Util::to_money($info->getValor() + $info->getValorJuros() + $info->getValorMora() + $info->getValorOutros() - ($info->getValorCancelado() + $info->getValorDesconto()));

switch ($codStatus) {
	case "L":
		$view	= 1;
		break;
	default:
		if (!isset($view)) $view = 0;
		break;
		
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
## Select da Conta de Crédito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$selected = ($aCntCer[$i]->getCodigo() == $codContaRec) ? "selected" : ""; 
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' $selected>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				$selected = ($aConta[$i]->getCodigo() == $codContaRec) ? "selected" : "";
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' $selected>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	'', '');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}





#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("C");
	$oCat   = "<option value=\"\"></option>";
	if ($aCat) {
		$aCatTemp	= array();
		$i 			= 0;
				
		foreach ($aCat as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();

		}
		
		ksort($aCatTemp);
		
		foreach ($aCatTemp as $cDesc => $cCod) {
			$oCat .= "<option value=\"".$cCod."\">".$cDesc.'</option>';
		}
	}else{
		$aCatTemp	= array();
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'indCredito' => 1),array('descricao' => 'ASC'));
	$oCentroCusto	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	null, '');
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
## Rateio
#################################################################################
$rateios		= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findBy(array('codContaRec' => $codConta),array('codigo' => 'ASC'));
$tabRateio		= "";
for ($i = 0; $i < sizeof($rateios); $i++) {
	
	#################################################################################
	## Monta a combo de Categorias
	#################################################################################
	$oCatInt   = "<option value=\"\"></option>";
	foreach ($aCatTemp as $cDesc => $cCod) {
		if ($rateios[$i]->getCodCategoria() !== null) {
			($rateios[$i]->getCodCategoria()->getCodigo() == $cCod) ? $selected = "selected=\"true\"" : $selected = "";
		}else{
			$selected = "";
		}
		$oCatInt .= "<option value=\"".$cCod."\" $selected>".$cDesc.'</option>';
	}
	
	#################################################################################
	## Monta a combo de Centro de Custo
	#################################################################################
	$codCentro			= ($rateios[$i]->getCodCentroCusto()) ? $rateios[$i]->getCodCentroCusto()->getCodigo() : null; 
	$oCentroCustoInt	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	$codCentro, '');
	
	
	$tabRateio	.= '<tr><td><select class="select2 '.$sView.'" style="width:100%;" name="codCategoria[]" data-rel="select2">'.$oCatInt.'</select></td><td><select class="select2 '.$sView.'" style="width:100%;" name="codCentroCusto[]" data-rel="select2">'.$oCentroCustoInt.'</select></td><td><input type="text" '.$tView.' '.$tView.' name="valorRateio[]" onchange="calculaPctCellRateioCRAlt($(this));" value="'.\Zage\App\Util::formataDinheiro(round($rateios[$i]->getValor(),2)).'" maxlength="20" autocomplete="off" zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td><td><input type="text" name="pctRateio[]" '.$tView.' '.$tView.' value="'.\Zage\App\Util::formataDinheiro(round($rateios[$i]->getPctValor(),2)).'" onchange="calculaValorCellRateioCRAlt($(this));" maxlength="7" autocomplete="off" zg-data-toggle="mask" zg-data-mask="porcentagem" zg-data-mask-retira="0"></td><td class="center"><span class="center '.$hView.'" '.$bView.' zgdelete onclick="delRowRateioCRAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codRateio[]" value="'.$rateios[$i]->getCodigo().'"></td></tr>';
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/contaReceberLis.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$codConta);

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta=');
$urlNovo			= ROOT_URL."/Fin/contaReceberAlt.php?id=".$uid;

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
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('NUMERO'				,$numero);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('COD_PESSOA'			,$codPessoa);
$tpl->set('PESSOA'				,$pessoa);
$tpl->set('PARCELA'				,$parcela);
$tpl->set('COD_STATUS'			,$codStatus);
$tpl->set('NUM_PARCELAS'		,$numParcelas);
$tpl->set('COD_MOEDA'			,$codMoeda);
$tpl->set('MOEDAS'				,$oMoeda);
$tpl->set('VALOR'				,\Zage\App\Util::formataDinheiro($valor));
$tpl->set('VALOR_JUROS'			,\Zage\App\Util::formataDinheiro($valorJuros));
$tpl->set('VALOR_MORA'			,\Zage\App\Util::formataDinheiro($valorMora));
$tpl->set('VALOR_DESCONTO'		,\Zage\App\Util::formataDinheiro($valorDesconto));
$tpl->set('VALOR_CANCELADO'		,\Zage\App\Util::formataDinheiro($valorCancelado));
$tpl->set('VALOR_OUTROS'		,\Zage\App\Util::formataDinheiro($valorOutros));
$tpl->set('VALOR_TOTAL'			,$valorTotal);
$tpl->set('DATA_EMISSAO'		,$dataEmissao);
$tpl->set('DATA_LIQ'			,$dataLiq);
$tpl->set('DATA_VENC'			,$dataVenc);
$tpl->set('DATA_AUT'			,$dataAut);
$tpl->set('IND_AUT'				,$indAut);
$tpl->set('DOCUMENTO'			,$documento);
$tpl->set('COD_FORMA_PAG'		,$codFormaPag);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('NOSSO_NUMERO'		,$nossoNumero);
$tpl->set('COD_GRUPO_CONTA'		,$codGrupoConta);
$tpl->set('COD_GRUPO_LANC'		,$codGrupoLanc);
$tpl->set('OBS'					,$obs);
$tpl->set('CONTAS'				,$oConta);
$tpl->set('COD_PERIODO_REC'		,$codPeriodoRec);
$tpl->set('CENTROS_CUSTO'		,$oCentroCusto);
$tpl->set('CATEGORIAS'			,$oCat);
$tpl->set('RATEIOS'				,$tabRateio);
$tpl->set('COD_TIPO_REC'		,$codTipoRec);


$tpl->set('INTERVALO_REC'		,$intervaloRec);
$tpl->set('PARCELA_INICIAL'		,$parcelaInicial);
$tpl->set('FLAG_RECEBER_AUTO'	,$indRecAuto);

$tpl->set('PERIODOS_REC'		,$oPeriodo);
$tpl->set('TEXT_VIEW'			,$tView);
$tpl->set('BUTTON_VIEW'			,$bView);
$tpl->set('SELECT_VIEW'			,$sView);
$tpl->set('ESCONDIDO'			,$hView);
$tpl->set('DESC_STATUS'			,$status);
$tpl->set('URL_EDITAR'			,$url);

$tpl->set('NOME_PESSOA'			,$nomePessoa);
$tpl->set('CPF_PESSOA'			,$infoCgc);
$tpl->set('DATACAD_PESSOA'		,$datacadPessoa);

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


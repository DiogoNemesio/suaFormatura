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

//$log->debug("POST: ".serialize($_POST));

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata os parâmetros passados pelo filtro
#################################################################################
if (isset($_POST['codStatus']))			$codStatus			= $_POST['codStatus'];
if (isset($_POST['codCategoria']))		$codCategoria		= $_POST['codCategoria'];
if (isset($_POST['codFormaPag']))		$codFormaPag		= $_POST['codFormaPag'];
if (isset($_POST['codCentroCusto']))	$codCentroCusto		= $_POST['codCentroCusto'];
if (isset($_POST['codContaPag']))		$codContaPag		= $_POST['codContaPag'];
if (isset($_POST['codTipoFiltro']))		$codTipoFiltro		= \Zage\App\Util::antiInjection($_POST['codTipoFiltro']);
if (isset($_POST['dataFiltro']))		$dataFiltro			= \Zage\App\Util::antiInjection($_POST['dataFiltro']);
if (isset($_POST['mesFiltro']))			$mesFiltro			= \Zage\App\Util::antiInjection($_POST['mesFiltro']);
if (isset($_POST['dataIniFiltro']))		$dataIniFiltro		= \Zage\App\Util::antiInjection($_POST['dataIniFiltro']);
if (isset($_POST['dataFimFiltro']))		$dataFimFiltro		= \Zage\App\Util::antiInjection($_POST['dataFimFiltro']);
if (isset($_POST['dataAvancar']))		$dataAvancar		= \Zage\App\Util::antiInjection($_POST['dataAvancar']);
if (isset($_POST['dataVoltar']))		$dataVoltar			= \Zage\App\Util::antiInjection($_POST['dataVoltar']);
if (isset($_POST['dataTipo']))			$dataTipo			= \Zage\App\Util::antiInjection($_POST['dataTipo']);
if (isset($_POST['valorIni']))			$valorIni			= \Zage\App\Util::antiInjection($_POST['valorIni']);
if (isset($_POST['valorFim']))			$valorFim			= \Zage\App\Util::antiInjection($_POST['valorFim']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['fornecedor']))		$fornecedor			= \Zage\App\Util::antiInjection($_POST['fornecedor']);


#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codCategoria	= (isset($codCategoria))	? $codCategoria 	: array();
$codStatus		= (isset($codStatus)) 		? $codStatus		: array();
$codCentroCusto	= (isset($codCentroCusto))	? $codCentroCusto	: array();
$codFormaPag	= (isset($codFormaPag)) 	? $codFormaPag		: array();
$codContaPag	= (isset($codContaPag)) 	? $codContaPag		: array();

#################################################################################
## Ajustar valores padrão das datas
#################################################################################
if (isset($codTipoFiltro)	&& ($codTipoFiltro))	$_SESSION["_CPLIS_codTipoFiltro"]	= $codTipoFiltro;
if (isset($dataTipo)		&&	($dataFiltro))		$_SESSION["_CPLIS_dataTipo"] 		= $dataTipo;

if (isset($codStatus))		$_SESSION["_CPLIS_codStatusFiltro"] 		= $codStatus;
if (isset($codFormaPag))	$_SESSION["_CPLIS_codFormaPagFiltro"] 		= $codFormaPag;
if (isset($codCategoria))	$_SESSION["_CPLIS_codCategoriaFiltro"] 		= $codCategoria;
if (isset($codCentroCusto))	$_SESSION["_CPLIS_codCentroCustoFiltro"] 	= $codCentroCusto;
if (isset($codContaPag))	$_SESSION["_CPLIS_codContaPagFiltro"]		= $codContaPag;
if (isset($valorIni))		$_SESSION["_CPLIS_valorIniFiltro"] 			= $valorIni;
if (isset($valorFim))		$_SESSION["_CPLIS_valorFimFiltro"] 			= $valorFim;
if (isset($descricao))		$_SESSION["_CPLIS_descricaoFiltro"] 		= $descricao;
if (isset($fornecedor))		$_SESSION["_CPLIS_fornecedorFiltro"] 		= $fornecedor;

if (!isset($_SESSION["_CPLIS_codStatusFiltro"]))			$_SESSION["_CPLIS_codStatusFiltro"]			= null;
if (!isset($_SESSION["_CPLIS_codFormaPagFiltro"]))			$_SESSION["_CPLIS_codFormaPagFiltro"]		= null;
if (!isset($_SESSION["_CPLIS_codCategoriaFiltro"]))			$_SESSION["_CPLIS_codCategoriaFiltro"]		= null;
if (!isset($_SESSION["_CPLIS_codCentroCustoFiltro"]))		$_SESSION["_CPLIS_codCentroCustoFiltro"]	= null;
if (!isset($_SESSION["_CPLIS_codContaPagFiltro"]))			$_SESSION["_CPLIS_codContaPagFiltro"]		= null;
if (!isset($_SESSION["_CPLIS_valorIniFiltro"]))				$_SESSION["_CPLIS_valorIniFiltro"]			= null;
if (!isset($_SESSION["_CPLIS_valorFimFiltro"]))				$_SESSION["_CPLIS_valorFimFiltro"]			= null;
if (!isset($_SESSION["_CPLIS_descricaoFiltro"]))			$_SESSION["_CPLIS_descricaoFiltro"]			= null;
if (!isset($_SESSION["_CPLIS_fornecedorFiltro"]))			$_SESSION["_CPLIS_fornecedorFiltro"]		= null;


if (isset($dataFiltro)		&& $dataFiltro == "all")		{
	$_SESSION["_CPLIS_dataFiltro"] 		= null;
}elseif (isset($dataFiltro))	{
	$_SESSION["_CPLIS_dataFiltro"] 		= $dataFiltro;
}

if (isset($mesFiltro)		&& $mesFiltro == "all")		{
	$_SESSION["_CPLIS_mesFiltro"] 		= null;
}elseif (isset($mesFiltro))	{
	$_SESSION["_CPLIS_mesFiltro"] 		= $mesFiltro;
}

if (isset($dataIniFiltro)		&& $dataIniFiltro == "all")		{
	$_SESSION["_CPLIS_dataIniFiltro"] 		= null;
}elseif (isset($dataIniFiltro))	{
	$_SESSION["_CPLIS_dataIniFiltro"] 		= $dataIniFiltro;
}

if (isset($dataFimFiltro)		&& $dataFimFiltro == "all")		{
	$_SESSION["_CPLIS_dataFimFiltro"] 		= null;
}elseif (isset($dataFimFiltro))	{
	$_SESSION["_CPLIS_dataFimFiltro"] 		= $dataFimFiltro;
}

if (!isset($_SESSION["_CPLIS_codTipoFiltro"])	&& (!isset($codTipoFiltro)))	$_SESSION["_CPLIS_codTipoFiltro"]	= "D";
if (!isset($_SESSION["_CPLIS_dataFiltro"])		&& (!isset($dataFiltro)))		$_SESSION["_CPLIS_dataFiltro"]		= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_CPLIS_mesFiltro"])		&& (!isset($mesFiltro)))		$_SESSION["_CPLIS_mesFiltro"]		= date('m/Y');
if (!isset($_SESSION["_CPLIS_dataIniFiltro"])	&& (!isset($dataIniFiltro)))	$_SESSION["_CPLIS_dataIniFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_CPLIS_dataFimFiltro"])	&& (!isset($dataFimFiltro)))	$_SESSION["_CPLIS_dataFimFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_CPLIS_dataTipo"])		&& (!isset($dataTipo)))			$_SESSION["_CPLIS_dataTipo"]		= "V";


#################################################################################
## Ajustar valores
#################################################################################
if (!isset($valorIni))		$valorIni		= null;
if (!isset($valorFim))		$valorFim		= null;
if (!isset($descricao))		$descricao		= null;
if (!isset($fornecedor))	$fornecedor		= null;

#################################################################################
## Verifica se é pra avançar ou retroceder a data
#################################################################################
if (isset($dataAvancar) && $dataAvancar == 1) {
	$offset	= 1;
}elseif (isset($dataVoltar) && $dataVoltar == 1) {
	$offset	= -1;
}else{
	$offset	= 0;
}

#################################################################################
## Calcular as datas da pesquisa
#################################################################################
if ($_SESSION["_CPLIS_codTipoFiltro"] == "D")	{
	if (!empty($_SESSION["_CPLIS_dataFiltro"])) {
		$dia			= substr($_SESSION["_CPLIS_dataFiltro"],0,2);
		$mes			= substr($_SESSION["_CPLIS_dataFiltro"],3,2);
		$ano			= substr($_SESSION["_CPLIS_dataFiltro"],6,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,$mes,($dia + $offset),$ano));
		$dataFim		= $dataIni;
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_CPLIS_dataFiltro"]	= $dataIni;
	}
	
}elseif ($_SESSION["_CPLIS_codTipoFiltro"]	== "M") {
	if (!empty($_SESSION["_CPLIS_mesFiltro"])) {
		$mes			= substr($_SESSION["_CPLIS_mesFiltro"],0,2);
		$ano			= substr($_SESSION["_CPLIS_mesFiltro"],3,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset),1,$ano));
		$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset+1),0,$ano));
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_CPLIS_mesFiltro"]	= date("m/Y", mktime (0,0,0,($mes+$offset),1,$ano));
	}
}else{
	if (!empty($_SESSION["_CPLIS_dataIniFiltro"]))	$dataIni		= $_SESSION["_CPLIS_dataIniFiltro"];
	if (!empty($_SESSION["_CPLIS_dataFimFiltro"]))	$dataFim		= $_SESSION["_CPLIS_dataFimFiltro"];
}

if (!isset($dataIni)) $dataIni = null;
if (!isset($dataFim)) $dataFim = null;

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$contas	= \Zage\Fin\ContaPagar::busca($dataIni,$dataFim,$_SESSION["_CPLIS_dataTipo"],$_SESSION["_CPLIS_valorIniFiltro"],$_SESSION["_CPLIS_valorFimFiltro"],$_SESSION["_CPLIS_codCategoriaFiltro"],$_SESSION["_CPLIS_codStatusFiltro"],$_SESSION["_CPLIS_codCentroCustoFiltro"],$_SESSION["_CPLIS_codFormaPagFiltro"],$_SESSION["_CPLIS_codContaPagFiltro"],$_SESSION["_CPLIS_descricaoFiltro"],$_SESSION["_CPLIS_fornecedorFiltro"]);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContasPagar");
$checkboxName	= "selItemContaPagar";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('STATUS'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO'),				7	,$grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			15	,$grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('FORNECEDOR'),			15	,$grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARC.'),				5	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			10	,$grid::CENTER	,'');
$grid->adicionaData($tr->trans('EMISSÃO'),				7	,$grid::CENTER	,'dataEmissao');
$grid->adicionaData($tr->trans('VENCIMENTO'),			8	,$grid::CENTER	,'dataVencimento');
$grid->adicionaTexto($tr->trans('FORMA'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('C. CUSTO'),			7	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('CATEGORIA'),			7	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('AÇÕES'),				5	,$grid::CENTER	,'');
$grid->importaDadosDoctrine($contas);

$colStatus	= 1;
$colParcela	= 5;
$colValTot	= 6;
$colForma	= 9;
$colCen		= 10;
$colCat		= 11;
$colAcao	= 12;

#################################################################################
## Criar array para controlar as ações em Lote
#################################################################################
$aCodigos	= array();


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($contas); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url);
	$vid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url.'&view=1');
	$cid		= \Zage\App\Util::encodeUrl('aSelContas='.$contas[$i]->getCodigo());
	
	#################################################################################
	## Definir o valor da Checkbox
	#################################################################################
	$grid->setValorCelula($i,0,$contas[$i]->getCodigo());
	
	#################################################################################
	## Popular o array para controlar as ações em Lote
	#################################################################################
	$aCodigos[$contas[$i]->getCodigo()]["STATUS"]		= $contas[$i]->getCodStatus()->getCodigo();
	$aCodigos[$contas[$i]->getCodigo()]["FORNEC"]		= ($contas[$i]->getCodPessoa()) ? $contas[$i]->getCodPessoa()->getCodigo() : null;
	
	#################################################################################
	## Verifica se está vencida
	#################################################################################
	if ($contas[$i]->getDataVencimento() < \DateTime::createFromFormat($system->config["data"]["dateFormat"].' H:i:s',date($system->config["data"]["dateFormat"].' H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))))) {
		$vencida 	= 1;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloVencido();
	}else{
		$vencida = 0;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloNormal();
	}
	
	
	#################################################################################
	## Parcela / NumParcelas
	#################################################################################
	$grid->setValorCelula($i,$colParcela,$contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas());
	
	
	#################################################################################
	## Resgatar o status para controlar as ações
	#################################################################################
	$status		= $contas[$i]->getCodStatus()->getCodigo();
	
	switch ($status) {
		
		case "A":
			$podeAlt	= true;
			$podeExc	= true;
			$podeCan	= true;
			$podeCon	= true;
			$podePls	= false;
			$podeSub	= true;
			$podeImp	= true;
			break;
		case "C":
			$podeAlt	= false;
			$podeExc	= true;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= false;
			$podeSub	= false;
			$podeImp	= true;
			break;
		case "L":
		case "EP":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= true;
			$podeSub	= false;
			$podeImp	= true;
			break;
		case "SC":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= true;
			$podeSub	= false;
			$podeImp	= true;
			break;
		case "S":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= false;
			$podeSub	= false;
			$podeImp	= true;
			break;
		case "SS":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= true;
			$podeSub	= false;
			$podeImp	= true;
			break;
		case "P":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= true;
			$podeCon	= true;
			$podePls	= true;
			$podeSub	= true;
			$podeImp	= true;
			break;
		default:
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podePls	= false;
			$podeSub	= false;
			$podeImp	= false;
			break;
	}
	
	
	#################################################################################
	## Status
	#################################################################################
	$grid->setValorCelula($i,$colStatus,"<span class='badge tooltip-".$corStatus." badge-".$corStatus." tooltip-info' data-rel='tooltip' data-placement='top' title='".$contas[$i]->getCodStatus()->getDescricao()."'>".$contas[$i]->getCodStatus()->getCodigo()."</span>");

	
	#################################################################################
	## Valor Total
	#################################################################################
	if ($status == "C") {
		$grid->setValorCelula($i,$colValTot,( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) - (floatval($contas[$i]->getValorDesconto())) ));
	}else{
		$grid->setValorCelula($i,$colValTot,( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) - (floatval($contas[$i]->getValorDesconto()) + floatval($contas[$i]->getValorCancelado())) ));
	}
	
	#################################################################################
	## Forma de Pagamento
	#################################################################################
	if ($contas[$i]->getCodFormaPagamento()) {
		$formaPag		= $contas[$i]->getCodFormaPagamento()->getCodigo();
		$corForma		= $contas[$i]->getCodFormaPagamento()->getEstilo();
	}else{
		$formaPag		= null;
		$corForma		= null;
	}
	 
	
	if ($formaPag)  {
		$grid->setValorCelula($i,$colForma,"<span class='badge tooltip-".$corForma." badge-".$corForma."' data-rel='tooltip' data-placement='top' title='".$contas[$i]->getCodFormaPagamento()->getDescricao()."'>".$formaPag."</span>");
	}
	
	#################################################################################
	## Informações de Rateio
	#################################################################################
	$rateios	= \Zage\Fin\ContaPagarRateio::lista($contas[$i]->getCodigo());
	$aCentros	= array();
	$aCats		= array();
	
	for ($r = 0; $r < sizeof($rateios); $r++) {
		if ($rateios[$r]->getCodCategoria()) 	$aCats[]		= $rateios[$r]->getCodCategoria()->getDescricao();
		if ($rateios[$r]->getCodCentroCusto())	$aCentros[]		= $rateios[$r]->getCodCentroCusto()->getDescricao();
	}
	sort($aCats);
	sort($aCentros);
	$aCats		= array_unique($aCats);
	$aCentros	= array_unique($aCentros);
	$cats		= "";
	$centros	= "";
	
	#################################################################################
	## Centro de Custo
	#################################################################################
	if (sizeof($aCentros) == 1) {
		foreach ($aCentros as $centro) {
			$htmlCentro	= $centro;
		}
	}elseif (sizeof($aCentros) > 0) {
		foreach ($aCentros as $centro) {
			$centros	.= '<li><a href="#">'.$centro.'</a></li>';
		}
		$htmlCentro	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="fa fa-ellipsis-h bigger-150"></i></a>
		<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
			'.$centros.'
		</ul>
		</div>';
	}else{
		$htmlCentro	= null;
	} 

	#################################################################################
	## Categorias
	#################################################################################
	if (sizeof($aCats) == 1) {
		foreach ($aCats as $cat) {
			$htmlCat = $cat;
		}
	}elseif (sizeof($aCats) > 0) {
		foreach ($aCats as $cat) {
			$cats	.= '<li><a href="#">'.$cat.'</a></li>';
		}
		
		$htmlCat	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="fa fa-ellipsis-h bigger-150"></i></a>
		<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
			'.$cats.'
		</ul>
		</div>';
	}else{
		$htmlCat 	= null;
	}
	
	$grid->setValorCelula($i,$colCen,$htmlCentro);
	$grid->setValorCelula($i,$colCat,$htmlCat);
	
	
	#################################################################################
	## Ações
	#################################################################################
	$htmlTplAcaoIni	= '<div class="inline blue center tooltip-info" style="width: 30px;" onclick="%U%" data-toggle="tooltip" data-placement="top" title="%M%">';
	$htmlTplAcaoFim	= '</div>';
	
	$urlVis			= "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaPagarAlt.php?id=".$vid."');";
	$urlAlt			= ($podeAlt)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaPagarAlt.php?id=".$uid."');" : null;
	$urlExc			= ($podeExc)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarExc.php?id=".$uid."');" : null;
	$urlCan			= ($podeCan)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarCan.php?id=".$uid."');" : null;
	$urlCon			= ($podeCon)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarPag.php?id=".$uid."');" : null;
	$urlPls			= ($podePls)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarPagLis.php?id=".$uid."');" : null;
	$urlSub			= ($podeSub)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaPagarSub.php?id=".$uid."&cid=".$cid."');" : null;
	$urlImp			= ($podeImp)	? "javascript:zgAbreModalFull('".ROOT_URL."/Fin/contaPagarPreview.php?id=".$uid."');" : null;
		
	
	$htmlVis		= str_replace("%M%","Visualizar"				, str_replace("%U%",$urlVis, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-search grey bigger-140"></i>' . $htmlTplAcaoFim;
	$htmlAlt		= str_replace("%M%","Alterar"					, str_replace("%U%",$urlAlt, $htmlTplAcaoIni)) . (($podeAlt)	?  '<i class="ace-icon fa fa-edit blue bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlExc		= str_replace("%M%","Excluir"					, str_replace("%U%",$urlExc, $htmlTplAcaoIni)) . (($podeExc)	?  '<i class="ace-icon fa fa-trash red bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlCan		= str_replace("%M%","Cancelar"					, str_replace("%U%",$urlCan, $htmlTplAcaoIni)) . (($podeCan)	?  '<i class="ace-icon fa fa-ban red bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlCon		= str_replace("%M%","Confirmar"					, str_replace("%U%",$urlCon, $htmlTplAcaoIni)) . (($podeCon)	?  '<i class="ace-icon fa fa-check green bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlPls		= str_replace("%M%","Pagamentos confirmados"	, str_replace("%U%",$urlPls, $htmlTplAcaoIni)) . (($podePls)	?  '<i class="ace-icon fa fa-usd grey bigger-140"></i>'			: null) . $htmlTplAcaoFim;
	$htmlSub		= str_replace("%M%","Substituir"				, str_replace("%U%",$urlSub, $htmlTplAcaoIni)) . (($podeSub)	?  '<i class="ace-icon fa fa-exchange blue bigger-140"></i>' 	: null) . $htmlTplAcaoFim;
	$htmlImp		= str_replace("%M%","Imprimir"					, str_replace("%U%",$urlImp, $htmlTplAcaoIni)) . (($podeImp)	?  '<i class="ace-icon fa fa-print grey bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	
	$htmlAcao	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="ace-icon fa fa-cog icon-on-right bigger-140"></i></a>
	<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
		<li class="active"><a href="#"><div class="center small bolder blue">Ações para: '.$contas[$i]->getDescricao().' ('.$contas[$i]->getParcela() . "/".$contas[$i]->getNumParcelas().')</div></a></li>
		<li><a href="#">'.$htmlVis.$htmlAlt.$htmlExc.$htmlCan.$htmlCon.$htmlPls.$htmlSub.$htmlImp.'</a></li>
	</ul>
	</div>';
	$grid->setValorCelula($i,$colAcao,$htmlAcao);
	
}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$codFormaPag, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Débito
#################################################################################
try {
	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaPag, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select da Categoria
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::listaCombo("D");
	$oCat   = "";
	if ($aCat) {
		$aCatTemp	= array();
		$i 			= 0;

		foreach ($aCat as $cat) {
			$tDesc 	= ($cat->getCodCategoriaPai() != null) ? $cat->getCodCategoriaPai()->getDescricao() . "/" . $cat->getDescricao() : $cat->getDescricao();
			$aCatTemp[$tDesc]	= $cat->getCodigo();

		}

		ksort($aCatTemp);

		foreach ($aCatTemp as $cDesc => $cCod) {
			if ($codCategoria !== null) {
				(in_array($cCod, $codCategoria)) ? $selected = "selected=\"selected\"" : $selected = "";
			}else{
				$selected = "";
			}
			$oCat .= "<option value=\"".$cCod."\" $selected>".$cDesc.'</option>';
		}
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Centro de Custo
#################################################################################
try {
	$aCentroCusto	= $em->getRepository('Entidades\ZgfinCentroCusto')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'indDebito' => 1),array('descricao' => 'ASC'));
	$oCentroCusto	= $system->geraHtmlCombo($aCentroCusto,	'CODIGO', 'DESCRICAO',	$codCentroCusto, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$codStatus, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= ROOT_URL.'/Fin/contaPagarAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta=');

#################################################################################
## Gerar a url de filtro
#################################################################################
$urlFiltroData		= ROOT_URL . "/Fin/contaPagarLisFiltroData.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoFiltro='.$_SESSION["_CPLIS_codTipoFiltro"].'&dataFiltro='.$_SESSION["_CPLIS_dataFiltro"].'&mesFiltro='.$_SESSION["_CPLIS_mesFiltro"].'&dataIniFiltro='.$_SESSION["_CPLIS_dataIniFiltro"].'&dataFimFiltro='.$_SESSION["_CPLIS_dataFimFiltro"]);
$urlFiltro			= ROOT_URL . "/Fin/contaPagarLisFiltro.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Gerar as urls dos botões de ação
#################################################################################
$excUrl		= ROOT_URL . "/Fin/contaPagarExc.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$canUrl		= ROOT_URL . "/Fin/contaPagarCan.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$conUrl		= ROOT_URL . "/Fin/contaPagarPagLote.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$subUrl		= ROOT_URL . "/Fin/contaPagarSub.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$impUrl		= ROOT_URL . "/Fin/contaPagarImp.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Contas a Pagar"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('FILTER_DATE_URL'	,$urlFiltroData);
$tpl->set('URL_FILTRO'		,$urlFiltro);
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('CHECK_NAME'		,$checkboxName);

$tpl->set('COD_TIPO_FILTRO'	,$_SESSION["_CPLIS_codTipoFiltro"]);
$tpl->set('DATA_TIPO'		,$_SESSION["_CPLIS_dataTipo"]);
$tpl->set('DATA_FILTRO'		,$_SESSION["_CPLIS_dataFiltro"]);
$tpl->set('MES_FILTRO'		,$_SESSION["_CPLIS_mesFiltro"]);
$tpl->set('DATA_INI_FILTRO'	,$_SESSION["_CPLIS_dataIniFiltro"]);
$tpl->set('DATA_FIM_FILTRO'	,$_SESSION["_CPLIS_dataFimFiltro"]);
$tpl->set('VALOR_INI'		,$valorIni);
$tpl->set('VALOR_FIM'		,$valorFim);
$tpl->set('DESCRICAO'		,$descricao);
$tpl->set('FORNECEDOR'		,$fornecedor);

$tpl->set('EXC_URL'			,$excUrl);
$tpl->set('CAN_URL'			,$canUrl);
$tpl->set('CON_URL'			,$conUrl);
$tpl->set('SUB_URL'			,$subUrl);
$tpl->set('IMP_URL'			,$impUrl);

$tpl->set('CATEGORIAS'		,$oCat);
$tpl->set('STATUS'			,$oStatus);
$tpl->set('CENTRO_CUSTO'	,$oCentroCusto);
$tpl->set('FORMAS_PAG'		,$oFormaPag);
$tpl->set('CONTAS'			,$oConta);

$tpl->set('JSON_CODIGOS'	,json_encode($aCodigos));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

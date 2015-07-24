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
## Resgata os parâmetros passados pelo filtro
#################################################################################
if (isset($_POST['codCategoria']))		$codCategoria		= $_POST['codCategoria'];
if (isset($_POST['codCentroCusto']))	$codCentroCusto		= $_POST['codCentroCusto'];
if (isset($_POST['codContaRec']))		$codContaRec		= $_POST['codContaRec'];
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
if (isset($_POST['cliente']))			$cliente			= \Zage\App\Util::antiInjection($_POST['cliente']);


#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codCategoria	= (isset($codCategoria))	? $codCategoria 	: array();
$codCentroCusto	= (isset($codCentroCusto))	? $codCentroCusto	: array();
$codContaRec	= (isset($codContaRec)) 	? $codContaRec		: array();

#################################################################################
## Ajustar valores padrão das datas
#################################################################################
if (isset($codTipoFiltro)	&& ($codTipoFiltro))	$_SESSION["_BOLLIS_codTipoFiltro"]	= $codTipoFiltro;
if (isset($dataTipo)		&&	($dataFiltro))		$_SESSION["_BOLLIS_dataTipo"] 		= $dataTipo;

if (isset($codCategoria))	$_SESSION["_BOLLIS_codCategoriaFiltro"] 		= $codCategoria;
if (isset($codCentroCusto))	$_SESSION["_BOLLIS_codCentroCustoFiltro"] 		= $codCentroCusto;
if (isset($codContaRec))	$_SESSION["_BOLLIS_codContaRecFiltro"]			= $codContaRec;
if (isset($valorIni))		$_SESSION["_BOLLIS_valorIniFiltro"] 			= $valorIni;
if (isset($valorFim))		$_SESSION["_BOLLIS_valorFimFiltro"] 			= $valorFim;
if (isset($descricao))		$_SESSION["_BOLLIS_descricaoFiltro"] 			= $descricao;
if (isset($cliente))		$_SESSION["_BOLLIS_clienteFiltro"] 				= $cliente;

if (!isset($_SESSION["_BOLLIS_codCategoriaFiltro"]))			$_SESSION["_BOLLIS_codCategoriaFiltro"]		= null;
if (!isset($_SESSION["_BOLLIS_codCentroCustoFiltro"]))			$_SESSION["_BOLLIS_codCentroCustoFiltro"]	= null;
if (!isset($_SESSION["_BOLLIS_codContaRecFiltro"]))				$_SESSION["_BOLLIS_codContaRecFiltro"]		= null;
if (!isset($_SESSION["_BOLLIS_valorIniFiltro"]))				$_SESSION["_BOLLIS_valorIniFiltro"]			= null;
if (!isset($_SESSION["_BOLLIS_valorFimFiltro"]))				$_SESSION["_BOLLIS_valorFimFiltro"]			= null;
if (!isset($_SESSION["_BOLLIS_descricaoFiltro"]))				$_SESSION["_BOLLIS_descricaoFiltro"]		= null;
if (!isset($_SESSION["_BOLLIS_clienteFiltro"]))					$_SESSION["_BOLLIS_clienteFiltro"]			= null;

if (isset($dataFiltro)		&& $dataFiltro == "all")		{
	$_SESSION["_BOLLIS_dataFiltro"] 		= null;
}elseif (isset($dataFiltro))	{
	$_SESSION["_BOLLIS_dataFiltro"] 		= $dataFiltro;
}

if (isset($mesFiltro)		&& $mesFiltro == "all")		{
	$_SESSION["_BOLLIS_mesFiltro"] 		= null;
}elseif (isset($mesFiltro))	{
	$_SESSION["_BOLLIS_mesFiltro"] 		= $mesFiltro;
}

if (isset($dataIniFiltro)		&& $dataIniFiltro == "all")		{
	$_SESSION["_BOLLIS_dataIniFiltro"] 		= null;
}elseif (isset($dataIniFiltro))	{
	$_SESSION["_BOLLIS_dataIniFiltro"] 		= $dataIniFiltro;
}

if (isset($dataFimFiltro)		&& $dataFimFiltro == "all")		{
	$_SESSION["_BOLLIS_dataFimFiltro"] 		= null;
}elseif (isset($dataFimFiltro))	{
	$_SESSION["_BOLLIS_dataFimFiltro"] 		= $dataFimFiltro;
}

if (!isset($_SESSION["_BOLLIS_codTipoFiltro"])	&& (!isset($codTipoFiltro)))	$_SESSION["_BOLLIS_codTipoFiltro"]	= "D";
if (!isset($_SESSION["_BOLLIS_dataFiltro"])		&& (!isset($dataFiltro)))		$_SESSION["_BOLLIS_dataFiltro"]		= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_BOLLIS_mesFiltro"])		&& (!isset($mesFiltro)))		$_SESSION["_BOLLIS_mesFiltro"]		= date('m/Y');
if (!isset($_SESSION["_BOLLIS_dataIniFiltro"])	&& (!isset($dataIniFiltro)))	$_SESSION["_BOLLIS_dataIniFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_BOLLIS_dataFimFiltro"])	&& (!isset($dataFimFiltro)))	$_SESSION["_BOLLIS_dataFimFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_BOLLIS_dataTipo"])		&& (!isset($dataTipo)))			$_SESSION["_BOLLIS_dataTipo"]		= "V";


#################################################################################
## Ajustar valores
#################################################################################
if (!isset($valorIni))		$valorIni		= null;
if (!isset($valorFim))		$valorFim		= null;
if (!isset($descricao))		$descricao		= null;
if (!isset($cliente))		$cliente		= null;

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
if ($_SESSION["_BOLLIS_codTipoFiltro"] == "D")	{
	if (!empty($_SESSION["_BOLLIS_dataFiltro"])) {
		$dia			= substr($_SESSION["_BOLLIS_dataFiltro"],0,2);
		$mes			= substr($_SESSION["_BOLLIS_dataFiltro"],3,2);
		$ano			= substr($_SESSION["_BOLLIS_dataFiltro"],6,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,$mes,($dia + $offset),$ano));
		$dataFim		= $dataIni;
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_BOLLIS_dataFiltro"]	= $dataIni;
	}
	
}elseif ($_SESSION["_BOLLIS_codTipoFiltro"]	== "M") {
	if (!empty($_SESSION["_BOLLIS_mesFiltro"])) {
		$mes			= substr($_SESSION["_BOLLIS_mesFiltro"],0,2);
		$ano			= substr($_SESSION["_BOLLIS_mesFiltro"],3,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset),1,$ano));
		$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset+1),0,$ano));
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_BOLLIS_mesFiltro"]	= date("m/Y", mktime (0,0,0,($mes+$offset),1,$ano));
	}
}else{
	if (!empty($_SESSION["_BOLLIS_dataIniFiltro"]))	$dataIni		= $_SESSION["_BOLLIS_dataIniFiltro"];
	if (!empty($_SESSION["_BOLLIS_dataFimFiltro"]))	$dataFim		= $_SESSION["_BOLLIS_dataFimFiltro"];
}

if (!isset($dataIni)) $dataIni = null;
if (!isset($dataFim)) $dataFim = null;

#################################################################################
## Fiza o Status e a Forma de Pagamento
#################################################################################
$_SESSION["_BOLLis_codStatusFiltro"]		= array("A","P");
$_SESSION["_BOLLis_codFormaPagFiltro"]		= array("BOL");

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$contas	= \Zage\Fin\ContaReceber::busca($dataIni,$dataFim,$_SESSION["_BOLLIS_dataTipo"],$_SESSION["_BOLLIS_valorIniFiltro"],$_SESSION["_BOLLIS_valorFimFiltro"],$_SESSION["_BOLLIS_codCategoriaFiltro"],$_SESSION["_BOLLis_codStatusFiltro"],$_SESSION["_BOLLIS_codCentroCustoFiltro"],$_SESSION["_BOLLis_codFormaPagFiltro"],$_SESSION["_BOLLIS_codContaRecFiltro"],$_SESSION["_BOLLIS_descricaoFiltro"],$_SESSION["_BOLLIS_clienteFiltro"]);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GBolLis");
$checkboxName	= "selItemGeracaoBoleto";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('STATUS'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO'),				8	,$grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			18	,$grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('CLIENTE'),				18	,$grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARC.'),				5	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			10	,$grid::CENTER	,'');
$grid->adicionaData($tr->trans('EMISSÃO'),				7	,$grid::CENTER	,'dataEmissao');
$grid->adicionaData($tr->trans('VENCIMENTO'),			8	,$grid::CENTER	,'dataVencimento');
$grid->adicionaTexto($tr->trans('FORMA'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('C. CUSTO'),			8	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('CATEGORIA'),			8	,$grid::CENTER	,'');
$grid->importaDadosDoctrine($contas);

$colStatus	= 1;
$colParcela	= 5;
$colValTot	= 6;
$colForma	= 9;
$colCen		= 10;
$colCat		= 11;

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Definir o valor da Checkbox
	#################################################################################
	$grid->setValorCelula($i,0,$contas[$i]->getCodigo());
	
	#################################################################################
	## Verifica se está vencida
	#################################################################################
	$vencimento			= $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$numDiasAtraso		= \Zage\Fin\Data::numDiasAtraso($vencimento);
	if ($numDiasAtraso > 0) {
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloVencido();
	}else{
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
	$rateios	= \Zage\Fin\ContaReceberRateio::lista($contas[$i]->getCodigo());
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
	## Verificar se a conta está configurada para emitir boleto
	## Fazer isso verificando se a carteira da conta está preenchida
	#################################################################################
	$contaRec	= $contas[$i]->getCodConta();
	if ( ($contaRec) && ($formaPag == 'BOL') ) {
		if ($contaRec->getCodTipo()->getCodigo() == 'CC' && ($contaRec->getCodCarteira() != null) ) {
			$podeBol	= true;
		}else{
			$podeBol	= false;
		}
	}else{
		$podeBol	= false;
	}
	
	if (!$podeBol) $grid->desabilitaLinha($i);
	
	
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
## Gerar a url de filtro
#################################################################################
$urlFiltroData		= ROOT_URL . "/Fin/geraBoletoLisFiltroData.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoFiltro='.$_SESSION["_BOLLIS_codTipoFiltro"].'&dataFiltro='.$_SESSION["_BOLLIS_dataFiltro"].'&mesFiltro='.$_SESSION["_BOLLIS_mesFiltro"].'&dataIniFiltro='.$_SESSION["_BOLLIS_dataIniFiltro"].'&dataFimFiltro='.$_SESSION["_BOLLIS_dataFimFiltro"]);
$urlFiltro			= ROOT_URL . "/Fin/geraBoletoLisFiltro.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Gerar a url de geração de boleto
#################################################################################
$urlBol				= ROOT_URL."/Fin/geraBoletoMassa.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Geração de Boletos"));
$tpl->set('FILTER_DATE_URL'	,$urlFiltroData);
$tpl->set('URL_FILTRO'		,$urlFiltro);
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('CHECK_NAME'		,$checkboxName);

$tpl->set('COD_TIPO_FILTRO'	,$_SESSION["_BOLLIS_codTipoFiltro"]);
$tpl->set('DATA_TIPO'		,$_SESSION["_BOLLIS_dataTipo"]);
$tpl->set('DATA_FILTRO'		,$_SESSION["_BOLLIS_dataFiltro"]);
$tpl->set('MES_FILTRO'		,$_SESSION["_BOLLIS_mesFiltro"]);
$tpl->set('DATA_INI_FILTRO'	,$_SESSION["_BOLLIS_dataIniFiltro"]);
$tpl->set('DATA_FIM_FILTRO'	,$_SESSION["_BOLLIS_dataFimFiltro"]);
$tpl->set('VALOR_INI'		,$valorIni);
$tpl->set('VALOR_FIM'		,$valorFim);
$tpl->set('DESCRICAO'		,$descricao);
$tpl->set('CLIENTE'			,$cliente);

$tpl->set('BOL_URL'			,$urlBol);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

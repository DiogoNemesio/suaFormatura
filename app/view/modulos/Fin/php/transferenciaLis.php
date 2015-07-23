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
if (isset($_POST['codFormaPag']))		$codFormaPag		= $_POST['codFormaPag'];
if (isset($_POST['codContaOrig']))		$codContaOrig		= $_POST['codContaOrig'];
if (isset($_POST['codContaDest']))		$codContaDest		= $_POST['codContaDest'];
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


#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codStatus		= (isset($codStatus)) 		? $codStatus		: array();
$codFormaPag	= (isset($codFormaPag)) 	? $codFormaPag		: array();
$codContaOrig	= (isset($codContaOrig)) 	? $codContaOrig		: array();
$codContaDest	= (isset($codContaDest)) 	? $codContaDest		: array();

#################################################################################
## Ajustar valores padrão das datas
#################################################################################
if (isset($codTipoFiltro)	&& ($codTipoFiltro))	$_SESSION["_TRLIS_codTipoFiltro"]	= $codTipoFiltro;
if (isset($dataTipo)		&&	($dataFiltro))		$_SESSION["_TRLIS_dataTipo"] 		= $dataTipo;

if (isset($codStatus))		$_SESSION["_TRLIS_codStatusFiltro"] 		= $codStatus;
if (isset($codFormaPag))	$_SESSION["_TRLIS_codFormaPagFiltro"] 		= $codFormaPag;
if (isset($codContaOrig))	$_SESSION["_TRLIS_codContaOrigFiltro"] 		= $codContaOrig;
if (isset($codContaDest))	$_SESSION["_TRLIS_codContaDestFiltro"] 		= $codContaDest;
if (isset($valorIni))		$_SESSION["_TRLIS_valorIniFiltro"] 			= $valorIni;
if (isset($valorFim))		$_SESSION["_TRLIS_valorFimFiltro"] 			= $valorFim;
if (isset($descricao))		$_SESSION["_TRLIS_descricaoFiltro"] 		= $descricao;

if (!isset($_SESSION["_TRLIS_codStatusFiltro"]))			$_SESSION["_TRLIS_codStatusFiltro"]			= null;
if (!isset($_SESSION["_TRLIS_codFormaPagFiltro"]))			$_SESSION["_TRLIS_codFormaPagFiltro"]		= null;
if (!isset($_SESSION["_TRLIS_codContaOrigFiltro"]))			$_SESSION["_TRLIS_codContaOrigFiltro"]		= null;
if (!isset($_SESSION["_TRLIS_codContaDestFiltro"]))			$_SESSION["_TRLIS_codContaDestFiltro"]		= null;
if (!isset($_SESSION["_TRLIS_valorIniFiltro"]))				$_SESSION["_TRLIS_valorIniFiltro"]			= null;
if (!isset($_SESSION["_TRLIS_valorFimFiltro"]))				$_SESSION["_TRLIS_valorFimFiltro"]			= null;
if (!isset($_SESSION["_TRLIS_descricaoFiltro"]))			$_SESSION["_TRLIS_descricaoFiltro"]			= null;


if (isset($dataFiltro)		&& $dataFiltro == "all")		{
	$_SESSION["_TRLIS_dataFiltro"] 		= null;
}elseif (isset($dataFiltro))	{
	$_SESSION["_TRLIS_dataFiltro"] 		= $dataFiltro;
}

if (isset($mesFiltro)		&& $mesFiltro == "all")		{
	$_SESSION["_TRLIS_mesFiltro"] 		= null;
}elseif (isset($mesFiltro))	{
	$_SESSION["_TRLIS_mesFiltro"] 		= $mesFiltro;
}

if (isset($dataIniFiltro)		&& $dataIniFiltro == "all")		{
	$_SESSION["_TRLIS_dataIniFiltro"] 		= null;
}elseif (isset($dataIniFiltro))	{
	$_SESSION["_TRLIS_dataIniFiltro"] 		= $dataIniFiltro;
}

if (isset($dataFimFiltro)		&& $dataFimFiltro == "all")		{
	$_SESSION["_TRLIS_dataFimFiltro"] 		= null;
}elseif (isset($dataFimFiltro))	{
	$_SESSION["_TRLIS_dataFimFiltro"] 		= $dataFimFiltro;
}

if (!isset($_SESSION["_TRLIS_codTipoFiltro"])	&& (!isset($codTipoFiltro)))	$_SESSION["_TRLIS_codTipoFiltro"]	= "D";
if (!isset($_SESSION["_TRLIS_dataFiltro"])		&& (!isset($dataFiltro)))		$_SESSION["_TRLIS_dataFiltro"]		= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_TRLIS_mesFiltro"])		&& (!isset($mesFiltro)))		$_SESSION["_TRLIS_mesFiltro"]		= date('m/Y');
if (!isset($_SESSION["_TRLIS_dataIniFiltro"])	&& (!isset($dataIniFiltro)))	$_SESSION["_TRLIS_dataIniFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_TRLIS_dataFimFiltro"])	&& (!isset($dataFimFiltro)))	$_SESSION["_TRLIS_dataFimFiltro"]	= date($system->config["data"]["dateFormat"]);
if (!isset($_SESSION["_TRLIS_dataTipo"])		&& (!isset($dataTipo)))			$_SESSION["_TRLIS_dataTipo"]		= "V";


#################################################################################
## Ajustar valores
#################################################################################
if (!isset($valorIni))		$valorIni		= null;
if (!isset($valorFim))		$valorFim		= null;
if (!isset($descricao))		$descricao		= null;

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
if ($_SESSION["_TRLIS_codTipoFiltro"] == "D")	{
	if (!empty($_SESSION["_TRLIS_dataFiltro"])) {
		$dia			= substr($_SESSION["_TRLIS_dataFiltro"],0,2);
		$mes			= substr($_SESSION["_TRLIS_dataFiltro"],3,2);
		$ano			= substr($_SESSION["_TRLIS_dataFiltro"],6,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,$mes,($dia + $offset),$ano));
		$dataFim		= $dataIni;
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_TRLIS_dataFiltro"]	= $dataIni;
	}
	
}elseif ($_SESSION["_TRLIS_codTipoFiltro"]	== "M") {
	if (!empty($_SESSION["_TRLIS_mesFiltro"])) {
		$mes			= substr($_SESSION["_TRLIS_mesFiltro"],0,2);
		$ano			= substr($_SESSION["_TRLIS_mesFiltro"],3,4);
		$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset),1,$ano));
		$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+$offset+1),0,$ano));
		
		/** Atualiza a variável de sessão **/
		$_SESSION["_TRLIS_mesFiltro"]	= date("m/Y", mktime (0,0,0,($mes+$offset),1,$ano));
	}
}else{
	if (!empty($_SESSION["_TRLIS_dataIniFiltro"]))	$dataIni		= $_SESSION["_TRLIS_dataIniFiltro"];
	if (!empty($_SESSION["_TRLIS_dataFimFiltro"]))	$dataFim		= $_SESSION["_TRLIS_dataFimFiltro"];
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
	$transferencias	= \Zage\Fin\Transferencia::busca($dataIni,$dataFim,$_SESSION["_TRLIS_dataTipo"],$_SESSION["_TRLIS_valorIniFiltro"],$_SESSION["_TRLIS_valorFimFiltro"],$_SESSION["_TRLIS_codStatusFiltro"],$_SESSION["_TRLIS_codFormaPagFiltro"],$_SESSION["_TRLIS_codContaOrigFiltro"],$_SESSION["_TRLIS_codContaDestFiltro"],$_SESSION["_TRLIS_descricaoFiltro"]);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GTransferencia");
$checkboxName	= "selItemTransferencia";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('STATUS'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO'),				10	,$grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			20	,$grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('PARC.'),				6	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			10	,$grid::CENTER	,'valor');
$grid->adicionaData($tr->trans('EMISSÃO'),				10	,$grid::CENTER	,'dataEmissao');
$grid->adicionaData($tr->trans('TRANSFERÊNCIA'),		10	,$grid::CENTER	,'dataTransferencia');
$grid->adicionaTexto($tr->trans('ORIGEM'),				10	,$grid::CENTER	,'codContaOrigem:nome');
$grid->adicionaTexto($tr->trans('DESTINO'),				10	,$grid::CENTER	,'codContaDestino:nome');
$grid->adicionaTexto($tr->trans('FORMA'),				8	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('AÇÕES'),				5	,$grid::CENTER	,'');
$grid->importaDadosDoctrine($transferencias);

$colStatus	= 1;
$colParcela	= 4;
$colValTot	= 5;
$colForma	= 10;
$colAcao	= 11;

#################################################################################
## Criar array para controlar as ações em Lote
#################################################################################
$aCodigos	= array();


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($transferencias); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTransf='.$transferencias[$i]->getCodigo().'&url='.$url);
	$vid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTransf='.$transferencias[$i]->getCodigo().'&url='.$url.'&view=1');
	
	#################################################################################
	## Definir o valor da Checkbox
	#################################################################################
	$grid->setValorCelula($i,0,$transferencias[$i]->getCodigo());
	
	#################################################################################
	## Popular o array para controlar as ações em Lote
	#################################################################################
	$aCodigos[$transferencias[$i]->getCodigo()]["STATUS"]		= $transferencias[$i]->getCodStatus()->getCodigo();
	
	#################################################################################
	## Verifica se está vencida
	#################################################################################
	if ($transferencias[$i]->getDataTransferencia() < \DateTime::createFromFormat($system->config["data"]["dateFormat"].' H:i:s',date($system->config["data"]["dateFormat"].' H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))))) {
		$vencida 	= 1;
		$corStatus	= $transferencias[$i]->getCodStatus()->getEstiloVencido();
	}else{
		$vencida = 0;
		$corStatus	= $transferencias[$i]->getCodStatus()->getEstiloNormal();
	}
	
	
	#################################################################################
	## Parcela / NumParcelas
	#################################################################################
	$grid->setValorCelula($i,$colParcela,$transferencias[$i]->getParcela() . " / ".$transferencias[$i]->getNumParcelas());
	
	
	#################################################################################
	## Resgatar o status para controlar as ações
	#################################################################################
	$status		= $transferencias[$i]->getCodStatus()->getCodigo();
	
	switch ($status) {
		
		case "P":
			$podeAlt	= true;
			$podeExc	= true;
			$podeCan	= true;
			$podeCon	= true;
			$podeHis	= false;
			$podeImp	= true;
			break;
		case "PA":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= true;
			$podeCon	= true;
			$podeHis	= true;
			$podeImp	= true;
			break;
		case "C":
			$podeAlt	= false;
			$podeExc	= true;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= false;
			$podeImp	= true;
			break;
		case "R":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= true;
			$podeImp	= true;
			break;
		default:
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= false;
			$podeImp	= false;
			break;
	}
	
	
	#################################################################################
	## Status
	#################################################################################
	$grid->setValorCelula($i,$colStatus,"<span class='badge tooltip-".$corStatus." badge-".$corStatus." tooltip-info' data-rel='tooltip' data-placement='top' title='".$transferencias[$i]->getCodStatus()->getDescricao()."'>".$transferencias[$i]->getCodStatus()->getCodigo()."</span>");

	
	#################################################################################
	## Forma de Pagamento
	#################################################################################
	if ($transferencias[$i]->getCodFormaPagamento()) {
		$formaPag		= $transferencias[$i]->getCodFormaPagamento()->getCodigo();
		$corForma		= $transferencias[$i]->getCodFormaPagamento()->getEstilo();
	}else{
		$formaPag		= null;
		$corForma		= null;
	}
	 
	if ($formaPag)  {
		$grid->setValorCelula($i,$colForma,"<span class='badge tooltip-".$corForma." badge-".$corForma."' data-rel='tooltip' data-placement='top' title='".$transferencias[$i]->getCodFormaPagamento()->getDescricao()."'>".$formaPag."</span>");
	}
	
	#################################################################################
	## Ações
	#################################################################################
	$htmlTplAcaoIni	= '<div class="inline blue center tooltip-info" style="width: 30px;" onclick="%U%" data-toggle="tooltip" data-placement="top" title="%M%">';
	$htmlTplAcaoFim	= '</div>';
	
	$urlVis			= "javascript:zgLoadUrl('".ROOT_URL."/Fin/transferenciaAlt.php?id=".$vid."');";
	$urlAlt			= ($podeAlt)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/transferenciaAlt.php?id=".$uid."');" : null;
	$urlExc			= ($podeExc)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/transferenciaExc.php?id=".$uid."');" : null;
	$urlCan			= ($podeCan)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/transferenciaCan.php?id=".$uid."');" : null;
	$urlCon			= ($podeCon)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/transferenciaCon.php?id=".$uid."');" : null;
	$urlHis			= ($podeHis)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/transferenciaHis.php?id=".$uid."');" : null;
	$urlImp			= ($podeImp)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/transferenciaPri.php?id=".$uid."');" : null;
		
	
	$htmlVis		= str_replace("%M%","Visualizar"	, str_replace("%U%",$urlVis, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-search grey bigger-140"></i>' . $htmlTplAcaoFim;
	$htmlAlt		= str_replace("%M%","Alterar"		, str_replace("%U%",$urlAlt, $htmlTplAcaoIni)) . (($podeAlt)	?  '<i class="ace-icon fa fa-edit blue bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlExc		= str_replace("%M%","Excluir"		, str_replace("%U%",$urlExc, $htmlTplAcaoIni)) . (($podeExc)	?  '<i class="ace-icon fa fa-trash red bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlCan		= str_replace("%M%","Cancelar"		, str_replace("%U%",$urlCan, $htmlTplAcaoIni)) . (($podeCan)	?  '<i class="ace-icon fa fa-ban red bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlCon		= str_replace("%M%","Confirmar"		, str_replace("%U%",$urlCon, $htmlTplAcaoIni)) . (($podeCon)	?  '<i class="ace-icon fa fa-check green bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlHis		= str_replace("%M%","Ver Histórico"	, str_replace("%U%",$urlHis, $htmlTplAcaoIni)) . (($podeHis)	?  '<i class="ace-icon fa fa-history grey bigger-140"></i>'		: null) . $htmlTplAcaoFim;
	$htmlImp		= str_replace("%M%","Imprimir"		, str_replace("%U%",$urlImp, $htmlTplAcaoIni)) . (($podeImp)	?  '<i class="ace-icon fa fa-print grey bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	
	$htmlAcao	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="ace-icon fa fa-cog icon-on-right bigger-140"></i></a>
	<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
		<li class="active"><a href="#"><div class="center small bolder blue">Ações para: '.$transferencias[$i]->getDescricao().' ('.$transferencias[$i]->getParcela() . "/".$transferencias[$i]->getNumParcelas().')</div></a></li>
		<li><a href="#">'.$htmlVis.$htmlAlt.$htmlExc.$htmlCan.$htmlCon.$htmlHis.$htmlImp.'</a></li>
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
## Select da Conta de Origem
#################################################################################
try {
	$aConta			= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$oContaOrig		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaOrig , null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Destino
#################################################################################
try {
	$oContaDest		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaDest, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select do Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findAll(array('descricao' => 'ASC'));
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'DESCRICAO',	$codStatus, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= ROOT_URL.'/Fin/transferenciaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta=');

#################################################################################
## Gerar a url de filtro
#################################################################################
$urlFiltroData		= ROOT_URL . "/Fin/transferenciaLisFiltroData.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoFiltro='.$_SESSION["_TRLIS_codTipoFiltro"].'&dataFiltro='.$_SESSION["_TRLIS_dataFiltro"].'&mesFiltro='.$_SESSION["_TRLIS_mesFiltro"].'&dataIniFiltro='.$_SESSION["_TRLIS_dataIniFiltro"].'&dataFimFiltro='.$_SESSION["_TRLIS_dataFimFiltro"]);
$urlFiltro			= ROOT_URL . "/Fin/transferenciaLisFiltro.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Gerar as urls dos botões de ação
#################################################################################
$excUrl		= ROOT_URL . "/Fin/transferenciaExc.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$canUrl		= ROOT_URL . "/Fin/transferenciaCan.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$conUrl		= ROOT_URL . "/Fin/transferenciaConLote.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$impUrl		= ROOT_URL . "/Fin/transferenciaImp.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Transferências"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('FILTER_DATE_URL'	,$urlFiltroData);
$tpl->set('URL_FILTRO'		,$urlFiltro);
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('CHECK_NAME'		,$checkboxName);

$tpl->set('COD_TIPO_FILTRO'	,$_SESSION["_TRLIS_codTipoFiltro"]);
$tpl->set('DATA_TIPO'		,$_SESSION["_TRLIS_dataTipo"]);
$tpl->set('DATA_FILTRO'		,$_SESSION["_TRLIS_dataFiltro"]);
$tpl->set('MES_FILTRO'		,$_SESSION["_TRLIS_mesFiltro"]);
$tpl->set('DATA_INI_FILTRO'	,$_SESSION["_TRLIS_dataIniFiltro"]);
$tpl->set('DATA_FIM_FILTRO'	,$_SESSION["_TRLIS_dataFimFiltro"]);
$tpl->set('VALOR_INI'		,$valorIni);
$tpl->set('VALOR_FIM'		,$valorFim);
$tpl->set('DESCRICAO'		,$descricao);

$tpl->set('EXC_URL'			,$excUrl);
$tpl->set('CAN_URL'			,$canUrl);
$tpl->set('CON_URL'			,$conUrl);
$tpl->set('IMP_URL'			,$impUrl);

$tpl->set('STATUS'			,$oStatus);
$tpl->set('FORMAS_PAG'		,$oFormaPag);
$tpl->set('CONTAS_ORIG'		,$oContaOrig);
$tpl->set('CONTAS_DEST'		,$oContaDest);

$tpl->set('JSON_CODIGOS'	,json_encode($aCodigos));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['mesFiltro']))		$mesFiltro			= \Zage\App\Util::antiInjection($_POST['mesFiltro']);

if (!$mesFiltro) exit;


#################################################################################
## Calcular as datas da pesquisa
#################################################################################
$mes			= substr($mesFiltro,0,2);
$ano			= substr($mesFiltro,3,4);
$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes),1,$ano));
$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+1),0,$ano));
$hoje			= date($system->config["data"]["dateFormat"]);

if (\DateTime::createFromFormat($system->config["data"]["dateFormat"],$hoje) < \DateTime::createFromFormat($system->config["data"]["dateFormat"],$dataFim)) {
	$dataSaldo		= $hoje;
}else{
	$dataSaldo		= $dataFim;
}


#################################################################################
## Resgata as contas não pagas
#################################################################################
$aStatus	= array("A","P");
try {
	$contas	= \Zage\Fin\ContaPagar::busca($dataIni,$dataFim,"V",null,null,null,$aStatus,null,null,null,null,null); 
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta o div com as contas
#################################################################################
$htmlDiv		= "";
$valorTotalMes	= 0;
for ($i = 0; $i < sizeof($contas); $i++) {
	
	#################################################################################
	## Montar os IDs
	#################################################################################
	$uid		= \Zage\App\Util::encodeUrl('&codConta='.$contas[$i]->getCodigo().'&url='.$url);
	$vid		= \Zage\App\Util::encodeUrl('&codConta='.$contas[$i]->getCodigo().'&url='.$url.'&view=1');
	
	#################################################################################
	## Verifica se está vencida
	#################################################################################
	if ($contas[$i]->getDataVencimento() < \DateTime::createFromFormat($system->config["data"]["dateFormat"].' H:i:s',date($system->config["data"]["dateFormat"].' H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))))) {
		$vencida 	= 1;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloVencido();
	}else{
		$vencida 	= 0;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloNormal();
	}
	
	#################################################################################
	## Data de Vencimento
	#################################################################################
	$dataVenc	= $contas[$i]->getDataVencimento()->format($system->config["data"]["datetimeFormat"]);
	
	#################################################################################
	## Parcela / NumParcelas
	#################################################################################
	$parcela	= $contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas();
	
	#################################################################################
	## Status
	#################################################################################
	$status		= "<span class='badge tooltip-".$corStatus." badge-".$corStatus." tooltip-info' data-rel='tooltip' data-placement='top' title='".$contas[$i]->getCodStatus()->getDescricao()."'>".$contas[$i]->getCodStatus()->getCodigo()."</span>";
	
	#################################################################################
	## Valor Total
	#################################################################################
	if ($status == "C") {
		$valTotal	= ( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) + floatval($contas[$i]->getValorOutros()) - (floatval($contas[$i]->getValorDesconto())) );
	}else{
		$valTotal	= ( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) + floatval($contas[$i]->getValorOutros()) - (floatval($contas[$i]->getValorDesconto()) + floatval($contas[$i]->getValorCancelado())) );
	}
	$valorTotalMes	+= $valTotal;
	
	#################################################################################
	## Url de Pagamento
	#################################################################################
	$urlVis			= "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaPagarAlt.php?id=".$vid."');";
	$urlCon			= "javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarPag.php?id=".$uid."');";
	
	#################################################################################
	## Ações
	#################################################################################
	$htmlTplAcaoIni	= '<div class="inline blue center tooltip-info" style="width: 30px;" onclick="%U%" data-toggle="tooltip" data-placement="top" title="%M%">';
	$htmlTplAcaoFim	= '</div>';
	
	$htmlVis		= str_replace("%M%","Visualizar"	, str_replace("%U%",$urlVis, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-search grey bigger-140"></i>' . $htmlTplAcaoFim;
	$htmlCon		= str_replace("%M%","Confirmar"		, str_replace("%U%",$urlCon, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-check green bigger-140"></i>' . $htmlTplAcaoFim;
	
	$htmlDiv	.= '<div class="row small">
		<div class="col-sm-1 center">'.$status.'</div>
		<div class="col-sm-1 center">'.$parcela.'</div>
		<div class="col-sm-5 center">'.$contas[$i]->getDescricao().'</div>
		<div class="col-sm-2 center">'.\Zage\App\Util::toDate($dataVenc).'</div>
		<div class="col-sm-3"><div class="pull-right">'.\Zage\App\Util::to_money($valTotal).'</div></div>
	</div>
	'; 
}

if (!empty($htmlDiv)) {
	$htmlDiv	.= '<div class="row small">
		<div class="col-sm-9"><div class="pull-right"><strong>Total</strong></div></div>
		<div class="col-sm-3 red"><div class="pull-right"><strong>'.\Zage\App\Util::to_money($valorTotalMes).'</strong></div></div>
	</div>
	';
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('CONTAS'			,$htmlDiv);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

<?php
use Zage\App\Aviso\Tipo\Info;
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
global $em,$tr,$system;

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
	\Zage\App\Erro::halt('FALTA PARÂMENTRO : ID');
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
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata informações do formando
#################################################################################
if (!isset($codFormando)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_FORMANDO!');

$formando 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));

if (!$formando){
	\Zage\App\Erro::halt($tr->trans('Ops! Não encontramos o formando selecionado. Por favor, tente novamente!').' (COD_FORMANDO)');
}

#################################################################################
## Resgata os dados de previsão orcamentária
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$contrato	= $em->getRepository('Entidades\ZgadmContrato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

	if ($oOrgFmt)	{
		$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
		$valorArrecadado		= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorArrecadadoFormatura($system->getCodOrganizacao()));
		$valorGasto				= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorGastoFormatura($system->getCodOrganizacao()));
		$pctArrecadado			= ($valorOrcado) ? round(($valorArrecadado * 100) / $valorOrcado,2) : 0;
		$pctGasto				= ($valorOrcado) ? round(($valorGasto * 100) / $valorOrcado,2) : 0;
		$diffPct				= round($pctArrecadado - $pctGasto,2);
		$viewPrevOrc			= null;
	}else{
		$valorOrcado			= 0;
		$valorArrecadado		= 0;
		$valorGasto				= 0;
		$pctArrecadado			= 0;
		$pctGasto				= 0;
		$diffPct				= 0;
		$viewPrevOrc			= "hidden";
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$contas 		= \Zage\Fmt\ContaReceber::listaMensalidadeFormando($formando->getCpf());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GFormandoConta");
$grid->adicionaTexto($tr->trans('STATUS'),			5, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO'),			10, $grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRICAO'),		10, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('PARC.'),			5, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),		10, $grid::CENTER	,'');
$grid->adicionaData($tr->trans('VENCIMENTO'),		10, $grid::CENTER	,'dataVencimento');
$grid->adicionaTexto($tr->trans('FORMA'),			5, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('AÇÕES'),			5	,$grid::CENTER	,'');

$grid->importaDadosDoctrine($contas);

$colStatus	= 0;
$colParcela = 3;
$colValTot	= 4;
$colForma	= 6;
$colAcao	= 7;

#################################################################################
## Resgatar as ações que podem ser feitas por status
#################################################################################
$aStatusAcao	= \Zage\Fin\ContaStatus::getArrayStatusAcao();

#################################################################################
## Resgatar as ações que podem ser feitas por Perfil de conta
#################################################################################
$aPerfilAcao	= \Zage\Fin\ContaPerfil::getArrayPerfilAcao();

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/mensalFormandoLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$formando->getCodigo());
$urlVoltarAcao		= ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.$id.'&codFormando='.$formando->getCodigo();

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($contas); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url.'&urlVoltar='.$urlVoltarAcao);
	$vid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url.'&view=1'.'&urlVoltar='.$urlVoltarAcao);
	
	#################################################################################
	## Status
	#################################################################################
	$vencimento			= $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$numDiasAtraso		= \Zage\Fin\Data::numDiasAtraso($vencimento);
	if ($numDiasAtraso > 0) {
		$vencida 	= 1;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloVencido();
	}else{
		$vencida = 0;
		$corStatus	= $contas[$i]->getCodStatus()->getEstiloNormal();
	}
	
	$grid->setValorCelula($i,$colStatus,"<span class='badge tooltip-".$corStatus." badge-".$corStatus." tooltip-info' data-rel='tooltip' data-placement='top' title='".$contas[$i]->getCodStatus()->getDescricao()."'>".$contas[$i]->getCodStatus()->getCodigo()."</span>");
	
	#################################################################################
	## Parcela / NumParcelas
	#################################################################################
	$grid->setValorCelula($i,$colParcela,$contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas());
	
	#################################################################################
	## Valor Total
	#################################################################################
	if ($status == "C" || $status == "S") {
		$grid->setValorCelula($i,$colValTot,( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) + floatval($contas[$i]->getValorOutros()) - (floatval($contas[$i]->getValorDesconto())) ));
	}else{
		$grid->setValorCelula($i,$colValTot, \Zage\Fin\ContaReceber::calculaValorTotal($contas[$i]));
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
	## Resgata o perfil da conta
	#################################################################################
	$codPerfil	= ($contas[$i]->getCodContaPerfil()) ? $contas[$i]->getCodContaPerfil()->getCodigo() : 0; 
	
	#################################################################################
	## Resgatar o array com as possíveis ações da conta
	#################################################################################
	$aAcoes		= \Zage\Fin\ContaAcao::getArrayAcoes($codPerfil, $contas[$i]->getCodStatus()->getCodigo(),$aStatusAcao, $aPerfilAcao);

	#################################################################################
	## Montar as flags das ações permitidas
	#################################################################################
	$podeAlt	= $aAcoes["ALT"];
	$podeExc	= $aAcoes["EXC"];
	$podeCan	= $aAcoes["CAN"];
	$podeCon	= $aAcoes["CON"];
	$podeHis	= $aAcoes["HIS"];
	$podeImp	= $aAcoes["IMP"];
	$podeSub	= $aAcoes["SUB"];
	$podeBol	= $aAcoes["BOL"];
	
	#################################################################################
	## Ações
	#################################################################################
	$htmlTplAcaoIni	= '<div class="inline blue center tooltip-info" style="width: 30px;" onclick="%U%" data-toggle="tooltip" data-placement="top" title="%M%">';
	$htmlTplAcaoFim	= '</div>';
	
	$urlVis			= "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberAlt.php?id=".$vid."');";
	$urlAlt			= ($podeAlt)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberAlt.php?id=".$uid."');" : null;
	$urlExc			= ($podeExc)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberExc.php?id=".$uid."');" : null;
	$urlCan			= ($podeCan)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberCan.php?id=".$uid."');" : null;
	$urlCon			= ($podeCon)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberRec.php?id=".$uid."');" : null;
	$urlRls			= ($podeHis)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberRecLis.php?id=".$uid."');" : null;
	$urlSub			= ($podeSub)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberSub.php?id=".$uid."&cid=".$cid."');" : null;
	$urlImp			= ($podeImp)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberPri.php?id=".$uid."');" : null;
	
	#################################################################################
	## Verificar se a conta está configurada para emitir boleto
	## Fazer isso verificando se a carteira da conta está preenchida
	#################################################################################
	$podeEmitirBoleto	= \Zage\Fin\ContaReceber::podeEmitirBoleto($contas[$i]);
	$urlBol				= ($podeBol && $podeEmitirBoleto)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/geraBoleto.php?id=".$uid."');" : null;
	if (!$urlBol)		$podeBol	= false;
	
	$htmlVis		= str_replace("%M%","Visualizar"				, str_replace("%U%",$urlVis, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-search grey bigger-140"></i>' . $htmlTplAcaoFim;
	$htmlAlt		= str_replace("%M%","Alterar"					, str_replace("%U%",$urlAlt, $htmlTplAcaoIni)) . (($podeAlt)	?  '<i class="ace-icon fa fa-edit blue bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlExc		= str_replace("%M%","Excluir"					, str_replace("%U%",$urlExc, $htmlTplAcaoIni)) . (($podeExc)	?  '<i class="ace-icon fa fa-trash red bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlCan		= str_replace("%M%","Cancelar"					, str_replace("%U%",$urlCan, $htmlTplAcaoIni)) . (($podeCan)	?  '<i class="ace-icon fa fa-ban red bigger-140"></i>' 				: null) . $htmlTplAcaoFim;
	$htmlCon		= str_replace("%M%","Confirmar"					, str_replace("%U%",$urlCon, $htmlTplAcaoIni)) . (($podeCon)	?  '<i class="ace-icon fa fa-check green bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlHis		= str_replace("%M%","Recebimentos confirmados"	, str_replace("%U%",$urlRls, $htmlTplAcaoIni)) . (($podeHis)	?  '<i class="ace-icon fa fa-usd grey bigger-140"></i>'				: null) . $htmlTplAcaoFim;
	$htmlImp		= str_replace("%M%","Imprimir"					, str_replace("%U%",$urlImp, $htmlTplAcaoIni)) . (($podeImp)	?  '<i class="ace-icon fa fa-print grey bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
	$htmlSub		= str_replace("%M%","Substituir"				, str_replace("%U%",$urlSub, $htmlTplAcaoIni)) . (($podeSub)	?  '<i class="ace-icon fa fa-exchange blue bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
	$htmlBol		= str_replace("%M%","Gerar Boleto"				, str_replace("%U%",$urlBol, $htmlTplAcaoIni)) . (($podeBol)	?  '<i class="ace-icon fa fa-file-pdf-o purple bigger-140"></i>'	: null) . $htmlTplAcaoFim;
	
	$htmlAcao	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="ace-icon fa fa-cog icon-on-right bigger-140"></i></a>
	<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
		<li class="active"><a href="#"><div class="center small bolder blue">Ações para: '.$contas[$i]->getDescricao().' ('.$contas[$i]->getParcela() . "/".$contas[$i]->getNumParcelas().')</div></a></li>
		<li><a href="#">'.$htmlVis.$htmlAlt.$htmlExc.$htmlCan.$htmlCon.$htmlHis.$htmlSub.$htmlImp.$htmlBol.'</a></li>
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Mensalidades"));
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('NOME_FORMANDO'	,$formando->getNome());
$tpl->set('IC'				,$_icone_);


$tpl->set('VALOR_ORCADO'		,\Zage\App\Util::to_money($valorOrcado));
$tpl->set('VALOR_ARRECADADO'	,\Zage\App\Util::to_money($valorArrecadado));
$tpl->set('VALOR_GASTO'			,\Zage\App\Util::to_money($valorGasto));
$tpl->set('PCT_ARRECADADO'		,$pctArrecadado);
$tpl->set('PCT_GASTO'			,$pctGasto);
$tpl->set('PCT_DIFF'			,$diffPct);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
## Resgata os dados do grid
#################################################################################
try {
	//$conviteExtraVenda = \Zage\Fmt\Convite::listaConvitesAlunos($codConvExtra);
	$conviteExtraVenda	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $codFormando), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GVendaLis");
$grid->adicionaTexto($tr->trans('STATUS'),			5, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('TIPO'),			5, $grid::CENTER	,'codVendaTipo:descricao');
$grid->adicionaTexto($tr->trans('NÚMERO'),			5, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),		5, $grid::CENTER	,'');
$grid->adicionaDataHora($tr->trans('EMISSÃO'),		5, $grid::CENTER	,'');
$grid->adicionaData($tr->trans('VENCIMENTO'),		5, $grid::CENTER	,'');
$grid->adicionaDataHora($tr->trans('PAGO EM'),		5, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('FORMA'),			5, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('AÇÕES'),			5	,$grid::CENTER	,'');

$grid->importaDadosDoctrine($conviteExtraVenda);

$colStatus	= 0;
$colNumero	= 2;
$colValTot	= 3;
$colEmissao	= 4;
$colVenc	= 5;
$colPago	= 6;
$colForma	= 7;
$colAcao	= 8;

#################################################################################
## Resgatar as ações que podem ser feitas por status
#################################################################################
$aStatusAcao	= \Zage\Fin\ContaStatus::getArrayStatusAcao();

#################################################################################
## Resgatar as ações que podem ser feitas por Perfil de conta
#################################################################################
$aPerfilAcao	= \Zage\Fin\ContaPerfil::getArrayPerfilAcao();

#################################################################################
## URL voltar
#################################################################################
$urlVoltar	= ROOT_URL . "/Fmt/conviteExtraVendaLis.php?id=".$id."&codFormando=".$codFormando;

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($conviteExtraVenda); $i++) {
	//$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&convExtraVenda='.$conviteExtraVenda[$i]->getCodigo().'&codFormando='.$conviteExtraVenda[$i]->getCodFormando()->getCodigo());
	
	#################################################################################
	## Resgatar a conta (conta a receber)
	#################################################################################
	$oContaRec	= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codTransacao' => $conviteExtraVenda[$i]->getCodTransacao()));
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$oContaRec->getCodigo().'&url='.$url.'&urlVoltar='.$urlVoltar);
	$vid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$oContaRec->getCodigo().'&url='.$url.'&view=1'.'&urlVoltar='.$urlVoltar);
	
	#################################################################################
	## Status
	#################################################################################
	$vencimento			= $oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$numDiasAtraso		= \Zage\Fin\Data::numDiasAtraso($vencimento);
	if ($numDiasAtraso > 0) {
		$vencida 	= 1;
		$corStatus	= $oContaRec->getCodStatus()->getEstiloVencido();
	}else{
		$vencida = 0;
		$corStatus	= $oContaRec->getCodStatus()->getEstiloNormal();
	}
	
	$grid->setValorCelula($i,$colStatus,"<span class='badge tooltip-".$corStatus." badge-".$corStatus." tooltip-info' data-rel='tooltip' data-placement='top' title='".$oContaRec->getCodStatus()->getDescricao()."'>".$oContaRec->getCodStatus()->getCodigo()."</span>");
	
	#################################################################################
	## Número
	#################################################################################
	$grid->setValorCelula($i,$colNumero,$oContaRec->getNumero());
	
	#################################################################################
	## Valor Total
	#################################################################################
	$grid->setValorCelula($i,$colValTot,$oContaRec->getValor());
	
	#################################################################################
	## Emissão
	#################################################################################
	$grid->setValorCelula($i,$colEmissao,$oContaRec->getDataEmissao()->format($system->config["data"]["datetimeSimplesFormat"]));
	
	#################################################################################
	## Vencimento
	#################################################################################
	$grid->setValorCelula($i,$colVenc,$oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]));
	
	#################################################################################
	## Liquidado
	#################################################################################
	if ($oContaRec->getDataLiquidacao()){
		$grid->setValorCelula($i,$colPago,$oContaRec->getDataLiquidacao()->format($system->config["data"]["datetimeSimplesFormat"]));
	}
	
	#################################################################################
	## Forma de pagamento
	#################################################################################
	if ($oContaRec->getCodFormaPagamento()) {
		$formaPag		= $oContaRec->getCodFormaPagamento()->getCodigo();
		$corForma		= $oContaRec->getCodFormaPagamento()->getEstilo();
	}else{
		$formaPag		= null;
		$corForma		= null;
	}
	
	if ($formaPag)  {
		$grid->setValorCelula($i,$colForma,"<span class='badge tooltip-".$corForma." badge-".$corForma."' data-rel='tooltip' data-placement='top' title='".$oContaRec->getCodFormaPagamento()->getDescricao()."'>".$formaPag."</span>");
	}
	
	#################################################################################
	## Resgata o perfil da conta
	#################################################################################
	$codPerfil	= ($oContaRec->getCodContaPerfil()) ? $oContaRec->getCodContaPerfil()->getCodigo() : 0; 
	
	#################################################################################
	## Resgatar o array com as possíveis ações da conta
	#################################################################################
	$aAcoes		= \Zage\Fin\ContaAcao::getArrayAcoes($codPerfil, $oContaRec->getCodStatus()->getCodigo(),$aStatusAcao, $aPerfilAcao);

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
	$podeEmitirBoleto	= \Zage\Fin\ContaReceber::podeEmitirBoleto($oContaRec);
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
		<li class="active"><a href="#"><div class="center small bolder blue">Ações para: '.$oContaRec->getDescricao().' ('.$oContaRec->getParcela() . "/".$oContaRec->getNumParcelas().')</div></a></li>
		<li><a href="#">'.$htmlVis.$htmlAlt.$htmlExc.$htmlCan.$htmlCon.$htmlHis.$htmlSub.$htmlImp.$htmlBol.'</a></li>
	</ul>
	</div>';
	$grid->setValorCelula($i,$colAcao,$htmlAcao);
	
	
	
	//$grid->setUrlCelula($i,5,ROOT_URL.'/Fmt/conviteExtraItemLis.php?id='.$uid);
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
## Url Novo
#################################################################################
$urlVenda = ROOT_URL.'/Fmt/conviteExtraVenda.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/conviteExtraAlunosLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$codFormando);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Venda dos Convites"));
$tpl->set('URLVENDA'		,$urlVenda);
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('NOME_FORMANDO'	,$conviteExtraVenda[0]->getCodFormando()->getNome());
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

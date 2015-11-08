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
## Resgata informações da rifa
#################################################################################
if (!isset($codFormando)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_FORMANDO!');

$formando 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));

if (!$formando){
	\Zage\App\Erro::halt($tr->trans('Ops! Não encontramos o formando selecionado. Por favor, tente novamente!').' (COD_FORMANDO)');
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
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($contas); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url);

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
	## Resgatar o status para controlar as ações
	#################################################################################
	$status		= $contas[$i]->getCodStatus()->getCodigo();
	
	switch ($status) {
	
		case "A":
			$podeAlt	= true;
			$podeExc	= true;
			$podeCan	= true;
			$podeCon	= true;
			$podeRls	= false;
			$podeImp	= true;
			$podeSub	= true;
			$podeBol	= true;
			break;
		case "C":
			$podeAlt	= false;
			$podeExc	= true;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= false;
			$podeImp	= true;
			$podeSub	= false;
			$podeBol	= false;
			break;
		case "L":
		case "EP":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= true;
			$podeImp	= true;
			$podeSub	= false;
			$podeBol	= false;
			break;
		case "SC":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= true;
			$podeImp	= true;
			$podeSub	= false;
			$podeBol	= false;
			break;
		case "S":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= false;
			$podeImp	= true;
			$podeSub	= false;
			$podeBol	= false;
			break;
		case "SS":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= true;
			$podeImp	= true;
			$podeSub	= false;
			$podeBol	= false;
			break;
		case "P":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= true;
			$podeCon	= true;
			$podeRls	= true;
			$podeImp	= true;
			$podeSub	= true;
			$podeBol	= true;
			break;
		default:
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeRls	= false;
			$podeImp	= false;
			$podeSub	= false;
			$podeBol	= false;
			break;
	}
	
	
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
	
	################################################################################
	## Ações
	#################################################################################
	$htmlTplAcaoIni	= '<div class="inline blue center tooltip-info" style="width: 30px;" onclick="%U%" data-toggle="tooltip" data-placement="top" title="%M%">';
	$htmlTplAcaoFim	= '</div>';
	
	$urlVis			= "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberAlt.php?id=".$vid."');";
	$urlAlt			= ($podeAlt)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberAlt.php?id=".$uid."');" : null;
	$urlExc			= ($podeExc)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberExc.php?id=".$uid."');" : null;
	$urlCan			= ($podeCan)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberCan.php?id=".$uid."');" : null;
	$urlCon			= ($podeCon)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberRec.php?id=".$uid."');" : null;
	$urlRls			= ($podeRls)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberRecLis.php?id=".$uid."');" : null;
	$urlSub			= ($podeSub)	? "javascript:zgLoadUrl('".ROOT_URL."/Fin/contaReceberSub.php?id=".$uid."&cid=".$cid."');" : null;
	$urlImp			= ($podeImp)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/contaReceberPri.php?id=".$uid."');" : null;
	
	#################################################################################
	## Verificar se a conta está configurada para emitir boleto
	## Fazer isso verificando se a carteira da conta está preenchida
	#################################################################################
	$contaRec	= $contas[$i]->getCodConta();
	if ( ($contaRec) && ($formaPag == 'BOL') ) {
	if ($contaRec->getCodTipo()->getCodigo() == 'CC' && ($contaRec->getCodCarteira() != null) ) {
	$urlBol		= ($podeBol)	? "javascript:zgAbreModal('".ROOT_URL."/Fin/geraBoleto.php?id=".$uid."');" : null;
	}else{
	$urlBol		= null;
	}
	}else{
	$urlBol		= null;
	}
	
	if (!$urlBol)	$podeBol	= false;
	
	$htmlVis		= str_replace("%M%","Visualizar"				, str_replace("%U%",$urlVis, $htmlTplAcaoIni)) . '<i class="ace-icon fa fa-search grey bigger-140"></i>' . $htmlTplAcaoFim;
	$htmlAlt		= str_replace("%M%","Alterar"					, str_replace("%U%",$urlAlt, $htmlTplAcaoIni)) . (($podeAlt)	?  '<i class="ace-icon fa fa-edit blue bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
				$htmlExc		= str_replace("%M%","Excluir"					, str_replace("%U%",$urlExc, $htmlTplAcaoIni)) . (($podeExc)	?  '<i class="ace-icon fa fa-trash red bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
						$htmlCan		= str_replace("%M%","Cancelar"					, str_replace("%U%",$urlCan, $htmlTplAcaoIni)) . (($podeCan)	?  '<i class="ace-icon fa fa-ban red bigger-140"></i>' 				: null) . $htmlTplAcaoFim;
								$htmlCon		= str_replace("%M%","Confirmar"					, str_replace("%U%",$urlCon, $htmlTplAcaoIni)) . (($podeCon)	?  '<i class="ace-icon fa fa-check green bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
										$htmlRls		= str_replace("%M%","Recebimentos confirmados"	, str_replace("%U%",$urlRls, $htmlTplAcaoIni)) . (($podeRls)	?  '<i class="ace-icon fa fa-usd grey bigger-140"></i>'				: null) . $htmlTplAcaoFim;
										$htmlImp		= str_replace("%M%","Imprimir"					, str_replace("%U%",$urlImp, $htmlTplAcaoIni)) . (($podeImp)	?  '<i class="ace-icon fa fa-print grey bigger-140"></i>' 			: null) . $htmlTplAcaoFim;
										$htmlSub		= str_replace("%M%","Substituir"				, str_replace("%U%",$urlSub, $htmlTplAcaoIni)) . (($podeSub)	?  '<i class="ace-icon fa fa-exchange blue bigger-140"></i>' 		: null) . $htmlTplAcaoFim;
										$htmlBol		= str_replace("%M%","Gerar Boleto"				, str_replace("%U%",$urlBol, $htmlTplAcaoIni)) . (($podeBol)	?  '<i class="ace-icon fa fa-file-pdf-o purple bigger-140"></i>'	: null) . $htmlTplAcaoFim;
	
	$htmlAcao	= '<div class="inline dropdown dropup"><a href="#" data-toggle="dropdown"><i class="ace-icon fa fa-cog icon-on-right bigger-140"></i></a>
	<ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
		<li class="active"><a href="#"><div class="center small bolder blue">Ações para: '.$contas[$i]->getDescricao().' ('.$contas[$i]->getParcela() . "/".$contas[$i]->getNumParcelas().')</div></a></li>
					<li><a href="#">'.$htmlVis.$htmlAlt.$htmlExc.$htmlCan.$htmlCon.$htmlRls.$htmlSub.$htmlImp.$htmlBol.'</a></li>
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
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/mensalFormandoLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/mensalFormandoContaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$formando->getCodigo());

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

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

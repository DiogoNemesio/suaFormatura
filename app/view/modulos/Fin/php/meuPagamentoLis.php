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
global $system,$em,$tr,$_user;


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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$pagamentosAtr	= \Zage\Fmt\Formando::listaPagamentosAtrasados($system->getCodOrganizacao(), $_user->getCpf());
	$pagamentosFut	= \Zage\Fmt\Formando::listaPagamentosAVencer($system->getCodOrganizacao(), $_user->getCpf());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Verifica se precisa mostrar a tabela de pagamentos em atraso
#################################################################################
if (sizeof($pagamentosAtr) == 0) {
	$hidAtr		= "hidden";
}else{
	$hidAtr		= null;
}

#################################################################################
## Verifica se precisa mostrar a tabela de pagamentos futuros
#################################################################################
if (sizeof($pagamentosFut) == 0) {
	$hidFut		= "hidden";
}else{
	$hidFut		= null;
}

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();


#################################################################################
## Popula a tabela de pagamentos em atraso
#################################################################################
$tabAtr		= "";
$totalAtr	= 0;
for ($i = 0; $i < sizeof($pagamentosAtr); $i++) {
	
	
	#################################################################################
	## Formatar campos da conta
	#################################################################################
	$podeBol			= true;
	$codFormaPag		= ($pagamentosAtr[$i]->getCodFormaPagamento() 	!= null) ? $pagamentosAtr[$i]->getCodFormaPagamento()->getCodigo() : null;
	$codContaRec		= ($pagamentosAtr[$i]->getCodConta() 			!= null) ? $pagamentosAtr[$i]->getCodConta() 						: null;
	$vencimento			= ($pagamentosAtr[$i]->getDataVencimento() 		!= null) ? $pagamentosAtr[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
	if (!$vencimento) 										$podeBol	= false;
	if ($codFormaPag	!== "BOL")							$podeBol	= false;
	if (!$codContaRec) 										$podeBol	= false;
	if ($codContaRec->getCodTipo()->getCodigo() !== "CC")	$podeBol	= false;
	if (!$codContaRec->getCodCarteira()) 					$podeBol	= false;
	
	
	#################################################################################
	## Verificar se a conta está atrasada
	#################################################################################
	$vencBol			= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
	$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento,$vencBol);
	$htmlAtraso			= "<i class='fa fa-check-circle green bigger-120'></i>";
	
	#################################################################################
	## Calcular o valor
	#################################################################################
	if (!$contaRec->getValorJaRecebido($pagamentosAtr[$i]->getCodigo())) {
		$valor				= ($pagamentosAtr[$i]->getValor() + $pagamentosAtr[$i]->getValorJuros() + $pagamentosAtr[$i]->getValorMora() + $pagamentosAtr[$i]->getValorOutros() - $pagamentosAtr[$i]->getValorDesconto() - $pagamentosAtr[$i]->getValorCancelado());
	}else{
		$valor				= \Zage\App\Util::to_float($contaRec->getSaldoAReceber($pagamentosAtr[$i]->getCodigo()));
	}
	
	#################################################################################
	## Calcular o Juros e Mora
	#################################################################################
	$_juros				= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$_mora				= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	
	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $pagamentosAtr[$i]->getParcela() . " de ".$pagamentosAtr[$i]->getNumParcelas();
	$juros				= ($_juros + $_mora);
	$totalAtr			+= $valor + $juros;
	$instrucao			= "";
	
	if ($podeBol) {
		$urlDown			= "meuPagBoleto('".$pagamentosAtr[$i]->getCodigo()."','".$vencBol."','".$valor."','".$_juros."','".$_mora."','0','0','PDF','".$instrucao."','');";
		$urlMail			= "meuPagBoleto('".$pagamentosAtr[$i]->getCodigo()."','".$vencBol."','".$valor."','".$_juros."','".$_mora."','0','0','MAIL','".$instrucao."','".$pagamentosAtr[$i]->getCodPessoa()->getEmail()."');";
		$htmlBol			= '
		<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
			<span class="btn btn-sm btn-white btn-info center" onclick="'.$urlDown.'"><i class="fa fa-file-pdf-o bigger-120"></i></span>
			<span class="btn btn-sm btn-white btn-info center" onclick="'.$urlMail.'"><i class="fa fa-envelope bigger-120"></i></span>
		</div>
		';
	}else{
		$htmlBol		= '<i class="icon-only ace-icon fa fa-minus bigger-110"></i>';
	}
	
	
	$tabAtr	.= '<tr>
			<td>'.$pagamentosAtr[$i]->getDescricao().'</td>
			<td style="text-align: center;">'.$parcela.'</td>
			<td style="text-align: center;">'.$vencimento.'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($juros).'</td>
			<td style="text-align: center;">'.$htmlBol.'</td>
			</tr>
	';
}

#################################################################################
## Popula a tabela de pagamentos futuros
#################################################################################
$tabFut		= "";
$numFut		= sizeof($pagamentosFut);
if ($numFut > 5) $numFut = 5;
for ($i = 0; $i < $numFut; $i++) {

	#################################################################################
	## Formatar campos da conta
	#################################################################################
	$podeBol			= true;
	$codFormaPag		= ($pagamentosFut[$i]->getCodFormaPagamento() 	!= null) ? $pagamentosFut[$i]->getCodFormaPagamento()->getCodigo() : null;
	$codContaRec		= ($pagamentosFut[$i]->getCodConta() 			!= null) ? $pagamentosFut[$i]->getCodConta() 						: null;
	$vencimento			= ($pagamentosFut[$i]->getDataVencimento() 		!= null) ? $pagamentosFut[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
	if (!$vencimento) 										$podeBol	= false;
	if ($codFormaPag	!== "BOL")							$podeBol	= false;
	if (!$codContaRec) 										$podeBol	= false;
	if ($codContaRec->getCodTipo()->getCodigo() !== "CC")	$podeBol	= false;
	if (!$codContaRec->getCodCarteira()) 					$podeBol	= false;
	
	
	#################################################################################
	## Verificar se a conta está atrasada
	#################################################################################
	$vencBol			= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
	$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento,$vencBol);
	$htmlAtraso			= "<i class='fa fa-check-circle green bigger-120'></i>";
	
	#################################################################################
	## Calcular o valor
	#################################################################################
	if (!$contaRec->getValorJaRecebido($pagamentosFut[$i]->getCodigo())) {
		$valor				= ($pagamentosFut[$i]->getValor() + $pagamentosFut[$i]->getValorJuros() + $pagamentosFut[$i]->getValorMora() + $pagamentosFut[$i]->getValorOutros() - $pagamentosFut[$i]->getValorDesconto() - $pagamentosFut[$i]->getValorCancelado());
	}else{
		$valor				= \Zage\App\Util::to_float($contaRec->getSaldoAReceber($pagamentosFut[$i]->getCodigo()));
	}
	
	#################################################################################
	## Calcular o Juros e Mora
	#################################################################################
	$_juros				= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($pagamentosFut[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$_mora				= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($pagamentosFut[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	
	
	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $pagamentosFut[$i]->getParcela() . " de ".$pagamentosFut[$i]->getNumParcelas();
	$juros				= ($_juros + $_mora);
	$tabFut	.= '<tr>
			<td>'.$pagamentosFut[$i]->getDescricao().'</td>
			<td style="text-align: center;">'.$parcela.'</td>
			<td style="text-align: center;">'.$vencimento.'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($juros).'</td>
			</tr>
	';
}



#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlHist				= ROOT_URL."/Fin/meuPagamentoHistorico.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('URL_HIST'		,$urlHist);
$tpl->set('HID_ATR'			,$hidAtr);
$tpl->set('HID_FUT'			,$hidFut);
$tpl->set('NUM_FUT'			,$numFut);
$tpl->set('TAB_ATR'			,$tabAtr);
$tpl->set('TAB_FUT'			,$tabFut);
$tpl->set('TOT_ATR'			,\Zage\App\Util::to_money($totalAtr));
$tpl->set('DP'				,ROOT_URL . "/Fin/geraBoletoMidia.php");

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

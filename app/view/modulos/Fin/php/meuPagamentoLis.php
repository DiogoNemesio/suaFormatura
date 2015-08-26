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
## Popula a tabela de pagamentos em atraso
#################################################################################
$tabAtr		= "";
$totalAtr	= 0;
for ($i = 0; $i < sizeof($pagamentosAtr); $i++) {
	$venc		= $pagamentosAtr[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$valor		= ($pagamentosAtr[$i]->getValor() + $pagamentosAtr[$i]->getValorJuros() + $pagamentosAtr[$i]->getValorMora() + $pagamentosAtr[$i]->getValorOutros() - $pagamentosAtr[$i]->getValorDesconto() - $pagamentosAtr[$i]->getValorCancelado());
	$_juros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$_mora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$juros		= ($_juros + $_mora);
	$totalAtr	+= $valor + $juros;
	$tabAtr	.= '<tr>
			<td>'.$pagamentosAtr[$i]->getDescricao().'</td>
			<td style="text-align: center;">('.$pagamentosAtr[$i]->getParcela().'/'.$pagamentosAtr[$i]->getNumParcelas().')</td>
			<td style="text-align: center;">'.$venc.'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($juros).'</td>
	';
}

#################################################################################
## Popula a tabela de pagamentos futuros
#################################################################################
$tabFut		= "";
$numFut		= sizeof($pagamentosFut);
if ($numFut > 5) $numFut = 5;
for ($i = 0; $i < $numFut; $i++) {
	$venc		= $pagamentosFut[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$valor		= ($pagamentosFut[$i]->getValor() + $pagamentosFut[$i]->getValorJuros() + $pagamentosFut[$i]->getValorMora() + $pagamentosFut[$i]->getValorOutros() - $pagamentosFut[$i]->getValorDesconto() - $pagamentosFut[$i]->getValorCancelado());
	$_juros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($pagamentosFut[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$_mora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($pagamentosFut[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$juros		= ($_juros + $_mora);
	$tabFut	.= '<tr>
			<td>'.$pagamentosFut[$i]->getDescricao().'</td>
			<td style="text-align: center;">('.$pagamentosFut[$i]->getParcela().'/'.$pagamentosFut[$i]->getNumParcelas().')</td>
			<td style="text-align: center;">'.$venc.'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: right;">'.\Zage\App\Util::to_money($juros).'</td>
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
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('URL_HIST'		,$urlHist);
$tpl->set('HID_ATR'			,$hidAtr);
$tpl->set('HID_FUT'			,$hidFut);
$tpl->set('NUM_FUT'			,$numFut);
$tpl->set('TAB_ATR'			,$tabAtr);
$tpl->set('TAB_FUT'			,$tabFut);
$tpl->set('TOT_ATR'			,\Zage\App\Util::to_money($totalAtr));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

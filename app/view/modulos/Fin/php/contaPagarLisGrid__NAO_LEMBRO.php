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
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContasPagar");
$checkboxName	= "selItemConta";
$grid->adicionaCheckBox($checkboxName);
$grid->adicionaTexto($tr->trans('NÚMERO'),				7, $grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			15, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('FORNECEDOR'),			20, $grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARCELA'),				7, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('STATUS'),				10, $grid::CENTER	,'codStatus:descricao');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			10, $grid::CENTER	,'');
$grid->adicionaData($tr->trans('EMISSÃO'),				8, $grid::CENTER	,'dataEmissao');
$grid->adicionaData($tr->trans('VENCIMENTO'),			8, $grid::CENTER	,'dataVencimento');
$grid->adicionaTexto($tr->trans('DOCUMENTO'),			10, $grid::CENTER	,'documento');
$grid->adicionaTexto($tr->trans('FORMA PAG'),			15, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaTexto($tr->trans('CCUSTO'),				10, $grid::CENTER	,'codCentroCusto:descricao');
$grid->adicionaIcone(null, "fa fa-search grey", $tr->trans("Visualizar Conta"));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_CANCEL);
$grid->adicionaIcone(null, "fa fa-check green", $tr->trans("Confirmar Pagamento (efetuar baixa da conta)"));
$grid->adicionaIcone(null, "fa fa-history grey", $tr->trans("Visualizar Histórico de Pagamento"));
$grid->adicionaIcone(null, "fa fa-print grey", $tr->trans("Impressão"));

$colParcela	= 4;
$colValTot	= 6;
$colVis		= 12;
$colAlt		= 13;
$colExc		= 14;
$colCan		= 15;
$colPag		= 16;
$colHis		= 17;
$colPri		= 18;

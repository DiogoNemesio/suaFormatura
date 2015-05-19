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
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codTransf)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
$oTransf		= $em->getRepository('Entidades\ZgfinTransferencia')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codTransf));

if (!$oTransf) {
	\Zage\App\Erro::halt($tr->trans('Transferência %s não encontrada !!!',array('%s' => $codTransf)));
}

#################################################################################
## Resgata o Histórico
#################################################################################
$oHist		= $em->getRepository('Entidades\ZgfinHistoricoTransf')->findBy(array('codTransferencia' => $codTransf));

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GTransfHis");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('FORMA PAG'),			15, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaData($tr->trans('DATA'),					8, $grid::CENTER	,'dataTransferencia');
$grid->adicionaMoeda($tr->trans('VALOR'),				10, $grid::CENTER	,'valor');
$grid->adicionaTexto($tr->trans('CONTA ORIGEM'),		10, $grid::CENTER	,'codContaOrigem:nome');
$grid->adicionaTexto($tr->trans('CONTA DESTINO'),		10, $grid::CENTER	,'codContaDestino:nome');
$grid->importaDadosDoctrine($oHist);

$gHtml	= $grid->getHtmlCode();

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/transferenciaLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Calculo dos valores
#################################################################################
$valorTotal			= \Zage\App\Util::to_money($oTransf->getValor());
$valorTransferido	= \Zage\App\Util::to_money(\Zage\Fin\Transferencia::getValorJaTransferido($codTransf)); 
$saldoCanc			= \Zage\App\Util::to_money($oTransf->getValorCancelado());
$saldo				= \Zage\App\Util::to_money(\Zage\Fin\Transferencia::getSaldoATransferir($codTransf) - $oTransf->getValorCancelado());

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Histórico de Transferência'));
$tpl->set('COD_TRANSF'			,$codTransf);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('GRID'				,$gHtml);
$tpl->set('VALOR_TOTAL'			,$valorTotal);
$tpl->set('VALOR_TRANSFERIDO'	,$valorTransferido);
$tpl->set('SALDO'				,$saldo);
$tpl->set('SALDO_CANCELADO'		,$saldoCanc);
$tpl->set('GRID'				,$gHtml);
$tpl->set('URL_VOLTAR'			,$urlVoltar);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


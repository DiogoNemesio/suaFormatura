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
if (!isset($codConta)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));

if (!$oConta) {
	\Zage\App\Erro::halt($tr->trans('Conta %s não encontrada !!!',array('%s' => $codConta)));
}

#################################################################################
## Resgata o Histórico
#################################################################################
$oHist		= $em->getRepository('Entidades\ZgfinHistoricoPag')->findBy(array('codContaPag' => $codConta));

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaHis");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('FORMA PAG'),			12, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaData($tr->trans('DATA PAG'),				8, $grid::CENTER	,'dataPagamento');
$grid->adicionaMoeda($tr->trans('VALOR'),				10, $grid::CENTER	,'valorPago');
$grid->adicionaMoeda($tr->trans('JUROS'),				10, $grid::CENTER	,'valorJuros');
$grid->adicionaMoeda($tr->trans('MORA'),				10, $grid::CENTER	,'valorMora');
$grid->adicionaMoeda($tr->trans('DESCONTO'),			10, $grid::CENTER	,'valorDesconto');
$grid->adicionaMoeda($tr->trans('OUTROS'),				10, $grid::CENTER	,'valorOutros');
$grid->adicionaTexto($tr->trans('CONTA'),				10, $grid::CENTER	,'codConta:nome');
$grid->adicionaTexto($tr->trans('TIPO BAIXA'),			10, $grid::CENTER	,'codTipoBaixa:nome');
$grid->importaDadosDoctrine($oHist);

$gHtml	= $grid->getHtmlCode();

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaPagarLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Calculo dos valores
#################################################################################
$valorTotal			= $oConta->getValor() + $oConta->getValorJuros() + $oConta->getValorMora() + $oConta->getValorOutros() - ($oConta->getValorCancelado() + $oConta->getValorDesconto());
$valorPago			= (new \Zage\Fin\ContaPagar())->getValorJaPago($codConta); 
$saldo				= $valorTotal - $valorPago;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Histórico de Pagamento'));
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('GRID'				,$gHtml);
$tpl->set('VALOR_TOTAL'			,\Zage\App\Util::to_money($valorTotal));
$tpl->set('VALOR_PAGO'			,\Zage\App\Util::to_money($valorPago));
$tpl->set('SALDO'				,\Zage\App\Util::to_money($saldo));
$tpl->set('GRID'				,$gHtml);
$tpl->set('URL_VOLTAR'			,$urlVoltar);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


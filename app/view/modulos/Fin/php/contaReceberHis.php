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
$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));

if (!$oConta) {
	\Zage\App\Erro::halt($tr->trans('Conta %s não encontrada !!!',array('%s' => $codConta)));
}

#################################################################################
## Resgata o Histórico
#################################################################################
$oHist		= $em->getRepository('Entidades\ZgfinHistoricoRec')->findBy(array('codContaRec' => $codConta));

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaHis");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('FORMA PAG'),			12, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaData($tr->trans('DATA REC'),				8, $grid::CENTER	,'dataRecebimento');
$grid->adicionaMoeda($tr->trans('VALOR'),				10, $grid::CENTER	,'valorRecebido');
$grid->adicionaMoeda($tr->trans('JUROS'),				10, $grid::CENTER	,'valorJuros');
$grid->adicionaMoeda($tr->trans('MORA'),				10, $grid::CENTER	,'valorMora');
$grid->adicionaMoeda($tr->trans('DESCONTO'),			10, $grid::CENTER	,'valorDesconto');
$grid->adicionaTexto($tr->trans('CONTA'),				10, $grid::CENTER	,'codConta:nome');
$grid->adicionaTexto($tr->trans('TIPO BAIXA'),			10, $grid::CENTER	,'codTipoBaixa:nome');
$grid->importaDadosDoctrine($oHist);

$gHtml	= $grid->getHtmlCode();

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Calculo dos valores
#################################################################################
$valorTotal			= \Zage\App\Util::to_money($oConta->getValor() + $oConta->getValorJuros() + $oConta->getValorMora() - ($oConta->getValorCancelado() + $oConta->getValorDesconto()));
$valorRecebido		= \Zage\App\Util::to_money((new \Zage\Fin\ContaReceber())->getValorJaRecebido($codConta)); 

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Histórico de Recebimento'));
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('GRID'				,$gHtml);
$tpl->set('VALOR_TOTAL'			,$valorTotal);
$tpl->set('VALOR_RECEBIDO'		,$valorRecebido);
$tpl->set('GRID'				,$gHtml);
$tpl->set('URL_VOLTAR'			,$urlVoltar);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


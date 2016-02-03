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
global $system,$em,$tr;

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
if (!isset($codHist)) \Zage\App\Erro::halt('Falta de Parâmetros 3');

#################################################################################
## Resgata as informações da conta
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
if (!$oConta) \Zage\App\Erro::halt('Conta não encontrada');

#################################################################################
## Resgata as informações da Baixa
#################################################################################
$oHist		= $em->getRepository('Entidades\ZgfinHistoricoPag')->findOneBy(array('codigo' => $codHist));
if (!$oHist) \Zage\App\Erro::halt('Pagamento não encontrado');

#################################################################################
## Verificar se a baixa pertence a conta informada
#################################################################################
if ($codConta != $oHist->getCodContaPag()->getCodigo()) \Zage\App\Erro::halt('Exclusão de baixa indevida, ERR: 01x947');

#################################################################################
## Resgata o perfil da conta
#################################################################################
$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;

if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "EXB")) {
	\Zage\App\Erro::halt('Pagamento não pode ser excluído');
}

#################################################################################
## Gerenciar as URls
#################################################################################
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaPagarLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$aHist			= array($oHist);
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContaHisExc");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaTexto($tr->trans('FORMA PAG'),			15, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaData($tr->trans('DATA PAG'),				8, $grid::CENTER	,'dataPagamento');
$grid->adicionaMoeda($tr->trans('VALOR'),				10, $grid::CENTER	,'valorPago');
$grid->adicionaMoeda($tr->trans('JUROS'),				10, $grid::CENTER	,'valorJuros');
$grid->adicionaMoeda($tr->trans('MORA'),				10, $grid::CENTER	,'valorMora');
$grid->adicionaMoeda($tr->trans('DESCONTO'),			10, $grid::CENTER	,'valorDesconto');
$grid->adicionaMoeda($tr->trans('OUTROS'),				10, $grid::CENTER	,'valorOutros');
$grid->adicionaTexto($tr->trans('CONTA'),				10, $grid::CENTER	,'codConta:nome');
$grid->adicionaTexto($tr->trans('TIPO BAIXA'),			10, $grid::CENTER	,'codTipoBaixa:nome');
$grid->importaDadosDoctrine($aHist);

for ($i = 0; $i < sizeof($aHist); $i++) {
	
	#################################################################################
	## Calcula o valor de Jurose mora
	#################################################################################
	$grid->setValorCelula($i,3,floatval($aHist[$i]->getValorJuros()) );
	$grid->setValorCelula($i,4,floatval($aHist[$i]->getValorMora())  );
}



#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Exclusão de Baixa');
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('COD_HIST'			,$codHist);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('GRID'				,$grid->getHtmlCode());
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

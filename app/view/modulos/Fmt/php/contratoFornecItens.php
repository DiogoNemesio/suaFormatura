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
global $em,$system,$tr;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
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
## Verifica se o parâmetro foi informado
#################################################################################
if (!isset($codGrupoItem))	die("Falta de parâmetros 1");

#################################################################################
## Resgata as informações do grupo de item de orçamento
#################################################################################
try {
	$oGrupoItemOrc	= $em->getRepository('Entidades\ZgfmtPlanoOrcGrupoItem')->findOneBy(array('codigo' => $codGrupoItem));
	if (!$oGrupoItemOrc)	die('Falta de parâmetros 2');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata os itens dess grupo de item de orçamento
#################################################################################
try {
	$oItens			= \Zage\Fmt\Orcamento::listaItensGrupoItemOrc($system->getCodOrganizacao(),$codGrupoItem);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContratoFornecItens");
$grid->adicionaTexto($tr->trans('ITEM'),				30	,$grid::CENTER	,'codItem:item');
$grid->adicionaTexto($tr->trans('QUANTIDADE'),			15	,$grid::CENTER	,'quantidade');
$grid->adicionaMoeda($tr->trans('R$ TOTAL'),			15	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('% CONTRATADO'),		10	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('% PAGO'),				10	,$grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-file-text-o green'		,$tr->trans('Contratar'));
$grid->adicionaIcone(null,'fa fa-dollar red'			,$tr->trans('Gerar o pagamento'));
$grid->importaDadosDoctrine($oItens);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($oItens); $i++) {
	$fid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando=');

	#################################################################################
	## Calcula o valor total do item
	#################################################################################
	$valorItem		= round($oItens[$i]->getQuantidade() * $oItens[$i]->getValorUnitario(),2); 
	$grid->setValorCelula($i,2,$valorItem);
	
	#################################################################################
	## Calcula os percentuais contratado e pago
	#################################################################################
	$pctCon			= "100";
	$pctPag			= "40";
	
	if ($pctCon < 50) {
		$classeCon	= "badge-danger";
	}elseif ($pctCon == 100) {
		$classeCon = "badge-success";
	}else{
		$classeCon = "badge-warning";
	}

	if ($pctPag < 50) {
		$classePag	= "badge-danger";
	}elseif ($pctPag == 100) {
		$classePag = "badge-success";
	}else{
		$classePag = "badge-warning";
	}
	
	
	$grid->setValorCelula($i,3,'<span class="badge '.$classeCon.'" data-rel="tooltip" title="'.$pctCon.'% contratado">'.$pctCon.' %</span>');
	$grid->setValorCelula($i,4,'<span class="badge '.$classePag.'" data-rel="tooltip" title="'.$pctPag.'% contratado">'.$pctPag.' %</span>');
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
## Urls
#################################################################################
$urlVoltar		= ROOT_URL . "/Fmt/contratoFornecLis.php?id=".$id;
$urlAtualizar	= ROOT_URL . "/Fmt/contratoFornecItens.php?id=".$id;

#################################################################################
## Título da página
#################################################################################
$titulo			= "Itens do evento: ".$oGrupoItemOrc->getDescricao();


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('ID'						,$id);
$tpl->set('GRID'					,$htmlGrid);
$tpl->set('TITULO'					,$titulo);
$tpl->set('URL_VOLTAR'				,$urlVoltar);
$tpl->set('URL_ATUALIZAR'			,$urlAtualizar);
$tpl->set('TIPO_EVENTO'				,$oGrupoItemOrc->getDescricao());
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

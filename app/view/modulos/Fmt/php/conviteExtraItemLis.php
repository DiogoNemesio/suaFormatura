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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Fmt/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$convExtraItem		= $em->getRepository('Entidades\ZgfmtConviteExtraItem')->findBy(array('codVenda' => $convExtraVenda), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap) Textos completos	
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GNotifLog");
$grid->adicionaTexto($tr->trans('EVENTO'),			 	15, $grid::CENTER	,'codConviteConf:codTipoEvento:descricao');
$grid->adicionaTexto($tr->trans('QUANTIDADE'),		 	15, $grid::CENTER	,'quantidade');
$grid->adicionaTexto($tr->trans('VALOR UNITÁRIO'),		15, $grid::CENTER	,'valorUnitario');
$grid->importaDadosDoctrine($convExtraItem);

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
$urlInicio			= ROOT_URL.'/Fmt/conviteExtraAlunosLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlVoltar			= ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$codFormando);
$urlAtualizar		= ROOT_URL.'/Fmt/conviteExtraItemLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&convExtraVenda='.$convExtraVenda.'&codFormando='.$codFormando);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Venda Itens'));
$tpl->set('URLVENDA'		,$urlVenda);
$tpl->set('URLINICIO'		,$urlInicio);
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('ASSUNTO'			,"Venda Itens");
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

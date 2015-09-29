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
## Resgata informações das vendas da rifa
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_RIFA');

$info 		= \Zage\Fmt\Rifa::listaVendasRifaFormando($codRifa);

if (!$info){
	\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');
}


#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GVendas");
$grid->adicionaTexto($tr->trans('COD VENDA'),			10, $grid::CENTER	,'codVenda:codigo');
$grid->adicionaDataHora($tr->trans('DATA'),				20, $grid::CENTER	,'data');
$grid->adicionaTexto($tr->trans('COMPRADOR'),			20, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('EMAIL'),				20, $grid::CENTER	,'email');
$grid->adicionaTexto($tr->trans('TELEFONE'),				20, $grid::CENTER	,'telefone');
$grid->importaDadosDoctrine($info);
//$grid->importaDadosArray($info);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($info); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa.'&url='.$url);
	
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
$urlVoltar			= ROOT_URL.'/Fmt/rifaVendaHis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/rifaVendaHisDetalhe.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Histórico de Vendas"));
$tpl->set('NOME_RIFA'		,$info[0]->getCodRifa()->getNome());
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

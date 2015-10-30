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
## Resgata os dados do grid
#################################################################################
try {
	//$convExtraVenda = \Zage\Fmt\Convite::listaConvitesAlunos($codConvExtra);
	$convExtraVenda	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codFormando' => $codFormando), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GNotifLog");
$grid->adicionaTexto($tr->trans('TRANSAÇÃO'),				15, $grid::CENTER	,'codTransacao');
$grid->adicionaTexto($tr->trans('FORMA DE PAGAMENTO'),		15, $grid::CENTER	,'codFormaPagamento:descricao');
$grid->adicionaTexto($tr->trans('VALOR COMPRA'),			15, $grid::CENTER	,'valorTotal');
$grid->adicionaDataHora($tr->trans('DATA VENDA'),			15, $grid::CENTER	,'dataCadastro');
$grid->adicionaIcone(null,'fa fa-info-circle',$tr->trans('Detalhes'));
$grid->importaDadosDoctrine($convExtraVenda);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($convExtraVenda); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&convExtraVenda='.$convExtraVenda[$i]->getCodigo().'&codFormando='.$convExtraVenda[$i]->getCodFormando()->getCodigo());

	$grid->setUrlCelula($i,4,ROOT_URL.'/Fmt/conviteExtraItemLis.php?id='.$uid);
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
## Url Novo
#################################################################################
$urlVenda = ROOT_URL.'/Fmt/conviteExtraVenda.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/conviteExtraAlunosLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$codFormando);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Venda dos Convites"));
$tpl->set('URLVENDA'		,$urlVenda);
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('ASSUNTO'			,"Venda dos Convites");
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

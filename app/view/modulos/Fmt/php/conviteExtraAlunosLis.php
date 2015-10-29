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
	$convExtra = \Zage\Fmt\Convite::listaVendaConviteFormando();
	//$convExtra		= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GConvAlunos");
$grid->adicionaTexto($tr->trans('ALUNO'),		 		20, $grid::CENTER	,'codFormando:nome');
$grid->adicionaTexto($tr->trans('TOTAL VENDAS'),	 	20, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('TOTAL ITENS'),	 		20, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('VALOR TOTAL'),		 	20, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-info-circle',$tr->trans('Detalhes'));
$grid->importaDadosDoctrine($convExtra);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($convExtra); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConvExtra='.$convExtra[$i]->getCodigo().'&url='.$url);
	
	$itens		= $em->getRepository('Entidades\ZgfmtConviteExtraItem')->findBy(array('codVenda' => $convExtra[$i]->getCodigo()), array());
	
	$grid->setValorCelula($i, 1, count($convExtra) );
	$grid->setValorCelula($i, 2, count($itens) );
	$grid->setValorCelula($i, 3, count($convExtra) * $convExtra[$i]->getValorTotal() );
	$grid->setUrlCelula($i, 4, ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.$uid);
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Convite dos Formandos'));
$tpl->set('URLADD'			,'');
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
$grid->adicionaTexto($tr->trans('NÚMERO DE COMPRAS'), 	20, $grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('TOTAL COMPRADO'),		 	20, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-info-circle',$tr->trans('Detalhes'));
$grid->importaDadosDoctrine($convExtra);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($convExtra); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFormando='.$convExtra[$i]->getCodFormando()->getCodigo().'&url='.$url);
	
	$itens		= $em->getRepository('Entidades\ZgfmtConviteExtraVendaItem')->findBy(array('codVenda' => $convExtra[$i]->getCodigo()), array());
	$convVenda	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codFormando' => $convExtra[$i]->getCodFormando()->getCodigo()), array());
	
	#################################################################################
	## Link no nome
	#################################################################################
	$linkNome = 'javascript:zgLoadUrl(\''.ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.$uid.'\');';
	$grid->setValorCelula($i,0,'<a href="'.$linkNome.'">'.$convExtra[$i]->getcodFormando()->getNome().'</a>');
	
	
	$valorTotal = null;
	for ($ii = 0; $ii < sizeof($convVenda); $ii++) {
		$valorTotal += $convVenda[$ii]->getValorTotal() + $convVenda[$ii]->getTaxaConveniencia();
	}
	
	$grid->setValorCelula($i, 1, count($convVenda));
	//$grid->setValorCelula($i, 1, count($itens));
	$grid->setValorCelula($i, 2, $valorTotal );
	$grid->setUrlCelula($i, 3, ROOT_URL.'/Fmt/conviteExtraVendaLis.php?id='.$uid);
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Convite dos Formandos'));
$tpl->set('URLVENDA'		,$urlVenda);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

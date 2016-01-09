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
## Resgata informações de orcamento
#################################################################################
try {
	$orcamento	= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GOrc");
$grid->adicionaTexto($tr->trans('VERSÃO'),	 			20, $grid::CENTER	,'versao');
$grid->adicionaTexto($tr->trans('STATUS'),				20, $grid::CENTER	,'indAtivo');
$grid->adicionaDataHora($tr->trans('DATA CADASTRO'),		20, $grid::CENTER	,'dataCadastro');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($orcamento);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($orcamento); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codVersao='.$orcamento[$i]->getCodigo().'&url='.$url);
	
	if ($orcamento[$i]->getIndAtivo() == 1) {
		$grid->setValorCelula($i, 1, "<span class=\"label label-success\">Ativo</span>");
	}else{
		$grid->setValorCelula($i, 1, "<span class=\"label label-danger\">Inativo</span>");
	}
	
	$grid->setUrlCelula($i,3,ROOT_URL.'/Fmt/planoOrcAlt.php?id='.$uid);
	$grid->setUrlCelula($i,4,"javascript:zgAbreModal('".ROOT_URL.'/Fmt/planoOrcExc.php?id='.$uid."');");
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
$urlAdd			= ROOT_URL.'/Fmt/planoOrcAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codVersao=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Orçamento'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('URL_ATUALIZAR'	,$urlAtualizar);
$tpl->set('IC'				,$_icone_);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

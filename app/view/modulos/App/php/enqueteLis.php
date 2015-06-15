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
$url		= ROOT_URL . "/App/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$enquetes	= $em->getRepository('Entidades\ZgappEnquetePergunta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
} 

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GEnquetes");
$grid->adicionaTexto($tr->trans('CÓDIGO'),			5, $grid::CENTER	,'codigo');
$grid->adicionaTexto($tr->trans('PERGUNTA'),		30, $grid::CENTER	,'pergunta');
$grid->adicionaDataHora($tr->trans('DATA CADASTRO'),	10, $grid::CENTER	,'dataCadastro');
$grid->adicionaDataHora($tr->trans('DATA PRAZO'),		10, $grid::CENTER	,'dataPrazo');
$grid->adicionaTexto($tr->trans('STATUS'),			10, $grid::CENTER	,'codStatus:descricao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaIcone(null,'fa fa-level-up',$tr->trans('Finalizar'));
$grid->adicionaIcone(null,'fa fa-level-up',$tr->trans('Resultados'));
$grid->adicionaIcone(null,'fa fa-level-up',$tr->trans('Resultados'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($enquetes);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($enquetes); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete='.$enquetes[$i]->getCodigo().'&url='.$url);
	$grid->setUrlCelula($i,5,ROOT_URL.'/App/enqueteAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/App/enqueteFin.php?id='.$uid);
	$grid->setUrlCelula($i,7,ROOT_URL.'/App/enqueteResLis.php?id='.$uid);
	$grid->setUrlCelula($i,8,ROOT_URL.'/App/enqueteRes.php?id='.$uid);
	$grid->setUrlCelula($i,9,ROOT_URL.'/App/enqueteExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/App/enqueteAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Enquetes"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

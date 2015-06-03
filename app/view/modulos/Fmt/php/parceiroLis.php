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
$url		= ROOT_URL . '/Rhu/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$parceiro	= $em->getRepository('Entidades\ZgfmtOrganizacao')->findBy(array ('codTipo' => 1), array('nome' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GCargo");
$grid->adicionaTexto($tr->trans('NOME/FANTASIA'),	 	15, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('IDENTIFICAÇÃO'),		15, $grid::CENTER	,'identificacao');
$grid->adicionaTexto($tr->trans('STATUS')	,			15, $grid::CENTER	,'codStatus:descricao');
$grid->adicionaTexto($tr->trans('TIPO'),	 			15, $grid::CENTER	,'codTipoPessoa:descricao');
$grid->adicionaIcone(null,'fa fa-user green',$tr->trans('Cadastro de usuários'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($parceiro);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($parceiro); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codParceiro='.$parceiro[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,4,ROOT_URL.'/Fmt/parceiroUsuarioLis.php?id='.$uid);
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fmt/parceiroAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Fmt/parceiroExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/Fmt/parceiroAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Parceiros Comerciais'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

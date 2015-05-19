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
$url		= ROOT_URL . "/Seg/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$usuarios	= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
$grid->adicionaTexto($tr->trans('USUÁRIO'),	12, $grid::CENTER	,'usuario');
$grid->adicionaTexto($tr->trans('NOME'), 	25, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('STATUS'), 	10, $grid::CENTER	,'codStatus:nome');
$grid->adicionaTexto($tr->trans('EMAIL'), 	30, $grid::CENTER	,'email');
$grid->adicionaTexto($tr->trans('SEXO'), 	10, $grid::CENTER	,'sexo:descricao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->adicionaIcone(null,'fa fa-building',$tr->trans('Configurar acesso as empresas'));
$grid->importaDadosDoctrine($usuarios);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($usuarios); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario='.$usuarios[$i]->getCodigo().'&url='.$url);
	$grid->setUrlCelula($i,5,ROOT_URL.'/Seg/usuarioAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Seg/usuarioExc.php?id='.$uid);
	$grid->setUrlCelula($i,7,ROOT_URL.'/Seg/usuarioEmpresaAlt.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/Seg/usuarioAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Usuários'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

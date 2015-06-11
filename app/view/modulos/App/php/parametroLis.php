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
$url		= ROOT_URL . '/Mco/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$parametro	= $em->getRepository('Entidades\ZgappParametro')->findBy(array(), array('parametro' => 'ASC', 'codModulo' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GParametro");
$grid->adicionaTexto($tr->trans('PARAMETRO'),	 	20, $grid::CENTER	,'parametro');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'), 		30, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('MODULO'),	 		20, $grid::CENTER	,'codModulo:nome');
$grid->adicionaTexto($tr->trans('TIPO'),	 		10, $grid::CENTER	,'codTipo:nome');
$grid->adicionaTexto($tr->trans('USO'),	 			10, $grid::CENTER	,'codUso:nome');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($parametro);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($parametro); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codParametro='.$parametro[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/App/parametroAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/App/parametroExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/App/parametroAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codParametro=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Parâmetros'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

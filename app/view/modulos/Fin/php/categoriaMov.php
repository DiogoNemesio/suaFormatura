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
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (isset($_GET['codCategoria'])) 		$codCategoria			= \Zage\App\Util::antiInjection($_GET['codCategoria']);
if (isset($_GET['codTipo'])) 			$codTipo				= \Zage\App\Util::antiInjection($_GET['codTipo']);

if (!isset($codCategoria)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_CATEGORIA)');
}

if (!isset($codTipo) || ($codTipo != "C" && $codTipo != "D")) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_TIPO)');
}


#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$cat			= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria, 'codOrganizacao' => $system->getcodOrganizacao()));
	
	if (!$cat) 	{
		\Zage\App\Erro::halt($tr->trans('Categoria não existe'));
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}


#################################################################################
## Select das Categorias Pai
#################################################################################
try {
	$aCat	= \Zage\Fin\Categoria::lista($codTipo,null); 
	$oCat	= $system->geraHtmlCombo($aCat,	'CODIGO', 'DESCRICAO',	'', '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Fin/categoriaLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('TITULO'				,$tr->trans('Mover Categoria'));
$tpl->set('ID'					,$id);
$tpl->set('COD_CATEGORIA'		,$cat->getCodigo());
$tpl->set('DESCRICAO'			,$cat->getDescricao());
$tpl->set('COD_TIPO'			,$codTipo);
$tpl->set('CATEGORIAS'			,$oCat);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


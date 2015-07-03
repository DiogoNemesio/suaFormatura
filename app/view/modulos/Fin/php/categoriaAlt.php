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
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros'));
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
if (isset($_GET['codCategoriaPai']))	$codCategoriaPai		= \Zage\App\Util::antiInjection($_GET['codCategoriaPai']);
if (isset($_GET['codCategoria'])) 		$codCategoria			= \Zage\App\Util::antiInjection($_GET['codCategoria']);
if (isset($_GET['codTipo'])) 			$codTipo				= \Zage\App\Util::antiInjection($_GET['codTipo']);

if (isset($codCategoriaPai) && $codCategoriaPai == \Zage\App\Arvore::_codPastaRaiz) {
	$codCategoriaPai	= null;
}

if (!isset($codCategoria) && !isset($codCategoriaPai)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_CATEGORIA)');
}

if (!isset($codTipo) || ($codTipo != "C" && $codTipo != "D")) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_TIPO)');
}

if (!isset($codCategoria)) {
	$codCategoria	= null;
}


#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	if (isset($codCategoriaPai) && $codCategoriaPai != null) {
		$catPai		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoriaPai, 'codOrganizacao' => $system->getcodOrganizacao()));
		if (!$catPai) $catPai			= new \Entidades\ZgfinCategoria();
	}else{
		$catPai		= new \Entidades\ZgfinCategoria();
	}
	
	if (isset($codCategoria) && $codCategoria != null) {
		$cat			= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria, 'codOrganizacao' => $system->getcodOrganizacao()));
		if (!$cat) {
			$cat	= new \Entidades\ZgfinCategoria();
			$ativa			= "checked";
		}else{
			$ativa			= ($cat->getIndAtiva()	== 1) ? "checked" : null;
		}
	}else{
		$cat			= new \Entidades\ZgfinCategoria();
		$ativa			= "checked";
	}

	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Fin/categoriaLis.php?id=".$id."&codCategoria=".$codCategoria;

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
$tpl->set('TITULO'				,$tr->trans('Categorias'));
$tpl->set('ID'					,$id);
$tpl->set('COD_CATEGORIA_PAI'	,$catPai->getCodigo());
$tpl->set('COD_CATEGORIA'		,$cat->getCodigo());
$tpl->set('COD_TIPO'			,$codTipo);
$tpl->set('DESCRICAO'			,$cat->getDescricao());
$tpl->set('ATIVA'				,$ativa);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


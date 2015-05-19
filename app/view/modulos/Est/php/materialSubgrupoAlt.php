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
if (isset($_GET['codGrupo']))		$codGrupo			= \Zage\App\Util::antiInjection($_GET['codGrupo']);
if (isset($_GET['codSubgrupo'])) 	$codSubgrupo		= \Zage\App\Util::antiInjection($_GET['codSubgrupo']);

if (isset($codGrupo) && $codGrupo == \Zage\App\Arvore::_codPastaRaiz) {
	$codGrupo	= null;
}

if (!isset($codSubgrupo) && !isset($codGrupo)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (GRUPO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	if (!isset($codSubgrupo) || empty($codSubgrupo)) {
		$codSubgrupo		= null;
		$subgrupo			= new \Entidades\ZgestSubgrupoMaterial();
	}else{
		$subgrupo			= $em->getRepository('Entidades\ZgestSubgrupoMaterial')->findOneBy(array('codigo' => $codSubgrupo));
		if (!$subgrupo) $subgrupo			= new \Entidades\ZgestSubgrupoMaterial();
		$codGrupo	= ($subgrupo->getCodGrupo() != null) ? $subgrupo->getCodGrupo()->getCodigo() : null;
	}
	
	if (isset($codGrupo) && $codGrupo != null) {
		$subgrupoPai		= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codGrupo));
		if (!$subgrupoPai) $subgrupoPai			= new \Entidades\ZgestGrupoMaterial();
	}else{
		$subgrupoPai		= new \Entidades\ZgestGrupoMaterial();
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Est/materialGrupoLis.php?id=".$id."&codGrupo=".$codSubgrupo;

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
$tpl->set('TITULO'				,$tr->trans('Gerenciamento de Grupo de Material'));
$tpl->set('ID'					,$id);
$tpl->set('COD_GRUPO'			,$subgrupoPai->getCodigo());
$tpl->set('COD_SUBGRUPO'		,$subgrupo->getCodigo());
$tpl->set('DESCRICAO'			,$subgrupo->getDescricao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


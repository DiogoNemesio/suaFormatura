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
if (isset($_GET['codGrupoPai']))	$codGrupoPai		= \Zage\App\Util::antiInjection($_GET['codGrupoPai']);
if (isset($_GET['codGrupo'])) 		$codGrupo			= \Zage\App\Util::antiInjection($_GET['codGrupo']);

if (isset($codGrupoPai) && $codGrupoPai == \Zage\App\Arvore::_codPastaRaiz) {
	$codGrupoPai	= null;
}

if (!isset($codGrupo) && !isset($codGrupoPai)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (GRUPO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	if (!isset($codGrupo) || empty($codGrupo)) {
		$codGrupo		= null;
		$grupo			= new \Entidades\ZgestGrupoMaterial();
	}else{
		$grupo			= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codGrupo));
		if (!$grupo) $grupo			= new \Entidades\ZgestGrupoMaterial();
		$codGrupoPai	= ($grupo->getCodGrupoPai() != null) ? $grupo->getCodGrupoPai()->getCodigo() : null;
	}
	
	if (isset($codGrupoPai) && $codGrupoPai != null) {
		$grupoPai		= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codGrupoPai));
		if (!$grupoPai) $grupoPai			= new \Entidades\ZgestGrupoMaterial();
	}else{
		$grupoPai		= new \Entidades\ZgestGrupoMaterial();
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Est/materialGrupoLis.php?id=".$id."&codGrupo=".$codGrupo;

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
$tpl->set('COD_GRUPO_PAI'		,$grupoPai->getCodigo());
$tpl->set('COD_GRUPO'			,$grupo->getCodigo());
$tpl->set('DESCRICAO'			,$grupo->getDescricao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


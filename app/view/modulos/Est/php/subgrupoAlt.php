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
if (isset($_GET['codGrupoPai']))	$codSubGrupo		= \Zage\App\Util::antiInjection($_GET['codGrupoPai']);
if (isset($_GET['codGrupo'])) 		$codGrupo			= \Zage\App\Util::antiInjection($_GET['codGrupo']);

if (isset($codSubGrupo) && $codSubGrupo == \Zage\App\Arvore::_codPastaRaiz) {
	$codSubGrupo	= null;
}

if (!isset($codGrupo) && !isset($codSubGrupo)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (GRUPO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	if (isset($codSubGrupo) && $codSubGrupo != null) {
		$subgrupo		= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubGrupo));
		if (!$subgrupo) $subgrupo			= new \Entidades\ZgestSubgrupo();
	}else{
		$subgrupo		= new \Entidades\ZgestSubgrupo();
	}
	
	if (isset($codGrupo) && $codGrupo != null) {
		$grupo			= $em->getRepository('Entidades\ZgestGrupo')->findOneBy(array('codigo' => $codGrupo));
		if (!$grupo) $grupo			= new \Entidades\ZgestGrupo();
	}else{
		$grupo			= new \Entidades\ZgestGrupo();
	}
	
	$oOrganizacao	 = $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findAll();
	for ($i = 0; $i < sizeof($oOrganizacao); $i++) {
		$checkOrgTipo .= "<div class=\"checkbox\">
						<label>
							<input name=\"codTipoOrg[$i]\" id=\"codTipoOrg[$i]\" value=".$oOrganizacao[$i]->getCodigo()." class=\"ace ace-checkbox-2\" type=\"checkbox\">
							<span class=\"lbl\"> ".$oOrganizacao[$i]->getDescricao()."</span>
						</label>
					</div>";
	}	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Est/grupoLis.php?id=".$id."&codGrupo=".$codGrupo;

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
$tpl->set('TITULO'				,$tr->trans('Gerenciamento de Grupos'));
$tpl->set('ID'					,$id);
$tpl->set('COD_SUBGRUPO'		,$subgrupo->getCodigo());
$tpl->set('COD_GRUPO'			,$grupo->getCodigo());
$tpl->set('DESCRICAO'			,$subgrupo->getDescricao());
$tpl->set('CHECK_ORG_TIPO'		,$checkOrgTipo);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


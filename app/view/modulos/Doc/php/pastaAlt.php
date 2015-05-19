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
if (isset($_GET['codPastaPai']))	$codPastaPai		= \Zage\App\Util::antiInjection($_GET['codPastaPai']);
if (isset($_GET['codPasta'])) 		$codPasta			= \Zage\App\Util::antiInjection($_GET['codPasta']);

if (isset($codPastaPai) && $codPastaPai == \Zage\App\Arvore::_codPastaRaiz) {
	$codPastaPai	= null;
}

if (!isset($codPasta) && !isset($codPastaPai)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (PASTA)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	if (isset($codPastaPai) && $codPastaPai != null) {
		$pastaPai		= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPastaPai));
		if (!$pastaPai) $pastaPai			= new \Entidades\ZgdocPasta();
	}else{
		$pastaPai		= new \Entidades\ZgdocPasta();
	}
	
	if (isset($codPasta) && $codPasta != null) {
		$pasta			= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPasta));
		if (!$pasta) $pasta			= new \Entidades\ZgdocPasta();
	}else{
		$pasta			= new \Entidades\ZgdocPasta();
	}

	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Doc/docTipoLis.php?id=".$id."&codPasta=".$codPasta;

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
$tpl->set('TITULO'				,$tr->trans('Gerenciamento de Pastas'));
$tpl->set('ID'					,$id);
$tpl->set('COD_PASTA_PAI'		,$pastaPai->getCodigo());
$tpl->set('COD_PASTA'			,$pasta->getCodigo());
$tpl->set('NOME'				,$pasta->getNome());
$tpl->set('DESCRICAO'			,$pasta->getDescricao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
if (isset($_GET['codTipo']))		$codTipo		= \Zage\App\Util::antiInjection($_GET['codTipo']);
if (isset($_GET['codMenuPai'])) 	$codMenuPai		= \Zage\App\Util::antiInjection($_GET['codMenuPai']);
if (isset($_GET['codMenu'])) 		$codMenu		= \Zage\App\Util::antiInjection($_GET['codMenu']);


if (!isset($codTipo)) {
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (codTipo)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	if (isset($codMenuPai) && $codMenuPai != null) {
		$menuPai		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenuPai));
		if (!$menuPai) $menuPai			= new \Entidades\ZgappMenu();
	}else{
		$menuPai		= new \Entidades\ZgappMenu();
	}
	
	if (isset($codMenu) && $codMenu != null) {
		$menu			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));
		if (!$menu) 	$menu	= new \Entidades\ZgappMenu();
	}else{
		$menu			= new \Entidades\ZgappMenu();
	}

	if ($codTipo == "M") {
		$ro		= "readonly";
	}elseif ($codTipo == "L") {
		$ro		= null;
	}else{
		\Zage\App\Erro::halt($tr->trans('Tipo de menu desconhecido'));
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}


#################################################################################
## Select dos Tipos de Menu
#################################################################################
/*try {
	$tipos	= $em->getRepository('Entidades\ZgappMenuTipo')->findAll();
	$oTipos	= $system->geraHtmlCombo($tipos,	'CODIGO', 'NOME', $codTipo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}*/

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL."/Seg/menuLis.php?id=".$id;


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
$tpl->set('TITULO'				,$tr->trans('Gerenciamento de Menu'));
$tpl->set('ID'					,$id);
$tpl->set('COD_MENU_PAI'		,$menuPai->getCodigo());
$tpl->set('COD_MENU'			,$menu->getCodigo());
$tpl->set('NOME'				,$menu->getNome());
$tpl->set('DESCRICAO'			,$menu->getDescricao());
$tpl->set('ICONE'				,$menu->getIcone());
$tpl->set('LINK'				,$menu->getLink());
$tpl->set('READONLY'			,$ro);
$tpl->set('COD_TIPO'			,$codTipo);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


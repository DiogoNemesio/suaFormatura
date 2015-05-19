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
if (isset($_GET['codMenu'])) 		$codMenu	= \Zage\App\Util::antiInjection($_GET['codMenu']);

#################################################################################
## Verifica se os parâmetros estão OK
#################################################################################
if (!isset($codMenu)) 	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_MENU)');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$menu	= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));
	
	if (!$menu) 	{
		\Zage\App\Erro::halt($tr->trans('Menu não existe'));
	}
	
	$menus			= $em->getRepository('Entidades\ZgappMenuPerfil')->findOneBy(array('codMenu' => $codMenu));

	
	if (!empty($menus)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Menu não pode ser excluído, remova as associações aos perfis antes de excluir');
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir o Menu').': <em><b>'.$menu->getNome().'</b></em> ?';
		$classe			= "text-warning";
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Seg/menuLis.php?id=".$id;


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
$tpl->set('PODE_REMOVER'		,$podeRemover);
$tpl->set('TITULO'				,$tr->trans('Exclusão de Menu'));
$tpl->set('ID'					,$id);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('COD_MENU'			,$menu->getCodigo());
$tpl->set('NOME'				,$menu->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


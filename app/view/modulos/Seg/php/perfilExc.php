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
if (!isset($codPerfil)) 	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_PERFIL)');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$perfil		= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	
	if (!$perfil) 	{
		\Zage\App\Erro::halt($tr->trans('Perfil não existe'));
	}
	
	$usuarios		= $em->getRepository('Entidades\ZgsegUsuarioEmpresa')->findOneBy(array('codPerfil' => $codPerfil));
	$menus			= $em->getRepository('Entidades\ZgappMenuPerfil')->findOneBy(array('codPerfil' => $codPerfil));

	
	if (!empty($usuarios)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Perfil não pode ser excluído, existem usuários associados');
		$classe			= "text-danger";
	}elseif (!empty($menus)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Perfil não pode ser excluído, existem menus configurados');
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir o Perfil').': <em><b>'.$perfil->getNome().'</b></em> ?';
		$classe			= "text-warning";
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Seg/perfilLis.php?id=".$id;


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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Perfil'));
$tpl->set('ID'					,$id);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('COD_PERFIL'			,$perfil->getCodigo());
$tpl->set('NOME'				,$perfil->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


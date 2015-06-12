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
if (!isset($codParametro)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_PARAMETRO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codParametro));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Parâmetro não existe'));
	}
	
	$valSis			= $em->getRepository('Entidades\ZgadmParametroSistema')->findOneBy(array('codParametro' => $codParametro));
	$valOrg			= $em->getRepository('Entidades\ZgadmParametroOrganizacao')->findOneBy(array('codParametro' => $codParametro));
	$valUsu			= $em->getRepository('Entidades\ZgadmParametroUsuario')->findOneBy(array('codParametro' => $codParametro));

	if (!empty($valSis)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Parâmetro "%s" está em uso e não pode ser excluído (SISTEMA)',array('%s' => $info->getParametro()));
		$classe			= "text-danger";
	}elseif (!empty($valOrg)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Parâmetro "%s" está em uso e não pode ser excluído (ORGANIZAÇÃO)',array('%s' => $info->getParametro()));
		$classe			= "text-danger";
	}elseif (!empty($valUsu)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Parâmetro "%s" está em uso e não pode ser excluído (USUÁRIO)',array('%s' => $info->getParametro()));
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir o Parâmetro').': <em><b>'.$info->getParametro().'</b></em> ?';
		$classe			= "text-warning";
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/parametroLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . '/templateExc.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('PODE_REMOVER'		,$podeRemover);
$tpl->set('TITULO'				,$tr->trans('Exclusão de Parâmetro'));
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codParametro');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('NOME'				,$info->getParametro());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


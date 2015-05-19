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
if (isset($_GET['codPasta'])) 		{
	$codPasta			= \Zage\App\Util::antiInjection($_GET['codPasta']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (PASTA)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$pasta			= $em->getRepository('Entidades\ZgdocPasta')->findOneBy(array('codigo' => $codPasta));
	
	if (!$pasta) 	{
		\Zage\App\Erro::halt($tr->trans('Pasta não existe'));
	}
	
	$tipos			= \Zage\Doc\DocumentoTipo::lista($codPasta);
	$pastas			= \Zage\Doc\Pasta::lista($codPasta);

	if (!$tipos && !$pastas) {
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir a pasta').': <em><b>%NOME%</b></em> ?';
		$classe			= "text-warning";
	}else{
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Pasta %NOME% não está vazia e não pode ser excluída',array('%NOME%' => $pasta->getNome()));
		$classe			= "text-danger";
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Doc/docTipoLis.php?id=".$id;


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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Pasta'));
$tpl->set('ID'					,$id);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('COD_PASTA'			,$pasta->getCodigo());
$tpl->set('NOME'				,$pasta->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


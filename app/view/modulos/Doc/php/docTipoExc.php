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
if (isset($_GET['codTipoDoc'])) 		{
	$codTipoDoc			= \Zage\App\Util::antiInjection($_GET['codTipoDoc']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (codTipoDoc)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$tipo		= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipoDoc));
	
	if (!$tipo) 	{
		\Zage\App\Erro::halt($tr->trans('Tipo de Documento não existe'));
	}
	
	$indices		= \Zage\Doc\Indice::lista($codTipoDoc);

	if (!$indices) {
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir o Tipo de Documento').': <em><b>%NOME%</b></em> ?';
		$classe			= "text-warning";
	}else{
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Tipo de Documento "'.$tipo->getNome().'" possui índices e não pode ser excluído');
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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Tipo de Documento'));
$tpl->set('ID'					,$id);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('COD_TIPO_DOC'		,$tipo->getCodigo());
$tpl->set('NOME'				,$tipo->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


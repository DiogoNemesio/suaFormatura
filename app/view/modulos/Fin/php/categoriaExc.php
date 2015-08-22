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
if (isset($_GET['codCategoria'])) 		{
	$codCategoria			= \Zage\App\Util::antiInjection($_GET['codCategoria']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (CATEGORIA)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$cat			= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCategoria));
	if (!$cat) 	{
		\Zage\App\Erro::halt($tr->trans('Categoria não existe'));
	}
	
	
	$cats			= \Zage\Fin\Categoria::lista(null,$codCategoria);
	$temConta		= \Zage\Fin\Categoria::estaEmUso($codCategoria);

	if (!$cat->getCodOrganizacao()) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Categoria "%DESCRICAO%" é padrão do sistema e não pode ser excluída !!!',array('%DESCRICAO%' => $cat->getDescricao()));
		$classe			= "text-danger";
	}elseif ($cats) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Categoria "%DESCRICAO%" não está vazia e não pode ser excluída !!!',array('%DESCRICAO%' => $cat->getDescricao()));
		$classe			= "text-danger";
	}elseif ($temConta == true) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Categoria "%DESCRICAO%" está sendo usada em ContaPagar / ContaReceber e não poderá ser excluída !!!',array('%DESCRICAO%' => $cat->getDescricao()));
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir a Categoria: <em><b>%DESCRICAO%</b></em> ? ',array('%DESCRICAO%' => $cat->getDescricao()));
		$classe			= "text-warning";
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar		= ROOT_URL . "/Fin/categoriaLis.php?id=".$id;

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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Categoria'));
$tpl->set('ID'					,$id);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('COD_CATEGORIA'		,$cat->getCodigo());
$tpl->set('DESCRICAO'			,$cat->getDescricao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
if (!isset($codVersao)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_VERSAO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgfmtPlanoOrcamentario')->findOneBy(array('codigo' => $codVersao));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Plano Orçamentário não foi encontrato.'));
	}
	
	$podeRemover	= null;
	$mensagem		= $tr->trans('Deseja realmente excluir a versão').': <b>'.$info->getVersao().'</b> ?';
	$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle red"></i> Está operação excluirá definitivamente este modelo de orçamento.');
	$classe			= "text-warning";

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlVoltar			= ROOT_URL . "/Fmt/planoOrcLis.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . '/templateModalExc.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('PODE_REMOVER'		,$podeRemover);
$tpl->set('TITULO'				,$tr->trans('Excluir'));
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codPlano');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('NOME'				,$info->getVersao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


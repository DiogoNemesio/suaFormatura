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
if (!isset($codEndereco)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (ENDERECO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('codigo' => $codEndereco));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Endereço não existe'));
	}
	
	$movDoc			= $em->getRepository('Entidades\ZgdocMovimentacaoDocumento')->findOneBy(array('codEndereco' => $codEndereco));
	$movDisp		= $em->getRepository('Entidades\ZgdocMovimentacaoDispositivo')->findOneBy(array('codEndereco' => $codEndereco));

	if (!empty($movDoc)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Endereço "%s" está em uso e não pode ser excluído (MOVIMENTAÇÃO DE DOCUMENTOS)',array('%s' => $info->getNome()));
		$classe			= "text-danger";
	}elseif (!empty($movDisp)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Endereço "%s" está em uso e não pode ser excluído (MOVIMENTAÇÃO DE DISPOSITIVOS)',array('%s' => $info->getNome()));
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir o endereço').': <em><b>'.$info->getNome().'</b></em> ?';
		$classe			= "text-warning";
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/enderecoLis.php?id=".$id;

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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Endereço'));
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codEndereco');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('NOME'				,$info->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


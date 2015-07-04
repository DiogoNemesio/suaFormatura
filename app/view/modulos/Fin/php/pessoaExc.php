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
if (!isset($codPessoa)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_PESSOA)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codPessoa));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Pessoa não existe'));
	}
	
	$movPag			= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codPessoa' => $codPessoa));
	$movRec			= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codPessoa' => $codPessoa));

	if (!empty($movPag)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Pessoa "%s" está em uso e não pode ser excluído (CONTAS A PAGAR)',array('%s' => $info->getNome()));
		$classe			= "text-danger";
	}elseif (!empty($movRec)) {
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('Pessoa "%s" está em uso e não pode ser excluído (CONTAS A RECEBER)',array('%s' => $info->getNome()));
		$classe			= "text-danger";
	}else{
		$podeRemover	= null;
		$mensagem		= $tr->trans('Deseja realmente excluir a Pessoa').': <em><b>'.$info->getNome().'</b></em> ?';
		$classe			= "text-warning";
	}
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/pessoaLis.php?id=".$id;

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
$tpl->set('TITULO'				,$tr->trans('Exclusão de Pessoa'));
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codPessoa');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('NOME'				,$info->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


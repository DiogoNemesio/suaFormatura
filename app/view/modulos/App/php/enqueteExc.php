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
if (!isset($codEnquete)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ENQUETE)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info 	 = $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
	$infoR 	 = $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codPergunta' => $codEnquete));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Enquete não existe'));
	}
	
	if ($info->getDataPrazo() < new \DateTime("now")){
		$podeRemover	= 'disabled';
		$mensagem		= $tr->trans('A pergunta "%s" já foi finalizada e não pode ser excluída.',array('%s' => $info->getDescricao()));
		$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle red"></i> Para garantir a integridade da enquete não é possível excluir uma pergunta que já teve seu prazo expirado.');
		$classe			= "text-danger";
	}else{
		if($infoR){
			$podeRemover	= 'disabled';
			$mensagem		= $tr->trans('A pergunta "%s" está em andamento e não pode ser excluída.',array('%s' => $info->getDescricao()));
			$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle red"></i> Para garantir a integridade da enquete não é possível excluir uma pergunta que já possui resposta.');
			$classe			= "text-danger";
		}else{
			$podeRemover	= null;
			$mensagem		= $tr->trans('Deseja realmente excluir a pergunta').': <em><b>'.$info->getDescricao().'</b></em> ?';
			$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle red"></i> Está operação excluirá definitivamente pergunta.');
			$classe			= "text-warning";
		}
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Url do Botão Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/enqueteLis.php?id=".$id;

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
$tpl->set('TITULO'				,$tr->trans('Excluir Pergunta'));
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('MENSAGEM'			,$mensagem);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codEnquete');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('NOME'				,$info->getDescricao());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


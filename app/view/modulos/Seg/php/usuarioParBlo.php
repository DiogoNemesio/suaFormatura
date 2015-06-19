<?php
use Zage\App\Grid\Coluna\Botao;
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
if (!isset($codUsuario)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_USUARIO)');
}

$codOrganizacao = $system->getCodOrganizacao();
if (!isset($codOrganizacao)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ORGANIZACAO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
	$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Usuário não existe'));
	}
	
	if ($oUsuAdm->getCodStatus()->getCodigo() == B){
		$submit			= null;
		$titulo			= 'Desbloqueio de usuário';
		$icone			= '<i class="fa fa-unlock red"></i>';
		$mensagem		= $tr->trans('Deseja realmente desbloquear o usuário').': <b>'.$info->getNome().'</b> ?';
		$observacao		= $tr->trans('Está operação libera o acesso do usuário ao sistema .');
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-unlock bigger-110"></i> Desbloquear ';
		$botaoClasse	= 'btn btn-danger';
	}else{
		$submit			= null;
		$titulo			= 'Bloqueio de usuário';
		$icone			= '<i class="fa fa-lock red"></i>';
		$mensagem		= $tr->trans('Deseja realmente bloquear o usuário').': <b>'.$info->getNome().'</b> ?';
		$observacao		= $tr->trans('Está operação bloqueará o acesso do usuário ao sistema.');
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-lock bigger-110"></i> Bloquear ';
		$botaoClasse	= 'btn btn-danger';
	}
	

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao);
$urlVoltar			= ROOT_URL . "/Seg/usuarioParLis.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . '/templateModal.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('SUBMIT'				,$submit);
$tpl->set('TITULO'				,$titulo);
$tpl->set('ICONE'				,$icone);
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('BOTAO'				,$botao);
$tpl->set('BOTAO_CLASSE'		,$botaoClasse);
$tpl->set('VAR'					,'codUsuario');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('VAR2'				,'codOrganizacao');
$tpl->set('VAR_VALUE2'			,$codOrganizacao);
$tpl->set('NOME'				,$info->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


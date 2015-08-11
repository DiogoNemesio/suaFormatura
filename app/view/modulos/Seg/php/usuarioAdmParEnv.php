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

if (!isset($codOrganizacao)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ORGANIZACAO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
	$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Usuário não existe'));
	}
	
	if ($oUsuOrg->getCodStatus()->getCodigo() == P || $info->getCodStatus()->getCodigo() == P){
		$submit			= null;
		$titulo			= 'Envio de convite';
		$icone			= '<i class="fa fa-envelope orange"></i>';
		$mensagem		= 'Deseja enviar um novo convite para o usuário: <b>'.$info->getNome().'</b> ?';
		$observacao		= '<i class="fa fa-arrow-right orange"></i> Está operação enviará um email com um novo convite para o usuário.';
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-envelope bigger-110"></i> Enviar ';
		$botaoClasse	= 'btn btn-warning';
	} else{
		$submit			= 'disabled';
		$icone			= '<i class="fa fa-envelope orange"></i>';
		$titulo			= 'Envio de convite';
		$enviar			= 'disabled';
		$mensagem		= 'Usuário de <b>'.$info->getNome().'</b> já está associado!';
		$observacao		= '<i class="fa fa-arrow-right orange"></i> Este usuário já possui o cadastro e a associação ativada.';
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-envelope bigger-110"></i> Enviar ';
		$botaoClasse	= 'btn btn-warning';
	}
	

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao);
$urlVoltar			= ROOT_URL . "/Seg/usuarioAdmParLis.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . '/templateModal.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('GRID'				,$htmlGrid);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('TITULO'				,$titulo);
$tpl->set('ICONE'				,$icone);
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('BOTAO'				,$botao);
$tpl->set('SUBMIT'				,$submit);
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


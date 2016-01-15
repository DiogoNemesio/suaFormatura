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
## Resgata os parâmetros passados pelo id
#################################################################################
if (!isset($codVendaPlano)){
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_VENDA_PLANO)');
}

if (!isset($codOrganizacao)){
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros').' (COD_ORGANIZACAO)');
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$info	= $em->getRepository('Entidades\ZgadmOrganizacaoVendaPlano')->findOneBy(array('codigo' => $codVendaPlano));
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));	
	
	if (!$info) 	{
		\Zage\App\Erro::halt($tr->trans('Ops! não conseguimentos encontratar o plano selecionado. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte.'));
	}
	
	if (!$oOrg) 	{
		\Zage\App\Erro::halt($tr->trans('Ops! não conseguimentos encontratar a organização selecionada. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte.'));
	}
	
	if ($info->getIndHabilitado() == 1){
		$icone			= '<i class="fa fa-ban red"></i>';
		$titulo 		= 'Desabilitar';
		$submit			= null;
		$mensagem		= $tr->trans('Deseja realmente desabilitar o plano').': <b>'.$info->getCodPlano()->getNome().'</b> ?';
		$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle red"></i> Está operação desabilitará a comissão deste plano para o parceiro <b> '.$oOrg->getFantasia().'</b>.');
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-ban bigger-110"></i> Desabilitar ';
		$botaoClasse	= 'btn btn-danger';
	}else{
		$icone			= '<i class="fa fa-check green"></i>';
		$titulo 		= 'Habilitar';
		$submit			= null;
		$mensagem		= $tr->trans('Deseja realmente habilitar o plano').': <b>'.$info->getCodPlano()->getNome().'</b> ?';
		$observacao		= $tr->trans('<i class="fa fa-exclamation-triangle orange"></i> Está operação habilitará a comissão deste plano para o parceiro <b> '.$oOrg->getFantasia().'</b>.');
		$classe			= "text-warning";
		$botao			= '<i class="fa fa-check bigger-110"></i> Habilitar ';
		$botaoClasse	= 'btn btn-success';
	}	

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao);
$urlVoltar			= ROOT_URL . "/Adm/parceiroComissaoLis.php?id=".$uid;

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
$tpl->set('PODE_REMOVER'		,$podeRemover);
$tpl->set('TITULO'				,$titulo);
$tpl->set('ID'					,$id);
$tpl->set('TEXTO'				,$mensagem);
$tpl->set('ICONE'				,$icone);
$tpl->set('BOTAO'				,$botao);
$tpl->set('BOTAO_CLASSE'		,$botaoClasse);
$tpl->set('OBSERVACAO'			,$observacao);
$tpl->set('CLASSE'				,$classe);
$tpl->set('VAR'					,'codVendaPlano');
$tpl->set('VAR_VALUE'			,$info->getCodigo());
$tpl->set('VAR2'				,'codOrganizacao');
$tpl->set('VAR_VALUE2'			,$codOrganizacao);
//$tpl->set('NOME'				,$info->getNome());
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
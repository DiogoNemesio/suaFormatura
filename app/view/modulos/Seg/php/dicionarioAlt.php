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


if (!$codDicionario) \Zage\App\Erro::halt($tr->trans('Falta de Parâmetros: %s',array('%s' => 'codDicionario')));

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$info = $em->getRepository('Entidades\ZgsegDicionario')->findOneBy(array('codigo' => $codDicionario));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$nome			= $info->getNome();
$descricao		= $info->getDescricao();
$audit			= ($info->getIndAudit()		== 1) ? "checked" : null;
	
#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Seg/dicionarioLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('ID'					,$id);
$tpl->set('DICIONARIO'			,$codDicionario);
$tpl->set('NOME'				,$nome);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('AUDIT'				,$audit);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
## Variáveis globais
#################################################################################
global $em,$system;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
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
## Resgata as informações da organização que está cadastrando a Formatura
#################################################################################
try {
	$oOrgCer 		= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Verifica se as configurações do cerimonial estão OK
#################################################################################
if (!$oOrgCer) \Zage\App\Erro::halt('Sua organização ainda não está configurada para cadastrar formaturas. Entre em contato com o nosso suporte.');

#################################################################################
## Resgata as informações do banco
#################################################################################
$indAceite			= ($oOrgCer->getIndVendedorAceite() == 1) ? "checked" : null;
$indDesmarcarPadrao	= ($oOrgCer->getIndVendedorDesmarcarPadrao() == 1) ? "checked" : null;
$indDarCortesia		= ($oOrgCer->getIndVendedorDarCortesia() == 1) ? "checked" : null;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLNOVO'					,$urlNovo);
$tpl->set('ID'						,$id);
$tpl->set('ACEITE'					,$indAceite);
$tpl->set('PADÃO'					,$indDesmarcarPadrao);
$tpl->set('CORTESIA'				,$indDarCortesia);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

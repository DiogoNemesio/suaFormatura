<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('includeNoAuth.php');
}

#################################################################################
## Resgata a variável CID que está criptografada
#################################################################################
if (isset($_GET['cid'])) $cid = \Zage\App\Util::antiInjection($_GET["cid"]);
if (!isset($cid))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas');

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($cid);

if (!isset($_cdu01))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 01');
if (!isset($_cdu02))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 02');
if (!isset($_cdu03))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 03');
if (!isset($_cdu04))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 04');
if (!isset($_cdu05))	\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 05');

#################################################################################
## Ajusta os nomes das variáveis
#################################################################################
$codRecSenha	= $_cdu01;
$senhaAlteracao	= $_cdu02;
$email			= $_cdu03;
$codUsuario		= $_cdu04;
$codOrganizacao = $_cdu05;
$log->debug($codOrganizacao);
#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oRecSenha	= $em->getRepository('Entidades\ZgsegUsuarioRecSenha')->findOneBy(array('codigo' => $codRecSenha));
if (!$oRecSenha) 											\Zage\App\Erro::externalHalt('Recuperação de senha não está mais disponível, COD_ERRO: 06');
if ($oRecSenha->getCodStatus()->getCodigo() != "A")			\Zage\App\Erro::externalHalt('Recuperação de senha não está mais disponível, COD_ERRO: 07');
if ($oRecSenha->getSenhaAlteracao() != $senhaAlteracao)		\Zage\App\Erro::externalHalt('Senha não correspondente, COD_ERRO: 08');

$oEmail	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
if (!$oEmail) 												\Zage\App\Erro::externalHalt('Usuario não existe, COD_ERRO: 09');

#################################################################################
## Urls
#################################################################################
$org = $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
$urlRedirecionar	= ROOT_URL . "/".$org->getIdentificacao();

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('REDIRECIONAR'		,$urlRedirecionar);
$tpl->set('CD01'				,$_cdu01);
$tpl->set('CD02'				,$_cdu02);
$tpl->set('CD03'				,$_cdu03);
$tpl->set('CD04'				,$_cdu04);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

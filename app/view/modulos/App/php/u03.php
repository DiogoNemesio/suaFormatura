<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('includeNoAuth.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

#################################################################################
## Resgata a variável CID que está criptografada
#################################################################################
if (isset($_GET['cid'])) $cid = \Zage\App\Util::antiInjection($_GET["cid"]);
if (!isset($cid))				\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas');

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($cid);

if (!isset($_cdu01))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 01');
if (!isset($_cdu02))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 02');
if (!isset($_cdu03))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 03');
if (!isset($_cdu04))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 04');

#################################################################################
## Ajusta os nomes das variáveis
#################################################################################
$codHistEmail	= $_cdu01;
$senhaAlteracao	= $_cdu02;
$emailAlteracao	= $_cdu03;
$codOrganizacao	= $_cdu04;
$hidden 		= "hidden";

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oHistEmail	= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codigo' => $codHistEmail));

if (!$oHistEmail) 													$texto = 'Não existem pendências de confirmação!!!';
elseif ($oHistEmail->getSenhaAlteracao() != $senhaAlteracao)		$texto = 'Senha não corresponde!!!';
elseif ($oHistEmail->getIndConfirmadoNovo() == 1) 					$texto = 'O email já foi confirmado!!!';
else{
	$texto 		= "";
	$hidden  	= "";
}

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
$tpl->set('TEXTO'				,$texto);
$tpl->set('HIDDEN'				,$hidden);
$tpl->set('CD01'				,$_cdu01);
$tpl->set('CD02'				,$_cdu02);
$tpl->set('CD03'				,$_cdu03);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

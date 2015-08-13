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
//if (!isset($_cdu04))		\Zage\App\Erro::externalHalt('Script só pode ser usado por pessoas autorizadas, COD_ERRO: 04');

#################################################################################
## Ajusta os nomes das variáveis
#################################################################################
$codHistEmail	= $_cdu01;
$senhaAlteracao	= $_cdu02;
$emailAlteracao	= $_cdu03;
//$codOrganizacao = $_cdu04;

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oHistEmail	= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codigo' => $codHistEmail));

if (!$oHistEmail) 																$texto = 'Não existem pendências de confirmação!!!';
elseif ($oHistEmail->getSenhaAlteracao() != $senhaAlteracao)					$texto = 'Senha não corresponde!!!';
elseif ($oHistEmail->getIndConfirmadoAnterior() == 1)  							$texto = 'O email já foi confirmado!!!';
elseif ($oHistEmail->getIndConfirmadoAnterior() == 0){
	try {
		$oHistEmail->setIndConfirmadoAnterior(1);
		$oHistEmail->setDataConfirmacaoAnterior(new \DateTime("now"));
		$oHistEmail->setIpConfirmacaoAnterior(\Zage\App\Util::getIPUsuario());
		
		$em->persist($oHistEmail);
		$em->flush();
		$em->detach($oHistEmail);
		
		#################################################################################
		## Gerar a notificação
		#################################################################################
		$cid 			= \Zage\App\Util::encodeUrl('_cdu01='.$oHistEmail->getCodigo().'&_cdu02='.$oHistEmail->getSenhaAlteracao().'&_cdu03='.$oHistEmail->getEmailNovo().'&_cdu04='.$system->getCodOrganizacao());
		$confirmUrl		= ROOT_URL . "/App/u03.php?cid=".$cid;
		$nome			= $oHistEmail->getCodUsuario()->getNome();
		$texto 			= 'O email: '.$emailAlteracao.' foi confirmado com sucesso, uma nova confirmação foi enviada para o novo email.';
		$textoEmail		= "Seu email foi alterado, mas ainda precisa ser confirmado. Para isso, clique no link abaixo:";
		
		$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'USUARIO_CADASTRO'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_ANONIMO);
		$notificacao->setAssunto("Alteração de email");
		$notificacao->setCodRemetente($oRemetente);
		
		//$notificacao->associaUsuario($oHistEmail->getCodUsuario()->getCodigo());
		//$notificacao->enviaSistema();
		$notificacao->enviaEmail();
		$notificacao->setEmail($oHistEmail->getEmailNovo());
		$notificacao->setCodTemplate($template);
		$notificacao->adicionaVariavel("NOME"		, $nome);
		$notificacao->adicionaVariavel("ASSUNTO"	, "Alteração de email");
		$notificacao->adicionaVariavel("TEXTO"		, $textoEmail);
		$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
		
		$notificacao->salva();
		
		$em->flush();
		$em->clear();
	
	} catch (Exception $e) {
		$log->debug("Erro ao enviar o e-mail:". $e->getTraceAsString());
		throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
	}	
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('TEXTO'				,$texto);
$tpl->set('CD01'				,$_cdu01);
$tpl->set('CD02'				,$_cdu02);
$tpl->set('CD03'				,$_cdu03);
//$tpl->set('CD04'				,$_cdu04);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

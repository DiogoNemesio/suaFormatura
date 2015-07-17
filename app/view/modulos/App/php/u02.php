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
$indConfirmado  = $_cdu04;

#################################################################################
## Verificar se os usuário já existe e se já está ativo
#################################################################################
$oHistEmail	= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codigo' => $codHistEmail));

if (!$oHistEmail) 																$texto = 'Não existem pendências de confirmação!!!';
elseif ($oHistEmail->getSenhaAlteracao() != $senhaAlteracao)					$texto = 'Senha não corresponde!!!';
elseif ($indConfirmado != ("A" || "N"))											$texto = 'Redefinição de email não estar mais disponivel!!!';
elseif ($indConfirmado == "A" && $oHistEmail->getIndConfirmadoAnterior() == 1)  $texto = 'O email já foi confirmado!!!';
elseif ($indConfirmado == "N" && $oHistEmail->getIndConfirmadoNovo() == 1) 		$texto = 'O email já foi confirmado!!!';
elseif ($indConfirmado == "A" && $oHistEmail->getIndConfirmadoAnterior() == 0){
	$oHistEmail->setIndConfirmadoAnterior(1);
	$oHistEmail->setDataConfirmacaoAnterior(new \DateTime("now"));
	$oHistEmail->setIpConfirmacaoAnterior(\Zage\App\Util::getIPUsuario());
	
	$em->persist($oHistEmail);
	$em->flush();
	$em->detach($oHistEmail);
	
	$texto 		= 'O email: '.$emailAlteracao.' foi confirmado com sucesso, uma nova confirmação foi enviada para o novo email.';
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oHistEmail->getCodigo().'&_cdu02='.$oHistEmail->getSenhaAlteracao().'&_cdu03='.$oHistEmail->getEmailNovo().'&_cdu04=N');
	$textoEmail	= "Seu email foi alterado, mas ainda precisa ser confirmado. Para isso, clique no link abaixo:";
	
	#################################################################################
	## Carregando o template html do email
	#################################################################################
	$tpl		= new \Zage\App\Template();
	
	$tpl->load(MOD_PATH . "/App/html/perfilConfirmEmail.html");
	$assunto			= "Alteração de email";
	$nome				= $oHistEmail->getCodUsuario()->getNome();
	$confirmUrl			= ROOT_URL . "/App/u02.php?cid=".$cid;
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('ID'					,$id);
	$tpl->set('CONFIRM_URL'			,$confirmUrl);
	$tpl->set('ASSUNTO'				,$assunto);
	$tpl->set('TEXTO'				,$textoEmail);
	$tpl->set('NOME'				,$nome);
	#################################################################################
	## Criar os objeto do email ,transporte e validador
	#################################################################################
	$mail 			= \Zage\App\Mail::getMail();
	$transport 		= \Zage\App\Mail::getTransport();
	$validator 		= new \Zend\Validator\EmailAddress();
	$htmlMail 		= new MimePart($tpl->getHtml());
	$htmlMail->type = "text/html";
	$body 			= new MimeMessage();
	
	#################################################################################
	## Definir o conteúdo do e-mail
	#################################################################################
	$body->setParts(array($htmlMail));
	$mail->setBody($body);
	$mail->setSubject("<ZageMail> ".$assunto);
	
	#################################################################################
	## Definir os destinatários
	#################################################################################
	$mail->addTo($oHistEmail->getEmailNovo());
	$tpl = null;
	#################################################################################
	## Salvar as informações e enviar o e-mail
	#################################################################################
	try {
		$transport->send($mail);
	} catch (Exception $e) {
		$log->debug("Erro ao enviar o e-mail:". $e->getTraceAsString());
		throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
	}
}
elseif ($indConfirmado == "N" && $oHistEmail->getIndConfirmadoNovo() == 0){
	$oHistEmail	= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codigo' => $codHistEmail));
	try {
		$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $oHistEmail->getCodUsuario()->getCodigo()));
	
		$oUsuario->setUsuario($emailAlteracao);
		
		$em->persist($oUsuario);
		
	} catch (\Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}
	
	$oStatus	= $em->getRepository('Entidades\ZgsegHistEmailStatus')->findOneBy(array('codigo' => 'F'));
	try {
		$oHistEmail->setCodStatus($oStatus);
		$oHistEmail->setIndConfirmadoNovo(1);
		$oHistEmail->setDataConfirmacaoNovo(new \DateTime("now"));
		$oHistEmail->setIpConfirmacaoNovo(\Zage\App\Util::getIPUsuario());
	
		$em->persist($oHistEmail);
		$em->flush();
		$em->detach($oHistEmail);
		$em->detach($oUsuario);
	
	} catch (\Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}
	$texto 	= 'O email: '.$oHistEmail->getEmailNovo().' foi confirmado com sucesso.';
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
$tpl->set('CD04'				,$_cdu04);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

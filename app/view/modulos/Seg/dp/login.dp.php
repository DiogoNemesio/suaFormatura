<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
 	include_once('../includeNoAuth.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['email'])) 			$email				= \Zage\App\Util::antiInjection($_POST['email']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Email **/
if (!isset($email) || (empty($email))) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo EMAIL é obrigatório !!!"))));
	$err	= 1;
}

$valEmail	= new \Zage\App\Validador\Email();
if ($valEmail->isValid($email) == false){
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("EMAIL inválido !!!"))));
	$err	= 1;
}

$oEmail	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $email));

if (($oEmail != null) && ($oEmail->getUsuario() == $email)){
	$codUsuario = $oEmail;
	$log->debug('email existe');
}else{
	$log->debug('email nao existe');
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Email não existe!"))));
	$err 	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	$oRecSenha	= $em->getRepository('Entidades\ZgsegUsuarioRecSenha')->findOneBy(array('codUsuario' => $codUsuario, 'codStatus' => 'A'));
	if (!$oRecSenha){
		$oRecSenha	= new \Entidades\ZgsegUsuarioRecSenha();
	}else{
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Existe confirmação de email pendende!"))));
		exit;
	}
	
 	if (!$oRecSenha) $oRecSenha	= new \Entidades\ZgsegUsuarioRecSenha();
 	
 	$oStatus	= $em->getRepository('Entidades\ZgsegHistEmailStatus')->findOneBy(array('codigo' => 'A'));
 	
 	$oRecSenha->setCodUsuario($codUsuario);
 	$oRecSenha->setDataSolicitacao(new \DateTime("now"));
 	$oRecSenha->setIpSolicitacao(\Zage\App\Util::getIPUsuario());
 	$oRecSenha->setSenhaAlteracao(\Zage\Seg\Perfil::_geraSenha());
 	$oRecSenha->setCodStatus($oStatus);
 	
 	$em->persist($oRecSenha);
 	$em->flush();
 	$em->detach($oRecSenha);
 	
 	//$texto 		= 'O email: '.$email.' foi confirmado com sucesso, uma nova confirmação foi enviada para o novo email.';
 	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oRecSenha->getCodigo().'&_cdu02='.$oRecSenha->getSenhaAlteracao().'&_cdu03='.$oEmail->getUsuario().'&_cdu04='.$oRecSenha->getCodUsuario()->getCodigo());
 	$textoEmail	= "Seu email foi confirmado, mas ainda precisa alterar a senha. Para isso, clique no link abaixo:";

 	#################################################################################
 	## Carregando o template html do email
 	#################################################################################
 	$tpl		= new \Zage\App\Template();
 	
 	$tpl->load(MOD_PATH . "/App/html/perfilConfirmEmail.html");
 	$assunto			= "Alteração de email";
 	$nome				= $oRecSenha->getCodUsuario()->getNome();
 	$confirmUrl			= ROOT_URL . "/Seg/u04.php?cid=".$cid;
 	 	
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
 	$mail->addTo($email);
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
	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
die ('0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Email enviado com sucesso!"))));

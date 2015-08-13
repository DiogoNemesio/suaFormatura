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
}else{
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
 	
 	#################################################################################
 	## Gerar a notificação
 	#################################################################################
 	$cid 			= \Zage\App\Util::encodeUrl('_cdu01='.$oRecSenha->getCodigo().'&_cdu02='.$oRecSenha->getSenhaAlteracao().'&_cdu03='.$oEmail->getUsuario().'&_cdu04='.$oRecSenha->getCodUsuario()->getCodigo().'&_cdu05='.$system->getCodOrganizacao());
 	$assunto		= "Redefinição da senha.";
 	$textoEmail		= "Seu email foi confirmado, mas ainda precisa alterar a senha. Para isso, clique no link abaixo:";
 	$nome			= $oRecSenha->getCodUsuario()->getNome();
 	$confirmUrl		= ROOT_URL . "/Seg/u04.php?cid=".$cid;
 	
 	$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
 	$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'USUARIO_CADASTRO'));
 	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
 	$notificacao->setAssunto("Redefinição da senha.");
 	$notificacao->setCodRemetente($oRemetente);
 	
 	$notificacao->associaUsuario($oRecSenha->getCodUsuario()->getCodigo());
 	$notificacao->enviaEmail();
 	//$notificacao->enviaSistema();
 	//$notificacao->setEmail($oHistEmail->getEmailNovo()); # Se quiser mandar com cópia
 	$notificacao->setCodTemplate($template);
 	$notificacao->adicionaVariavel("NOME"		, $nome);
 	$notificacao->adicionaVariavel("ASSUNTO"	, "Redefinição da senha.");
 	$notificacao->adicionaVariavel("TEXTO"		, $textoEmail);
 	$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
 	
 	$notificacao->salva();
 	
 	$em->flush();
 	$em->clear();
	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
die ('0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Email enviado com sucesso!"))));

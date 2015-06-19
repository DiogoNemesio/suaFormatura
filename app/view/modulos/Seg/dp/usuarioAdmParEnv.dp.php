<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codUsuario'])) 		$codUsuario			= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['codOrganizacao']))	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificações
#################################################################################

try {
	/*** Verificar parâmetros ***/
	if (!isset($codUsuario) || (!$codUsuario)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_USUARIO"))));
		$err	= 1;
	}
	
	if (!isset($codOrganizacao) || (!$codOrganizacao)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_ORGANIZACAO"))));
		$err	= 1;
	}
	
	/*** Verificar se o usuario existe ***/
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrado"))));
		$err	= 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuario->getCodStatus()->getCodigo() != P && $oUsuOrg->getCodStatus()->getCodigo() != P ){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Está operação não pode ser concluída, porque não foi idêntificado pendências."))));
			$err = 1;
		}
	}
	
	if ($err != null) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
#################################################################################
## CRIAR CONVITE
#################################################################################
$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));

$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
$convite		= new \Zage\Seg\Convite();
$convite->setCodOrganizacaoOrigem($oOrg);
$convite->setCodUsuarioDestino($oUsuario);
$convite->setCodStatus($oConviteStatus);
$convite->salvar();

#################################################################################
## Salvar as informações
#################################################################################
try {
	$em->flush();
	$em->clear();
} catch (Exception $e) {
	$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
	throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
}

#################################################################################
## Carregando o template html do email
#################################################################################
$tpl		= new \Zage\App\Template();
$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
if ($oUsuario->getCodStatus()->getCodigo() == P) {
	$tpl->load(MOD_PATH . "/Seg/html/usuarioCadEmail.html");
	$assunto			= "Cadatro de usuário";
	$nome				= $oUsuario->getNome();
	$texto				= "Sua conta foi criada, mas ainda precisa ser confirmada. Para isso, clique no link abaixo:";
	$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
}else{
	$tpl->load(MOD_PATH . "/Seg/html/usuarioCadAssocEmail.html");
	$assunto			= "Associação a empresa";
	$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
}

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('CONFIRM_URL'			,$confirmUrl);
$tpl->set('ASSUNTO'				,$assunto);
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
$mail->addTo($oUsuario->getUsuario());

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
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Convite enviado com sucesso!')));
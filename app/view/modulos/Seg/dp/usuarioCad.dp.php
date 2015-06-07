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
if (isset($_POST['codOrganizacao'])) 	$codOrganizacao	= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['email'])) 			$usuario		= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['codPerfil'])) 		$codPerfil		= \Zage\App\Util::antiInjection($_POST['codPerfil']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

/** Nome **/
if (isset($nome) || !empty($nome)) {
	if (strlen($nome) < 5){
		if(strlen($nome) == 0){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome deve ser preenchido!"));
			$err	= 1;
		}else{
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome muito pequeno, informe o nome completo !!"));
			$err	= 1;
		}
	}elseif (strlen($nome) > 60){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome tem limite de 60 caracteres !!"));
		$err	= 1;
	}
}

/** Usuário (email) **/
if (isset($usuario) || !empty($usuario)) {
	if (strlen($usuario) == 0){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Email deve ser preenchido!"));
		$err	= 1;
	}elseif(\Zage\App\Util::validarEMail($usuario) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email inválido"));
		$err	= 1;
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Email deve ser preenchido!"));
	$err	= 1;
}

/** Perfil **/
if (!isset($codPerfil) || empty($codPerfil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Perfil deve ser informado!"));
	$err	= 1;
}

/** Organização **/
if (!isset($codOrganizacao) || empty($codOrganizacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Organização deve ser informada!"));
	$err	= 1;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	#################################################################################
	## Verificar se o usuário já existe
	#################################################################################
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $usuario));
	
	
	if (!$oUsuario) {
		$novoUsuario	= true;
		
		#################################################################################
		## Criar o usuário com o status pendente
		#################################################################################
		$oUsuario	= new \Entidades\ZgsegUsuario();
		$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
		$oUsuario->setUsuario($usuario);
		$oUsuario->setNome($nome);
		$oUsuario->setCodStatus($oStatus);
		
		$em->persist($oUsuario);
		
	}else{
		$novoUsuario	= false;
		#################################################################################
		## Verificar se o usuário já está associado a organização
		#################################################################################
		$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));
		
		if ($oUsuOrg) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email já associado a organização"));
			die ('1'.\Zage\App\Util::encodeUrl('||'));
		}
	}
	
	
	#################################################################################
	## Usuário - Organização
	#################################################################################
	$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	
	$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
	
	$oUsuarioOrg->setCodUsuario($oUsuario);
	$oUsuarioOrg->setCodOrganizacao($oOrg);
	$oUsuarioOrg->setCodPerfil($oPerfil);
	$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);
	
	$em->persist($oUsuarioOrg);
	
	
	#################################################################################
	## Cria o convite
	#################################################################################
	$convite		= new \Zage\Seg\Convite();
	$convite->setCodOrganizacaoOrigem($oOrg);
	$convite->setCodUsuarioDestino($oUsuario);
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
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuarioOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
	if ($novoUsuario) {
		$tpl->load(MOD_PATH . "/Seg/html/usuarioCadEmail.html");
		$assunto			= "Cadatro de usuário";
		$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
	}else{
		$tpl->load(MOD_PATH . "/Seg/html/usuarioCadAssocEmail.html");
		$assunto			= "Associação a empresa";
		$confirmUrl			= ROOT_URL . "/Seg//u02.php?cid=".$cid;
	}
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('ID'					,$id);
	$tpl->set('CONFIRM_URL'			,$confirmUrl);
	$tpl->set('ASSUNTO'				,$assunto);
	
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
	$mail->addTo($usuario);
	
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


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

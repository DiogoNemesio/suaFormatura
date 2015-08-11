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
$codOrganizacao		= $system->getCodOrganizacao();

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
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}
	
	#################################################################################
	## Salvar notificação
	#################################################################################
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
	if ($oUsuario->getCodStatus()->getCodigo() == P) {
		$assunto			= "Confirmação de cadastro";
		$template			= 'USUARIO_CADASTRO';
		$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
		$texto = 'Você foi adionado a empresa <b>'.$oOrg->getNome().'</b>. Para concluir o seu cadastro é necessário confimar seus dados.';
	}else{
		$assunto			= "Associação a uma nova empresa";
		$template			= 'USUARIO_CADASTRO';
		$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
		$texto = 'Identificamos que você já é usuário do portal SUAFORMATURA.COM. Confirme seu cadastro para acessar tudo sobre sua nova empresa <b>'.$oOrg->getNome().'</b>.';
	}
	
	//$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
	$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => $template));
	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
	$notificacao->setAssunto($assunto);
	//$notificacao->setCodRemetente($oRemetente);
	
	$notificacao->associaUsuario($oUsuario->getCodigo());
	
	$notificacao->enviaEmail();
	//$notificacao->enviaSistema();
	//$notificacao->setEmail("daniel.cassela@usinacaete.com"); # Se quiser mandar com cópia
	$notificacao->setCodTemplate($template);
	$notificacao->adicionaVariavel('ID', $id);
	$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
	$notificacao->adicionaVariavel("ASSUNTO", $assunto);
	$notificacao->adicionaVariavel("NOME", $oUsuario->getNome());
	$notificacao->adicionaVariavel("TEXTO", $texto);
	$notificacao->salva();
	
	#################################################################################
	## Salvar notificação
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}


} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.'Convite enviado com sucesso!');
<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
 	include_once('../includeNoAuth.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$log,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['_cdu01'])) 			$_cdu01		= \Zage\App\Util::antiInjection($_POST['_cdu01']);
if (isset($_POST['_cdu02'])) 			$_cdu02		= \Zage\App\Util::antiInjection($_POST['_cdu02']);
if (isset($_POST['_cdu03'])) 			$_cdu03		= \Zage\App\Util::antiInjection($_POST['_cdu03']);
if (isset($_POST['_cdu04'])) 			$_cdu04		= \Zage\App\Util::antiInjection($_POST['_cdu04']);

$codRecSenha	= $_cdu01;
$senhaAlteracao	= $_cdu02;
$email			= $_cdu03;
$codUsuario		= $_cdu04;

if (isset($_POST['novaSenha'])) 		$senhaNova	= \Zage\App\Util::antiInjection($_POST['novaSenha']);
if (isset($_POST['confNovaSenha'])) 	$confSenha	= \Zage\App\Util::antiInjection($_POST['confNovaSenha']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Senha **/
if (($senhaNova) && (strlen($senhaNova) < 4)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Senha inválido!"))));
	$err		= "1";
}elseif ($senhaNova) {
	$senhaCrypt	= \Zage\App\Crypt::crypt($email, $senhaNova);
}

if ($senhaNova !== $confSenha) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Confirmação de senha inválida!"))));
	$err		= "1";
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
	
	$oUsuario->setSenha($senhaCrypt);
	$oUsuario->setIndTrocarSenha(0);
	$em->persist($oUsuario);
	//$em->flush();
	//$em->detach($oUsuario);
	
	$oStatus	= $em->getRepository('Entidades\ZgsegHistEmailStatus')->findOneBy(array('codigo' => 'F'));
	try {
		$oRecEmail	= $em->getRepository('Entidades\ZgsegUsuarioRecSenha')->findOneBy(array('codigo' => $codRecSenha));
		
		$oRecEmail->setCodStatus($oStatus);
		$oRecEmail->setDataAlteracao(new \DateTime("now"));
		$oRecEmail->setIpAlteracao(\Zage\App\Util::getIPUsuario());
	
		$em->persist($oRecEmail);
		$em->flush();
		$em->detach($oRecEmail);
		$em->detach($oUsuario);
	
	} catch (\Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

die ('0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Senha alterada com sucesso, aguarde que você será redirecionado!"))));

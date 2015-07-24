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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['_cdu01'])) 			$_cdu01		= \Zage\App\Util::antiInjection($_POST['_cdu01']);
if (isset($_POST['_cdu02'])) 			$_cdu02		= \Zage\App\Util::antiInjection($_POST['_cdu02']);
if (isset($_POST['_cdu03'])) 			$_cdu03		= \Zage\App\Util::antiInjection($_POST['_cdu03']);
if (isset($_POST['_cdu04'])) 			$_cdu04		= \Zage\App\Util::antiInjection($_POST['_cdu04']);

$codHistEmail	= $_cdu01;
$senhaAlteracao	= $_cdu02;
$emailAlteracao	= $_cdu03;
$codOrganizacao = $_cdu04;

if (isset($_POST['novaSenha'])) 		$senhaNova	= \Zage\App\Util::antiInjection($_POST['novaSenha']);
if (isset($_POST['senhaAtual'])) 		$senhaAtual	= \Zage\App\Util::antiInjection($_POST['senhaAtual']);
if (isset($_POST['confNovaSenha'])) 	$confSenha	= \Zage\App\Util::antiInjection($_POST['confNovaSenha']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if ((!isset($senhaAtual))  || (!$senhaAtual)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Senha atual não informada!"))));
	$err	= 1;
}

/** Senha **/
if (($senhaNova) && (strlen($senhaNova) < 5)) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Senha inválido!"))));
	$err		= "1";
}elseif ($senhaNova) {
	$senhaCrypt	= \Zage\App\Crypt::crypt($emailAlteracao, $senhaNova, $codOrganizacao);
}

if ($senhaNova !== $confSenha) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Confirmação de senha inválida!"))));
	$err		= "1";
}

if ($senhaNova == $senhaAtual) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A nova senha deve ser diferente da atual!"))));
	$err		= "1";
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}
exit;

#################################################################################
## Salvar no banco
#################################################################################
try {
	$oHistEmail	= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codigo' => $codHistEmail));
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $oHistEmail->getCodUsuario()->getCodigo()));
	
	$oUsuario->setSenha($senhaCrypt);
	$oUsuario->setIndTrocarSenha(0);
	$oUsuario->setUsuario($emailAlteracao);
	$em->persist($oUsuario);
	//$em->flush();
	//$em->detach($oUsuario);
	
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

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Senha alterada com sucesso!"))));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

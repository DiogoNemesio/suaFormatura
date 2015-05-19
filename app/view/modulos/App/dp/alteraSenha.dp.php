<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
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
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Senha atual não informada"));
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Senha atual não informada"))));
}

#################################################################################
## Checa se a senha atual está correta
#################################################################################
$senhaCrip	= \Zage\App\Crypt::crypt($_user->getUsuario(), $senhaAtual,$system->getCodOrganizacao());
$authAdap	= new \Zage\Seg\Auth($_user->getUsuario(),$senhaCrip,$system->getCodOrganizacao());
$result		= $authAdap->authenticate();

if (!$result->isValid()) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Senha atual incorreta"));
	die ('1'.\Zage\App\Util::encodeUrl('||'));
}


/** Senha **/
if (($senhaNova) && (strlen($senhaNova) < 5)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Senha inválido"));
	$err		= "1";
}elseif ($senhaNova) {
	$senhaCrypt	= \Zage\App\Crypt::crypt($_user->getUsuario(), $senhaNova,$system->getCodOrganizacao());
}

if ($senhaNova !== $confSenha) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Confirmação de senha inválida"));
	$err		= "1";
}

if ($senhaNova == $senhaAtual) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A nova senha deve ser diferente da atual !!!"));
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
	
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	$oUsuario->setSenha($senhaCrypt);
	$oUsuario->setIndTrocarSenha(0);
	$em->persist($oUsuario);
	$em->flush();
	$em->detach($oUsuario);

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Senha alterada com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

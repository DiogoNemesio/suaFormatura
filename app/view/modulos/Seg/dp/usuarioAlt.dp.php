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
if (isset($_POST['codUsuario'])) 		$codUsuario		= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['usuario'])) 			$usuario		= \Zage\App\Util::antiInjection($_POST['usuario']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['senhaCad'])) 			$senha			= \Zage\App\Util::antiInjection($_POST['senhaCad']);
if (isset($_POST['confSenhaCad'])) 		$confSenha		= \Zage\App\Util::antiInjection($_POST['confSenhaCad']);
if (isset($_POST['codStatus'])) 		$codStatus		= \Zage\App\Util::antiInjection($_POST['codStatus']);
if (isset($_POST['email'])) 			$email			= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['telefone'])) 			$telefone		= \Zage\App\Util::antiInjection($_POST['telefone']);
if (isset($_POST['celular'])) 			$celular		= \Zage\App\Util::antiInjection($_POST['celular']);
if (isset($_POST['sexo'])) 				$sexo			= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['avatar'])) 			$avatar			= \Zage\App\Util::antiInjection($_POST['avatar']);
if (isset($_POST['indTrocarSenha']))	$indTrocarSenha	= \Zage\App\Util::antiInjection($_POST['indTrocarSenha']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

/** Senha **/
if (!empty($senha) && strlen($senha) < 4) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Insira uma senha com mais caracteres !!"));
	$err	= 1;
}

if (!empty($senha) && ($senha !== $confSenha)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A senha está diferente da confirmação !!"));
	$err	= 1;
}

/** Nome **/
if (isset($nome) || !empty($nome)) {
	if (strlen($nome) < 4){
		if(strlen($nome) == 0){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome deve ser preenchido !!"));
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


/** Usuário **/
if (isset($usuario) || !empty($usuario)) {
	if (strlen($usuario) < 2){
		if(strlen($usuario) == 0){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Usuário deve ser preenchido !!"));
			$err	= 1;
		}else{
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Usuário muito pequeno, informe o usuário com mais caracteres !!"));
			$err	= 1;
		}
	}elseif (strlen($usuario) > 25){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Usuário tem limite de 25 caracteres !!"));
		$err	= 1;
	}
}

/** Telefone **/
if (isset($telefone) && (!empty($telefone)) && (!is_numeric($telefone))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Telefone inválido"));
	$err	= 1;
}

/** Celular **/
if (isset($celular) && (!empty($celular)) && (!is_numeric($celular))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Celular inválido"));
	$err	= 1;
}

/** Troca de senha **/
if (isset($indTrocarSenha) && (!empty($indTrocarSenha))) {
	$indTrocarSenha	= 1;
}else{
	$indTrocarSenha	= 0;
}

if (!isset($avatar)) {
	$avatar	= null;
}




if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	if (isset($codUsuario) && (!empty($codUsuario))) {
		$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $codUsuario));
		if (!$oUsuario) $oUsuario	= new \Entidades\ZgsegUsuario();
	}else{
		$oUsuario	= new \Entidades\ZgsegUsuario();
	}
	
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => $codStatus));
	$oSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	
	$oUsuario->setCodOrganizacao($oOrg);
	$oUsuario->setUsuario($usuario);
	$oUsuario->setNome($nome);
	$oUsuario->setEmail($email);
	$oUsuario->setTelefone($telefone);
	$oUsuario->setCelular($celular);
	$oUsuario->setCodStatus($oStatus);
	$oUsuario->setSexo($oSexo);
	$oUsuario->setIndTrocarSenha($indTrocarSenha);
	
	if (isset($avatar) && (!empty($avatar))) {
		$oAvatar	= $em->getRepository('Entidades\ZgsegAvatar')->findOneBy(array('codigo' => $avatar));
		$oUsuario->setAvatar($oAvatar);
	}

	if (!empty($senha)) {
		$senhaCrip	= \Zage\App\Crypt::crypt($usuario, $senha, $system->getCodOrganizacao());
		$oUsuario->setSenha($senhaCrip);
	}
	
	$em->persist($oUsuario);
	$em->flush();
	
	$em->detach($oUsuario);

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

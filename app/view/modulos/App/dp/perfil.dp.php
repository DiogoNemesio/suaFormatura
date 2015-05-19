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
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
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
/** Nome **/
if (!isset($nome) || (empty($nome)) || (strlen($nome) < 4)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Nome inválido"));
	$err	= 1;
}

/** Telefone **/
if (isset($telefone) && (!empty($telefone)) && (!is_int($telefone))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Telefone inválido"));
	$err	= 1;
}

/** Celular **/
if (isset($celular) && (!empty($celular)) && (!is_numeric($celular))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Celular inválido"));
	$err	= 1;
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
	
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));

	$oSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	
	$oUsuario->setNome($nome);
	$oUsuario->setEmail($email);
	$oUsuario->setTelefone($telefone);
	$oUsuario->setCelular($celular);
	$oUsuario->setSexo($oSexo);
	
	if (isset($avatar) && (!empty($avatar))) {
		$oAvatar	= $em->getRepository('Entidades\ZgsegAvatar')->findOneBy(array('codigo' => $avatar));
		$oUsuario->setAvatar($oAvatar);
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

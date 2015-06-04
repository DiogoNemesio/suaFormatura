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
	}elseif(!ereg('^([a-zA-Z0-9.-])*([@])([a-z0-9]).([a-z]{2,3})',$usuario)){
		//verifica se e-mail esta no formato correto de escrita
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email inválido"));
		$err	= 1;
	}else{
		$dominio=explode('@',$usuario);
		if(!checkdnsrr($dominio[1],'A')){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Domínio do e-mail inválido"));
			$err	= 1;
		}
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {


	$oUsuario	= new \Entidades\ZgsegUsuario();
	
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
	
	$oUsuario->setUsuario($usuario);
	$oUsuario->setNome($nome);
	$oUsuario->setCodStatus($oStatus);
	
	$em->persist($oUsuario);
	$em->flush();
	//$em->detach($oUsuario);
	
	#################################################################################
	## Usuário - Organização
	#################################################################################
	$log->debug($codOrganizacao);
	$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findBy(array('codigo' => $codOrganizacao));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findBy(array('codigo' => $codPerfil));
	$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findBy(array('codigo' => 'P'));
	
	$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
	
	$oUsuarioOrg->setCodUsuario($oUsuario);
	$oUsuarioOrg->setCodOrganizacao($oOrg);
	$oUsuarioOrg->setCodPerfil($oPerfil);
	$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);
	
	$em->persist($oUsuarioOrg);
	$em->flush();
	$em->detach($oUsuarioOrg);
	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

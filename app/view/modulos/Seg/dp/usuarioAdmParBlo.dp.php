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
	if (!isset($codUsuario) || (!$codUsuario)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_USUARIO"))));
		$err = 1;
	}
	
	/*** Verificar se o usuario existe ***/
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrando"))));
		$err = 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuAdm	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuAdm) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuAdm->getCodStatus()->getCodigo() != B && $oUsuAdm->getCodStatus()->getCodigo() != A ){		
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Está operação não pode ser concluída, porque a associação entre a organização e o usuário não está ativa."))));
			$err = 1;				
		}
	}
	
	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Bloquear usuario
	#################################################################################
	if ($oUsuAdm->getCodStatus()->getCodigo() == A){
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'B'));
		
		$oUsuAdm->setCodStatus($oStatus);
		$oUsuAdm->setDataBloqueio(new \DateTime());
		$em->persist($oUsuAdm);
		
		$mensagem = 'Usu&aacute;rio bloqueado com sucesso!';
		
	}elseif ($oUsuAdm->getCodStatus()->getCodigo() == B){
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'A'));
		
		$oUsuAdm->setCodStatus($oStatus);
		$oUsuAdm->setDataBloqueio(null);
		$em->persist($oUsuAdm);
		
		$mensagem = 'Usu&aacute;rio desbloqueado com sucesso!';
	}
	
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'. $mensagem);


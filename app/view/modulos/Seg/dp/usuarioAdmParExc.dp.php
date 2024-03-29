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
	$oUsu	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsu) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrando"))));
		$err = 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuOrg->getCodStatus()->getCodigo() == 'C'){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Este usuário já está cancelado!"))));
			$err = 1;
		}
	}
	
	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Cancelar usuario
	#################################################################################

	$oUsuario		= new \Zage\Seg\Usuario();
	
	$oUsuario->_setCodOrganizacao($codOrganizacao);
	$oUsuario->_setCodUsuario($codUsuario);
	$oUsuario->cancelar();
	
	/***** Flush *****/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}	

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário excluído com sucesso!")));
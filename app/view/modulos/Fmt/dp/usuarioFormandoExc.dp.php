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
$codOrganizacao		= $system->getCodOrganizacao();

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
## Excluir usuario(formando) e cliente
#################################################################################
	
	/***********************
	* Excluir/Cancelar
	**********************/
	$oUsuario		= new \Zage\Seg\Usuario();
	
	/**** Cancelar usuario ****/
	$oUsuario->_setCodOrganizacao($codOrganizacao);
	$oUsuario->_setCodUsuario($codUsuario);
	$oUsuario->excluir();
	
	/**** Cancelar usuario ****/
	$oCli = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsu->getCpf() , 'codOrganizacao' => $codOrganizacao));
	
	if ($oCli){
		\Zage\Fin\Pessoa::inativa($oCli->getCodigo());
	}
	
	/***** Flush *****/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Erro excluir o formando. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Formando excluído com sucesso!")));
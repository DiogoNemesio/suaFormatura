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

#################################################################################
## Excluir usuario(formando) e cliente
#################################################################################
try {
	
	/***********************
	* Excluir usuario
	***********************/
	$oUsuario			= new \Zage\Seg\Usuario();
	$oUsuario->_setCodUsuario($codUsuario);
	$oUsuario->_setCodOrganizacao($codOrganizacao);
	
	$retorno	= $oUsuario->excluir();
	
	if ($retorno && is_string($retorno)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$retorno);
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($retorno));
		exit;
	}
	
	/***********************
	* Excluir cliente
	***********************
	$oCli = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsuario->_getUsuario()->getCpf()));
	
	//Endereço
	$oCliEnd = $em->getRepository('Entidades\ZgfinPessoaEndereco')->findBy(array('codPessoa' => $oCli->getCodigo()));
	
	for ($i = 0; $i < sizeof($oCliEnd); $i++) {
		$em->remove($oCliEnd[$i]);
	}
	***/
	
	
	$em->flush();
	$em->clear();
	/***** Flush 
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Erro excluir o formando. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	
*****/
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário excluído com sucesso!")));
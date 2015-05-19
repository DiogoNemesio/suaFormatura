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
if (isset($_POST['codPerfil']))			$codPerfil			= \Zage\App\Util::antiInjection($_POST['codPerfil']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'nome' => $nome));

if (($oNome != null) && ($oNome->getCodigo() != $codPerfil)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome já utilizado"));
	$err 	= 1;
}

 
/** Ativo **/
if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

if (!$oOrg) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Organização '%s' não encontrada",array('%s' => $system->getCodOrganizacao())));
	$err 	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codPerfil) && (!empty($codPerfil))) {
 		$oPerfil	= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
 		if (!$oPerfil) $oPerfil	= new \Entidades\ZgsegPerfil();
 	}else{
 		$oPerfil	= new \Entidades\ZgsegPerfil();
 	}
 	
 	$oPerfil->setCodOrganizacao($oOrg);
 	$oPerfil->setNome($nome);
 	$oPerfil->setIndAtivo($ativo);
 	
 	$em->persist($oPerfil);
 	$em->flush();
 	$em->detach($oPerfil);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oPerfil->getCodigo());
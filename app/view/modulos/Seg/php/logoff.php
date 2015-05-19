<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../../../includeNoAuth.php');
}

#################################################################################
## Define a url para onde a tela de login vai 
#################################################################################
$_org 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array ('codigo' => $system->getCodOrganizacao()));
if (isset($_org) && ($_org instanceof \Entidades\ZgadmOrganizacao) ) {
	$url = ROOT_URL . "/" . $_org->getIdentificacao();
}else{
	$url = ROOT_URL ;
}

#################################################################################
## limpar a variável de autenticação
#################################################################################
$system->desautentica();
$system->setCodEmpresa(null);
header("Location: ".$url, TRUE, 303);
exit;
//include(DOC_ROOT . '/view/index.php');
//exit;


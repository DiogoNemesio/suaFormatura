<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Define a url para onde vai ser recarredada a tela
#################################################################################
$_org 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array ('codigo' => $system->getCodOrganizacao()));
if (isset($_org) && ($_org instanceof \Entidades\ZgadmOrganizacao) ) {
	$url = ROOT_URL . "/" . $_org->getIdentificacao();
}else{
	$url = ROOT_URL;
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl    = new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ROOT_URL'				,$url);
$tpl->set('ID'						,$id);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

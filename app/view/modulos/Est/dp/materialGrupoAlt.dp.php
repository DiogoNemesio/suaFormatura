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
if (isset($_POST['codGrupoPai'])) 	$codGrupoPai	= \Zage\App\Util::antiInjection($_POST['codGrupoPai']);
if (isset($_POST['codGrupo'])) 		$codGrupo		= \Zage\App\Util::antiInjection($_POST['codGrupo']);
if (isset($_POST['descricao'])) 	$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 60)) {
 	$err	= $tr->trans("Campo DESCRIÇÃO não deve conter mais de 60 caracteres");
}

if ((empty($descricao))) {
	$err	= $tr->trans("Campo DESCRIÇÃO é obrigatório");
}

if (isset($codGrupoPai) && !empty($codGrupoPai)) {
	$oGrupoPai		= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codGrupoPai));
}else{
	$oGrupoPai		= null;
}

if (\Zage\Est\Grupo::existeDescricao($codGrupo, $codGrupoPai, $descricao) == true) {
	$err	= $tr->trans("Grupo já existente");
}

$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
if (!$oOrg) {
	$err	= $tr->trans("Organização não encontrada");
}



if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codGrupo) && (!empty($codGrupo))) {
 		$oGrupo	= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codGrupo));
 		if (!$oGrupo) $oGrupo	= new \Entidades\ZgestGrupoMaterial();
 	}else{
 		$oGrupo	= new \Entidades\ZgestGrupoMaterial();
 	}
 	
 	$oGrupo->setDescricao($descricao);
 	$oGrupo->setCodGrupoPai($oGrupoPai);
 	$oGrupo->setCodOrganizacao($oOrg);
 	
 	$em->persist($oGrupo);
 	$em->flush();
 	$em->detach($oGrupo);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oGrupo->getCodigo()."|".htmlentities("Informações salvas com sucesso"));
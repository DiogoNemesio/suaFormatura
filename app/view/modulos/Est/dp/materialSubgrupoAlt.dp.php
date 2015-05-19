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
if (isset($_POST['codSubgrupo'])) 	$codSubgrupo	= \Zage\App\Util::antiInjection($_POST['codSubgrupo']);
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

if (isset($codGrupo) && !empty($codGrupo)) {
	$oGrupo			= $em->getRepository('Entidades\ZgestGrupoMaterial')->findOneBy(array('codigo' => $codGrupo));
}else{
	$oGrupo			= null;
}

if (\Zage\Est\Subgrupo::existeDescricao($codSubgrupo, $codGrupo, $descricao) == true) {
	$err	= $tr->trans("SubGrupo já existente");
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codSubgrupo) && (!empty($codSubgrupo))) {
 		$oSubgrupo	= $em->getRepository('Entidades\ZgestSubgrupoMaterial')->findOneBy(array('codigo' => $codSubgrupo));
 		if (!$oSubgrupo) $oSubgrupo	= new \Entidades\ZgestSubgrupoMaterial();
 	}else{
 		$oSubgrupo	= new \Entidades\ZgestSubgrupoMaterial();
 	}
 	
 	$oSubgrupo->setDescricao($descricao);
 	$oSubgrupo->setCodGrupo($oGrupo);
 	
 	$em->persist($oSubgrupo);
 	$em->flush();
 	$em->detach($oSubgrupo);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
//$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSubgrupo->getCodigo()."|".htmlentities("Informações salvas com sucesso"));
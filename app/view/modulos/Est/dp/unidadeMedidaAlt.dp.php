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
if (isset($_POST['codUnidadeMedida']))	$codUnidadeMedida	= \Zage\App\Util::antiInjection($_POST['codUnidadeMedida']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['sigla']))	 			$sigla				= strtoupper (\Zage\App\Util::antiInjection($_POST['sigla']));

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 60)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO não deve conter mais de 60 caracteres"));
	$err	= 1;
}

if ((empty($descricao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO é obrigatório"));
	$err	= 1;
}

/** Sigla **/

if ((!empty($sigla)) && (strlen($sigla) > 6)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SIGLA não deve conter mais de 6 caracteres"));
	$err	= 1;
}

if ((empty($descricao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo SIGLA é obrigatório"));
	$err	= 1;
}

$oUnidade	= $em->getRepository('Entidades\ZgestUnidadeMedida')->findOneBy(array('sigla' => $sigla));

if($oUnidade != null && ($oUnidade->getCodigo() != $codUnidadeMedida)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Sigla já existe"));
	$err	= 1;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codUnidadeMedida) && (!empty($codUnidadeMedida))) {
 		$oUnidadeMedida	= $em->getRepository('Entidades\ZgestUnidadeMedida')->findOneBy(array('codigo' => $codUnidadeMedida));
 		if (!$oUnidadeMedida) $oUnidadeMedida	= new \Entidades\ZgestUnidadeMedida();
 	}else{
 		$oUnidadeMedida	= new \Entidades\ZgestUnidadeMedida();
 	}
 	
 	$oOrganização		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	
 	$oUnidadeMedida->setCodOrganizacao($oOrganização);
 	$oUnidadeMedida->setDescricao($descricao);
 	$oUnidadeMedida->setSigla($sigla);
 	
 	$em->persist($oUnidadeMedida);
 	$em->flush();
 	$em->detach($oUnidadeMedida);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUnidadeMedida->getCodigo());
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
if (isset($_POST['codMarca']))		$codMarca			= \Zage\App\Util::antiInjection($_POST['codMarca']);
if (isset($_POST['descricao']))		$descricao			= strtoupper(\Zage\App\Util::antiInjection($_POST['descricao']));

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

$oMarca	= $em->getRepository('Entidades\ZgestMarca')->findOneBy(array('descricao' => $descricao, 'codOrganizacao' => $system->getCodOrganizacao()));

if($oMarca != null && ($oMarca->getCodigo() != $codMarca)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Esta MARCA já existente"));
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
	
	if (isset($codMarca) && (!empty($codMarca))) {
 		$oMarca	= $em->getRepository('Entidades\ZgestTipoMaterial')->findOneBy(array('codigo' => $codMarca));
 		if (!$oMarca) $oMarca	= new \Entidades\ZgestTipoMarca();
 	}else{
 		$oMarca	= new \Entidades\ZgestMarca();
 	}
 	
 	$oOrganização		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	
 	$oMarca->setCodOrganizacao($oOrganização);
 	$oMarca->setDescricao($descricao);
 	
 	$em->persist($oMarca);
 	$em->flush();
 	$em->detach($oMarca);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oMarca->getCodigo());
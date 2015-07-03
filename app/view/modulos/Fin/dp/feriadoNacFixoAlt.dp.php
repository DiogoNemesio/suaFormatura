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
if (isset($_POST['codFeriadoNac']))		$codFeriadoNac		= \Zage\App\Util::antiInjection($_POST['codFeriadoNac']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['dia']))	 			$dia				= \Zage\App\Util::antiInjection($_POST['dia']);
if (isset($_POST['mes']))	 			$mes				= \Zage\App\Util::antiInjection($_POST['mes']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($descricao) || (empty($descricao))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO é obrigatório");
	$err	= 1;
}

if ((!empty($descricao)) && (strlen($descricao) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** Dia **/
if (!isset($dia) || (empty($dia))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DIA é obrigatório");
	$err	= 1;
}

if ((!empty($dia)) && (strlen($dia) > 2)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DIA não deve conter mais de 2 caracteres");
	$err	= 1;
}
/*
if ((!empty($dia)) && $dia > 0 || $dia > 31) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DIA deve ser maior que 0 e menor que 31");
	$err	= 1;
}*/

/** Mes **/
if (!isset($mes) || (empty($mes))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DIA é obrigatório");
	$err	= 1;
}

if ((!empty($mes)) && (strlen($mes) > 2)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo MES não deve conter mais de 2 caracteres");
	$err	= 1;
}
/*
if ((!empty($mes)) && $mes > 0 || $mes > 12) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo MES deve ser maior que 0 e menor que 12");
	$err	= 1;
}*/

if ((!empty($dia)) && (!empty($mes)) && !checkdate($mes,$dia,date('y'))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Data inválida, ao mês definido");
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
	
	if (isset($codFeriadoNac) && (!empty($codFeriadoNac))) {
 		$oFeriadoNac	= $em->getRepository('Entidades\ZgfinFeriadoNacional')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codFeriadoNac));
 		if (!$oFeriadoNac) $oFeriadoNac	= new \Entidades\ZgfinFeriadoNacional();
 	}else{
 		$oFeriadoNac	= new \Entidades\ZgfinFeriadoNacional();
 	}
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	
 	$oFeriadoNac->setCodOrganizacao($oOrganizacao);
 	$oFeriadoNac->setDescricao($descricao);
 	$oFeriadoNac->setDia($dia);
 	$oFeriadoNac->setMes($mes);
 	
 	$em->persist($oFeriadoNac);
 	$em->flush();
 	$em->detach($oFeriadoNac);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoNac->getCodigo());
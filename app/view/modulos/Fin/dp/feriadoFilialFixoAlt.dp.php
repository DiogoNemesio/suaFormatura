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
if (isset($_POST['codFeriadoFilial']))	$codFeriadoFilial	= \Zage\App\Util::antiInjection($_POST['codFeriadoFilial']);
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
	
	if (isset($codFeriadoFilial) && (!empty($codFeriadoFilial))) {
 		$oFeriadoFilial	= $em->getRepository('Entidades\ZgfinFeriadoFilial')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codFeriadoFilial));
 		if (!$oFeriadoFilial) $oFeriadoFilial	= new \Entidades\ZgfinFeriadoFilial();
 	}else{
 		$oFeriadoFilial	= new \Entidades\ZgfinFeriadoFilial();
 	}
 	
 	$oFilial	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
 	
 	$oFeriadoFilial->setCodFilial($oFilial);
 	$oFeriadoFilial->setDescricao($descricao);
 	$oFeriadoFilial->setDia($dia);
 	$oFeriadoFilial->setMes($mes);
 	
 	$em->persist($oFeriadoFilial);
 	$em->flush();
 	$em->detach($oFeriadoFilial);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoFilial->getCodigo());
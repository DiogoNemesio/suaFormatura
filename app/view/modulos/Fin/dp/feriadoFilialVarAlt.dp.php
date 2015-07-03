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
if (isset($_POST['codFeriadoFilialVar']))	$codFeriadoFilialVar	= \Zage\App\Util::antiInjection($_POST['codFeriadoFilialVar']);
if (isset($_POST['descricao'])) 			$descricao				= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['data']))	 				$data					= \Zage\App\Util::antiInjection($_POST['data']);
if (isset($_POST['mes']))	 				$mes					= \Zage\App\Util::antiInjection($_POST['mes']);

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

/** data **/
if (!isset($data) || (empty($data))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo data é obrigatório");
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
	
	if (isset($codFeriadoFilialVar) && (!empty($codFeriadoFilialVar))) {
 		$oFeriadoFilialVar	= $em->getRepository('Entidades\ZgfinFeriadoFilialVariavel')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codFeriadoFilialVar));
 		if (!$oFeriadoFilialVar) $oFeriadoFilialVar	= new \Entidades\ZgfinFeriadoFilialVariavel();
 	}else{
 		$oFeriadoFilialVar	= new \Entidades\ZgfinFeriadoFilialVariavel();
 	}
 	
 	if (!empty($data)) {
 		$data		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
 	}else{
 		$data		= null;
 	}
 	
 	$oFilial	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
 	
 	$oFeriadoFilialVar->setCodFilial($oFilial);
 	$oFeriadoFilialVar->setDescricao($descricao);
 	$oFeriadoFilialVar->setData($data);
 	
 	$em->persist($oFeriadoFilialVar);
 	$em->flush();
 	$em->detach($oFeriadoFilialVar);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoFilialVar->getCodigo());
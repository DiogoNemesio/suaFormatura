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
if (isset($_POST['codFeriado']))		$codFeriado			= \Zage\App\Util::antiInjection($_POST['codFeriado']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['data']))	 			$data				= \Zage\App\Util::antiInjection($_POST['data']);

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

/** Data **/
if (!isset($data) || (empty($data))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA é obrigatório");
	$err	= 1;
}else{
	$valData	= new \Zage\App\Validador\DataBR();
	
	if ($valData->isValid($data) == false) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DATA inválido");
		$err	= 1;
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codFeriado) && (!empty($codFeriado))) {
 		$oFeriado	= $em->getRepository('Entidades\ZgfinFeriadoVariavel')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(), 'codigo' => $codFeriado));
 		if (!$oFeriado) $oFeriado	= new \Entidades\ZgfinFeriadoVariavel();
 	}else{
 		$oFeriado	= new \Entidades\ZgfinFeriadoVariavel();
 	}
 	
 	if (!empty($data)) {
 		$data		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
 	}else{
 		$data		= null;
 	}
 	
 	$oFil		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
 	
 	$oFeriado->setCodEmpresa($oFil);
 	$oFeriado->setDescricao($descricao);
 	$oFeriado->setData($data);
 	
 	$em->persist($oFeriado);
 	$em->flush();
 	$em->detach($oFeriado);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriado->getCodigo());
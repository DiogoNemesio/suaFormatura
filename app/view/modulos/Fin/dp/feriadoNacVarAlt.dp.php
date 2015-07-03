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
if (isset($_POST['codFeriadoNacVar']))		$codFeriadoNacVar		= \Zage\App\Util::antiInjection($_POST['codFeriadoNacVar']);
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
	
	if (isset($codFeriadoNacVar) && (!empty($codFeriadoNacVar))) {
 		$oFeriadoNacVar	= $em->getRepository('Entidades\ZgfinFeriadoNacionalVariavel')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codFeriadoNacVar));
 		if (!$oFeriadoNacVar) $oFeriadoNacVar	= new \Entidades\ZgfinFeriadoNacionalVariavel();
 	}else{
 		$oFeriadoNacVar	= new \Entidades\ZgfinFeriadoNacionalVariavel();
 	}
 	
 	if (!empty($data)) {
 		$data		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
 	}else{
 		$data		= null;
 	}
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	
 	$oFeriadoNacVar->setCodOrganizacao($oOrganizacao);
 	$oFeriadoNacVar->setDescricao($descricao);
 	$oFeriadoNacVar->setData($data);
 	
 	$em->persist($oFeriadoNacVar);
 	$em->flush();
 	$em->detach($oFeriadoNacVar);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oFeriadoNacVar->getCodigo());
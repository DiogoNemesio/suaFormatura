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
if (isset($_POST['codEmpresa']))	 	$codEmpresa			= \Zage\App\Util::antiInjection($_POST['codEmpresa']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['fantasia']))			$fantasia			= \Zage\App\Util::antiInjection($_POST['fantasia']);
if (isset($_POST['matriz']))			$matriz				= \Zage\App\Util::antiInjection($_POST['matriz']);
if (isset($_POST['cnpj']))				$cnpj				= \Zage\App\Util::antiInjection($_POST['cnpj']);
if (isset($_POST['inscrEst']))	 		$inscrEst			= \Zage\App\Util::antiInjection($_POST['inscrEst']);
if (isset($_POST['inscrMun']))	 		$inscrMun			= \Zage\App\Util::antiInjection($_POST['inscrMun']);

if (isset($_POST['codLogradouro']))	 	$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['descLogradouro']))	$endereco			= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['cep']))				$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['complemento']))		$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['bairro']))			$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);

if (isset($_POST['dataAbertura']))		$dataAbertura		= \Zage\App\Util::antiInjection($_POST['dataAbertura']);
if (isset($_POST['codStatus']))			$codStatus			= \Zage\App\Util::antiInjection($_POST['codStatus']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo RAZÃO é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 100 caracteres");
	$err	= 1;
}

/** Fantasia **/
if (!isset($fantasia) || (empty($fantasia))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA é obrigatório");
	$err	= 1;
}

if ((!empty($fantasia)) && (strlen($fantasia) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo FANTASIA não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** CNPJ **/
$valCnpj	= new \Zage\App\Validador\Cnpj();

if (!isset($cnpj) || (empty($cnpj))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo CNPJ é obrigatório");
	$err	= 1;
}else{
	if ($valCnpj->isValid($cnpj) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CNPJ inválido !! Verifique se a informação está correta"));
		$err	= 1;
	}else{
		$oEmpresaInfo	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('cnpj' => $cnpj));
		
		if($oEmpresaInfo != null && $oEmpresaInfo->getCodigo() != $codEmpresa){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Já existe uma empresa com este CNPJ !!");
			$err	= 1;
		}
	}
}

/** DATA **/
if (!empty($dataAbertura)) {
	$dataAbertura		= DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataAbertura);
}else{
	$dataAbertura		= null;
}


/** STATUS **/
if (!isset($codStatus) || (empty($codStatus))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo STATUS é obrigatório");
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
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oMatriz		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $matriz));
	$oStatus		= $em->getRepository('Entidades\ZgadmEmpresaStatusTipo')->findOneBy(array('codigo' => $codStatus));
	
	if (isset($codEmpresa) && (!empty($codEmpresa))) {
 		$oEmpresa	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $codEmpresa));
 		if (!$oEmpresa) $oEmpresa	= new \Entidades\ZgadmEmpresa();
 	}else{
 		$oEmpresa	= new \Entidades\ZgadmEmpresa();
 	}
 	
 	$oEmpresa->setCodOrganizacao($oOrganizacao);
 	$oEmpresa->setNome($nome);
 	$oEmpresa->setFantasia($fantasia);
 	$oEmpresa->setCnpj($cnpj);
 	$oEmpresa->setCodMatriz($oMatriz);
 	$oEmpresa->setInscEstadual($inscrEst);
 	$oEmpresa->setInscMunicipal($inscrMun);
 	
 	$oEmpresa->setCep($cep);
 	$oEmpresa->setCodLogradouro($oLogradouro);
 	$oEmpresa->setBairro($bairro);
 	$oEmpresa->setEndereco($endereco);
 	$oEmpresa->setNumero($numero);
 	$oEmpresa->setComplemento($complemento);
 	
 	$oEmpresa->setCodStatus($oStatus);
 	$oEmpresa->setDataAbertura($dataAbertura);
 	
 	$em->persist($oEmpresa);
 	$em->flush();
 	$em->detach($oEmpresa);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEmpresa->getCodigo());
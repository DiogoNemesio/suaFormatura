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
if (isset($_POST['codStatus']))			$codStatus			= \Zage\App\Util::antiInjection($_POST['codStatus']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################

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
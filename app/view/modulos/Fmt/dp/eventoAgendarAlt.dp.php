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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codEvento']))				$codEvento			= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['codTipo']))				$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codLocal']))				$codLocal			= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['dataEvento']))			$dataEvento			= \Zage\App\Util::antiInjection($_POST['dataEvento']);
if (isset($_POST['nome']))					$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['codLogradouro']))			$codLogradouro		= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['cep']))					$cep				= \Zage\App\Util::antiInjection($_POST['cep']);
if (isset($_POST['descLogradouro']))		$descLogradouro		= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))				$bairro				= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))			$complemento		= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))				$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['latitude']))				$latitude			= \Zage\App\Util::antiInjection($_POST['latitude']);
if (isset($_POST['longitude']))				$longitude			= \Zage\App\Util::antiInjection($_POST['longitude']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** Tipo **/
if (!isset($codTipo) || empty($codTipo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo TIPO é obrigatório !!"));
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
	
	if (isset($codEvento) && (!empty($codEvento))) {
 		$oEvento	= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
 		if (!$oEvento) $oEvento	= new \Entidades\ZgfmtEvento();
 	}else{
 		$oEvento	= new \Entidades\ZgfmtEvento();
 	}
 	
 	#################################################################################
 	## Configurações da data
 	#################################################################################
 	if (!empty($dataEvento)) {
 		$dataEvento		= DateTime::createFromFormat($system->config["data"]["datetimeSimplesFormat"], $dataEvento);
 	}else{
 		$dataEvento		= null;
 	}
 	
 	$oOrganizacao	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oTipo			= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipo));
 	$oLocal			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codLocal));
 	$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
 	
 	$oEvento->setCodFormatura($oOrganizacao); 
 	$oEvento->setCodTipoEvento($oTipo);
 	$oEvento->setCodLocal($oLocal);
 	$oEvento->setData($dataEvento);
 	$oEvento->setNome($nome);
 	$oEvento->setCodLogradouro($oLogradouro);
 	$oEvento->setCep($cep);
 	$oEvento->setEndereco($descLogradouro);
 	$oEvento->setBairro($bairro);
 	$oEvento->setComplemento($complemento);
 	$oEvento->setNumero($numero);
 	$oEvento->setLatitude($latitude);
 	$oEvento->setLongitude($longitude);
 	
 	$em->persist($oEvento);
 	$em->flush();
 	$em->detach($oEvento);
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEvento->getCodigo());

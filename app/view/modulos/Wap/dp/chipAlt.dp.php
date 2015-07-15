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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codChip']))				$codChip		= \Zage\App\Util::antiInjection($_POST['codChip']);
if (isset($_POST['identificacao']))			$identificacao	= \Zage\App\Util::antiInjection($_POST['identificacao']);
if (isset($_POST['numero']))				$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['codPais']))				$codPais		= \Zage\App\Util::antiInjection($_POST['codPais']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codChip)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de parâmetros !!"));
	$err	= 1;
}

/** Identificação **/
if (!isset($identificacao) || empty($identificacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Identificação é obrigatório !!"));
	$err	= 1;
}elseif ((!empty($ident)) && (strlen($ident) > 40)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A identificação não deve conter mais de 40 caracteres!"));
	$err	= 1;
}

/** Número **/
if (!isset($numero) || empty($numero)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Número é obrigatório !!"));
	$err	= 1;
}

/** País **/
if (!isset($codPais) || empty($codPais)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo País é obrigatório !!"));
	$err	= 1;
}else{
	$oPais	= $em->getRepository('\Entidades\ZgadmPais')->findOneBy(array('codigo' => $codPais));
	if (!$oPais) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("País não encontrado !!"));
		$err	= 1;
	}
}


/** Separar o ddd do número **/
$ddd		= substr($numero,0,2);
$celular	= substr($numero,2);


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## Resgatar o status que será salvo
	#################################################################################
	$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "R");
	
	#################################################################################
	## Resgatar a organização
	#################################################################################
	$oOrg	= $em->getReference('\Entidades\ZgadmOrganizacao', $system->getCodOrganizacao());
	
	
	if (isset($codChip) && (!empty($codChip))) {
 		$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
 		if (!$oChip) {
 			$oChip	= new \Entidades\ZgwapChip();
 			$oChip->setDataCadastro(new \DateTime("now"));
 		}else{
 			$oStatus	= $oChip->getCodStatus();
 		}
 	}else{
 		$oChip	= new \Entidades\ZgwapChip();
 		$oChip->setDataCadastro(new \DateTime("now"));
 	}
 	
 	$oChip->setDdd($ddd);
 	$oChip->setIdentificacao($identificacao);
 	$oChip->setNumero($celular);
 	$oChip->setCodStatus($oStatus);
 	$oChip->setCodOrganizacao($oOrg);
 	$oChip->setCodPais($oPais);
 	
 	$em->persist($oChip);
 	$em->flush();
 	$em->detach($oChip);
 	
 	
	#################################################################################
	## Solicitar o registro através de SMS
	#################################################################################
	if ($oChip->getCodStatus()->getCodigo() == "R") {
	 	$debug 		= true;
	 	$waUser 	= $oChip->getCodPais()->getCallingCode() . $ddd . $celular;  	// Telephone number including the country code without '+' or '00'.
	 	$nickname 	= $oChip->getIdentificacao();    								// This is the username displayed by WhatsApp clients.
	 	
	 	$log->info("Solicitando código SMS para o chip: ".$waUser);
	 	// Create an instance of WhatsProt.
	 	$w 			= new WhatsProt($waUser, $nickname, $debug);
	 	$return		= $w->codeRequest('sms');
	 	$log->info("Retorno SMS: ".serialize($return));
	}
 	
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo());

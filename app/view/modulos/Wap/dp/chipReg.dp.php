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
if (isset($_POST['codChip']))			$codChip		= \Zage\App\Util::antiInjection($_POST['codChip']);
if (isset($_POST['code']))				$code			= \Zage\App\Util::antiInjection($_POST['code']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codChip)) {
	$err	= $tr->trans("Falta de parâmetros !!");
}else{
	#################################################################################
	## Resgatar as informações do Chip
	#################################################################################
	$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
	if (!$oChip) {
		$err	= $tr->trans("Chip não encontrado !!");
	}
	
}

/** SMS Code **/
if (!isset($code) || empty($code)) {
	$err	= $tr->trans("Campo Código SMS é obrigatório !!");
}elseif ((!empty($code)) && (strlen($code) < 3)) {
	$err	= $tr->trans("Código SMS deve conter mais de 3 caracteres!");
}else{
	$code 	= str_replace("-", "", $code);
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Formatar os campos para efetuar o registro
#################################################################################
$debug 		= true;
$waUser 	= $oChip->getCodPais()->getCallingCode() . $oChip->getDdd(). $oChip->getNumero();  	// Telephone number including the country code without '+' or '00'.
$nickname 	= $oChip->getIdentificacao();    													// This is the username displayed by WhatsApp clients.

#################################################################################
## Fazer o registro
#################################################################################
try {

	$w 			= new WhatsProt($waUser, $nickname, $debug);
	$log->info("Vou registrar o número: ".$waUser, "Nickname: ".$nickname);
	$return		= $w->codeRegister($code);
	$log->info(serialize($return));
	$status		= $return->status;
	$senha		= $return->pw;
	
	if ($status != "ok") {
		$log->err("Falha no registro do chip: $waUser -> ".$e->getMessage());
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Falha ao registrar o chip: $waUser, retorno dos servidores whatsapp: ".$status));
		exit;
	}

} catch (\Exception $e) {
	$log->err("Falha no registro do chip: $waUser -> ".$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Falha ao registrar o chip, entre em contato com os administradores do sistema através do email: contato@suaformatura.com"));
	exit;
}

#################################################################################
## Resgatar o status que será salvo
#################################################################################
$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "A");


#################################################################################
## Salvar os dados de registro
#################################################################################
try {
	$oChip->setCodStatus($oStatus);
	$oChip->setSenha($senha);
	$oChip->setCode($code);
	$oChip->setDataRegistro(new \DateTime("now"));
	
	$em->persist($oChip);
	$em->flush();
	$em->detach($oChip);
 	
} catch (\Exception $e) {
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo().'|'.$tr->trans("Registro efetuado com sucesso"));

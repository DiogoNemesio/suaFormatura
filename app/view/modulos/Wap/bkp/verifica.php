<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}
require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");

#################################################################################
## Parâmetros para resgate da fila de números
#################################################################################
if (!isset($argv[1])) {
	die ('Falta de argumentos (SERVIDOR) ');
}else{
	$servidor	= $argv[1];
	$oServ		= $em->getRepository('Entidades\ZgwapServidor')->findOneBy(array( 'codigo' => $servidor));
	
	if (!$oServ) {
		die ('Servidor não encontrado !!!');
	}
}


if (!isset($chip) && !isset($argv[2])) {
	die ('Falta de argumentos (CHIP) ');
}else{
	if (!isset($chip)) $chip = $argv[2];
	
	$oChip		= $em->getRepository('Entidades\ZgwapChip')->findOneBy(array( 'codigo' => $chip));
	
	if (!$oChip) {
		die ("Chip não cadastrado !!!");
	}
	
}

if (!isset($argv[3])) {
	$numRegistros		= 500;
}else{
	$numRegistros		= (int) $argv[3];
}


#################################################################################
## Carregar as configurações do Chip
#################################################################################
$username 	= "55".$oChip->getDdd().$oChip->getNumero();
$password 	= $oChip->getSenha();
$identity 	= $oChip->getIdentificacao();
$nickname 	= $oChip->getApelido();
$debug 		= true;

#################################################################################
## Configurações de aviso
#################################################################################
$admins		= $em->getRepository('Entidades\ZgwapUser')->findAll();
$wn			= new \Zage\Wap\Numero();

//event handler
/**
 * @param $result SyncResult
 */
function onSyncResult($result) {
	global $wn;
	
	foreach($result->existing as $number) {
		$pos 	= strpos($number, "@");
		if ($pos !== false) {
			$pos	= $pos - 2;
		}else{
			$pos	= strlen($number);
		}
		
		$numero	= substr($number,2,$pos);
		$wn->setTemWa($numero);
		//echo "$number exists\n";
	}

	foreach($result->nonExisting as $number) {
		$pos 	= strpos($number, "@");
		if ($pos !== false) {
			$pos	= $pos - 2;
		}else{
			$pos	= strlen($number);
		}
		$numero	= substr($number,2,$pos);
		$wn->setNaoTemWa($numero);
	}
	return 1;
	//exit;
}

$numeros	= $wn->listaNaoConsultados($servidor,$chip,$numRegistros);
$total		= sizeof($numeros);
$atual		= 0;

$w = new WhatsProt($username, $identity, $nickname, $debug);
$w->eventManager()->bind('onGetSyncResult', 'onSyncResult');
$w->connect();
$w->loginWithPassword($password);

if (sizeof($numeros) == 0) {
	foreach ($admins as $admin) {
		$w->sendMessage($admin->getNumero(), "Olá ".$admin->getNome()." o servidor: (".$oServ->getCodigo().")".$oServ->getNome()." terminou de processar as requisições do Chip ".$oChip->getCodigo()." (".$oChip->getDdd().") ".$oChip->getNumero(). " , favor desativar o serviço !!!");
	}
	exit;
}

$numbers 	= array();
for ($i = 0; $i < $numRegistros; $i++) {
	$numbers[] = "+55".$numeros[$atual]->getDdd() . $numeros[$atual]->getNumero();
	$atual++;
	
	if ($atual == $total) {
		break;
	}
}

//send dataset to server
$w->sendSync($numbers);

$wait = 1;

//wait for response
while($return = $w->pollMessage()) {
	$w->sendPresence(true);
	sleep($wait);
	$wait++;
	if (!$return) break;
}
$w->disconnect();

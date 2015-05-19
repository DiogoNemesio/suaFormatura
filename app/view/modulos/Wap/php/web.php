<?php

#################################################################################
## Includes
#################################################################################
include_once('../../../includeNoAuth.php');
require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");

#################################################################################
## Parâmetros para resgate da fila de números
#################################################################################
$numRegistros		= 30;
$servidor			= 3;

#################################################################################
## Chips habilitados
#################################################################################
$chips	= array(2);
//$chips	= array(2,3,8,9,10);
//$chips	= array(11,12,13,14,15);

//$chips	= array(10,11,12,13);

#################################################################################
## Configurações de aviso
#################################################################################
$admins		= $em->getRepository('Entidades\ZgwapUser')->findAll();
$wn			= new \Zage\Wap\Numero();
$debug 		= false;

$mensagem	= "RODRIGO CUNHA cresceu vendo o trabalho da mãe, Ceci Cunha. Transformou o PROCON-AL em referência nacional, promovendo justiça e cidadania. Se você quer uma Assembleia diferente, vote 45888 para deputado estadual.";
$arquivo	= "img2.jpg";
$foto		= "RodrigoCunha.jpg";


$numeros	= $wn->listaNaoEnviados($servidor,null,$numRegistros);
$total		= sizeof($numeros);
$atual		= 0;
$ic			= 0;
$numChips	= sizeof($chips);
$w			= array();

connectWA();

while ($atual < $total) {

	$chip		= $chips[$ic];

	$numero		= $numeros[$atual]->getPais().$numeros[$atual]->getDdd().$numeros[$atual]->getNumero();
	$celular	= $numeros[$atual]->getDdd().$numeros[$atual]->getNumero();

	echo "Numero: $celular<BR>";

	try {
		$w[$chip]->sendMessageImage($numero, $arquivo);
		$w[$chip]->sendMessage($numero, $mensagem);
		$wn->setEnviada($celular);
	} catch (\Exception $e) {
		$atual--;
		connectWA();
	}

	$atual++;
	$ic++;

	if ($ic == $numChips) {
		$ic = 0;
	}
}


$w[$chip]->sendMessage("558299999611", "Enviadas ".($atual+1)." mensagens do servidor: $servidor");

function connectWA () {
	global $chips,$w,$em,$debug;
	foreach ($chips as $c) {
		if(isset($w[$c])) $w[$c]->disconnect();
		sleep(1);
			
		try {
			#################################################################################
			## Carregar as configurações do Chip
			#################################################################################
			$oChip		= $em->getRepository('Entidades\ZgwapChip')->findOneBy(array( 'codigo' => $c));
			$username 	= "55".$oChip->getDdd().$oChip->getNumero();
			$password 	= $oChip->getSenha();
			$rand		= rand(100, 999999);
			$identity	= "060044941".str_pad($rand,6, "0",STR_PAD_LEFT);
			$nickname 	= $oChip->getApelido();

			$w[$c] = new WhatsProt($username, $identity, $nickname, $debug);
			$w[$c]->connect();
			$w[$c]->loginWithPassword($password);
		} catch (\Exception $e) {
			die('Chip: '.$c. " com problemas: ".$e->getTraceAsString());
		}
	}
}

///////////////////////////////////////////////////////////

function fgets_u($pStdn)
{
	$pArr = array($pStdn);

	if (false === ($num_changed_streams = stream_select($pArr, $write = NULL, $except = NULL, 0))) {
		print("\$ 001 Socket Error : UNABLE TO WATCH STDIN.\n");

		return FALSE;
	} elseif ($num_changed_streams > 0) {
		return trim(fgets($pStdn, 1024));
	}
	return null;
}

//This function only needed to show how eventmanager works.
function onGetProfilePicture($from, $target, $type, $data)
{
	if ($type == "preview") {
		$filename = "preview_" . $target . ".jpg";
	} else {
		$filename = $target . ".jpg";
	}
	$filename = WhatsProt::PICTURES_FOLDER."/" . $filename;
	$fp = @fopen($filename, "w");
	if ($fp) {
		fwrite($fp, $data);
		fclose($fp);
	}

	echo "- Profile picture saved in /".WhatsProt::PICTURES_FOLDER."\n";
}

function onPresenceReceived($username, $from, $type)
{
	$dFrom = str_replace(array("@s.whatsapp.net","@g.us"), "", $from);
	if($type == "available")
		echo "<$dFrom is online>\n\n";
		else
			echo "<$dFrom is offline>\n\n";
}


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
	$numRegistros		= 20000;
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

$mensagem	= "RODRIGO CUNHA cresceu vendo o trabalho da mãe, Ceci Cunha. Transformou o PROCON-AL em referência nacional, promovendo justiça e cidadania. Se você quer uma Assembleia diferente, vote 45888 para deputado estadual.";
$arquivo	= "img2.jpg";
$foto		= "RodrigoCunha.jpg";


$numeros	= $wn->listaNaoEnviados($servidor,$chip,$numRegistros);
$total		= sizeof($numeros);
$atual		= 0;

$w = new WhatsProt($username, $identity, $nickname, $debug);
$w->connect();
// Now loginWithPassword function sends Nickname and (Available) Presence
$w->loginWithPassword($password);


//Retrieve large profile picture. Output is in /src/php/pictures/ (you need to bind a function
//to the event onProfilePicture so the script knows what to do.
//$w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");

while ($atual < $total) {
	
	try {
		//Create the whatsapp object and setup a connection.
		//echo "[*] Connected to WhatsApp\n\n";
		
		$numero		= $numeros[$atual]->getPais().$numeros[$atual]->getDdd().$numeros[$atual]->getNumero();
		$celular	= $numeros[$atual]->getDdd().$numeros[$atual]->getNumero();
		
		echo "Numero: $numero -> ";
		
		#$w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
		#$w->sendGetProfilePicture($fone, true);
		//Print when the user goes online/offline (you need to bind a function to the event onPressence
		//so the script knows what to do)
		#$w->eventManager()->bind("onPresence", "onPresenceReceived");
		//update your profile picture
		#$w->sendSetProfilePicture($foto);
		
		#$w->sendStatusUpdate("Rodrigo Cunha (45888)");
		
		//send picture
		$w->sendMessageImage($numero, $arquivo);
		
		//send video
		//$w->sendMessageVideo($target, 'http://techslides.com/demos/sample-videos/small.mp4');
		
		//send Audio
		//$w->sendMessageAudio($target, 'http://www.kozco.com/tech/piano2.wav');
		
		//send Location
		//$w->sendLocation($target, '4.948568', '52.352957');
		
		// Implemented out queue messages and auto msgid
		$w->sendMessage($numero, $mensagem);
		
		
		$wn->setEnviada($celular);
		
		echo "OK\n";
		
		$atual++;
		
		
		if ($atual%100 == 0) {
			sleep(1);
			$w->sendPresence(true);
			$w->sendMessage("558299999611", "Enviadas $atual mensagens do chip: ".$chip." do servidor: ".$servidor);
		}
		
	} catch (\Exception $e) {
		sleep(1);
		$w = new WhatsProt($username, $identity, $nickname, $debug);
		$w->connect();
		// Now loginWithPassword function sends Nickname and (Available) Presence
		$w->loginWithPassword($password);
		echo "[*] Connected to WhatsApp\n\n";
		
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


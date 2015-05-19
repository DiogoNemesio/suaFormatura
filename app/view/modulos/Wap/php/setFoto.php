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
if (!isset($chip) && !isset($argv[1])) {
	die ('Falta de argumentos (CHIP) ');
}else{
	if (!isset($chip)) $chip = $argv[1];
	
	$oChip		= $em->getRepository('Entidades\ZgwapChip')->findOneBy(array( 'codigo' => $chip));
	
	if (!$oChip) {
		die ("Chip não cadastrado !!!");
	}
	
}

#################################################################################
## Carregar as configurações do Chip
#################################################################################
$username 	= "55".$oChip->getDdd().$oChip->getNumero();
$password 	= $oChip->getSenha();
$identity 	= $oChip->getIdentificacao();
$nickname 	= $oChip->getApelido();
$debug 		= true;
$fone		= "558296775045";
$foto		= "galeteria_perfil.jpg";

#################################################################################
## Configurações de aviso
#################################################################################
$wn			= new \Zage\Wap\Numero();

$w = new WhatsProt($username, $identity, $nickname, $debug);
#$w->checkCredentials();
#exit;

$w->connect();
$w->loginWithPassword($password);
$w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
$w->sendGetProfilePicture($fone, true);
//$w->eventManager()->bind("onPresence", "onPresenceReceived");
$w->sendSetProfilePicture($foto);
$w->sendStatusUpdate($nickname);
$w->sendMessage($fone, "Foto do perfil alterada !!!");
		
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


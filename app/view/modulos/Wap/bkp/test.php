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


#################################################################################
## Chips habilitados
#################################################################################
//$chips	= array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
$chips		= array(35,36,37,38,39,40,41,42,43,44,45,46,47);

#################################################################################
## Configurações de aviso
#################################################################################
$admins		= $em->getRepository('Entidades\ZgwapUser')->findAll();
$baseIdent	= "741986112";
$wn			= new \Zage\Wap\Numero();
$debug 		= false;


$mensagem	= "Apenas testando !!!";

$total		= sizeof($admins);
$atual		= 0;
$ic			= 0;
$im			= 0;
$if			= 0;
$numChips	= sizeof($chips);
$w			= array();

connectWA();


while ($atual < $numChips) {

	$chip		= $chips[$ic];
	
	
	for ($j = 0; $j < $total; $j++ ) {
		$numero	= $admins[$j]->getNumero();	
		try {
			$w[$chip]->sendMessage($numero, $mensagem);
		} catch (\Exception $e) {
			$atual--;
			connectWA();
		}
		
	}
	

	$atual++;
	$ic++;

}
	

function connectWA () {
	global $chips,$w,$em,$debug,$baseIdent;
	foreach ($chips as $c) {
		
		if(isset($w[$c])) $w[$c]->disconnect();
		//sleep(1);
			
		try {
			#################################################################################
			## Carregar as configurações do Chip
			#################################################################################
			$oChip		= $em->getRepository('Entidades\ZgwapChip')->findOneBy(array( 'codigo' => $c));
			$username 	= "55".$oChip->getDdd().$oChip->getNumero();
			$password 	= $oChip->getSenha();
			$rand		= rand(100, 999999);
			$identity	= $baseIdent.str_pad($rand,6, "0",STR_PAD_LEFT);
			$nickname 	= $oChip->getApelido();
	
			echo "Conectando com o chip: $c -> ";
			$w[$c] = new WhatsProt($username, $identity, $nickname, $debug);
			$w[$c]->connect();
			$w[$c]->loginWithPassword($password);
			echo "OK\n";
		} catch (\Exception $e) {
			echo "com problemas\n";
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


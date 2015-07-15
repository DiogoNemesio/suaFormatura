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
		die ('Servidor: '.$servidor.' não encontrado !!!');
	}
}
if (!isset($argv[2])) {
	$numRegistros		= 20000;
}else{
	$numRegistros		= (int) $argv[3];
}


#################################################################################
## Chips habilitados
#################################################################################
$chips	= array(37,42);

#################################################################################
## Configurações de aviso
#################################################################################
$admins		= $em->getRepository('Entidades\ZgwapUser')->findAll();
$baseIdent	= "012454545";
$wn			= new \Zage\Wap\Numero();
$debug 		= false;


$mensagem	= array();
$arquivo	= array();

$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente ___";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_!_";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_!_!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_!_!_";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !_!_!_!_!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!.";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!.!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!.!.";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!.!.!";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente !.!.!.!.";
$mensagem[]	= "Domingo temos a chance de mudar o Brasil. Não podemos aceitar a corrupção, o aumento da energia, o aumento dos preços no supermercado, a falta de segurança, de educação e de saúde. Se você quer um novo país, vote 45. Aecio Presidente _";

$arquivo[]	= "Aecio_3.jpg";
$arquivo[]	= "Aecio_4.jpg";
$arquivo[]	= "Aecio_5.jpg";
$arquivo[]	= "Aecio_6.jpg";
$arquivo[]	= "Aecio_7.jpg";
$arquivo[]	= "Aecio_8.jpg";
$arquivo[]	= "Aecio_9.jpg";
$arquivo[]	= "Aecio_10.jpg";
$arquivo[]	= "Aecio_11.jpg";
$arquivo[]	= "Aecio_12.jpg";
$arquivo[]	= "Aecio_13.jpg";
$arquivo[]	= "Aecio_14.jpg";
$arquivo[]	= "Aecio_15.jpg";
$arquivo[]	= "Aecio_16.jpg";
$arquivo[]	= "Aecio_17.jpg";
$arquivo[]	= "Aecio_18.jpg";
$arquivo[]	= "Aecio_19.jpg";
$arquivo[]	= "Aecio_20.jpg";
$arquivo[]	= "Aecio_21.jpg";
$arquivo[]	= "Aecio_22.jpg";
$arquivo[]	= "Aecio_23.jpg";
$arquivo[]	= "Aecio_1.jpg";
$arquivo[]	= "Aecio_2.jpg";

$numeros	= $wn->listaNaoEnviados($servidor,$chips,$numRegistros);
$total		= sizeof($numeros);
$atual		= 0;
$ic			= 0;
$im			= 0;
$if			= 0;
$numChips	= sizeof($chips);
$numMen		= sizeof($mensagem);
$numFotos	= sizeof($arquivo);
$w			= array();

connectWA();

while ($atual < $total) {

	$chip		= $chips[$ic];
	
	$numero		= $numeros[$atual]->getPais().$numeros[$atual]->getDdd().$numeros[$atual]->getNumero();
	$celular	= $numeros[$atual]->getDdd().$numeros[$atual]->getNumero();
	
	echo "Numero: $celular\n";
	
	try {
		$w[$chip]->sendMessageImage($numero, $arquivo[$if]);
		$w[$chip]->sendMessage($numero, $mensagem[$im]);
		$wn->setEnviada($celular);
	} catch (\Exception $e) {
		$atual--;
		connectWA();
	}

	$atual++;
	$ic++;
	$im++;
	$if++;
		
	if ($ic == $numChips) 	$ic = 0;
	if ($im == $numMen) 	$im = 0;
	if ($if == $numFotos) 	$if = 0;
	

	/*if ($atual%100 == 0) {
		connectWA();
	}*/

	if ($atual%500 == 0) {
		$w[$chip]->sendMessage("558299999611", "Enviadas ".($atual)." mensagens do servidor: $servidor");
	}
	

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


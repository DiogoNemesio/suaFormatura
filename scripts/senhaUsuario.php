<?php

include_once('../app/includeNoSystem.php');

if ($_SERVER['DOCUMENT_ROOT']) {
	die('Script não pode ser excutado através de um Browser !!!');
}

/** Alterar para receber parâmetros da linha de comando **/
$senha			= $argv[1];
$usuario		= $argv[2];
$crypt			= \Zage\App\Crypt::crypt($usuario, $senha);
echo "Senha: ". $senha. PHP_EOL;
echo "String Criptografada: ".$crypt . PHP_EOL;

?>


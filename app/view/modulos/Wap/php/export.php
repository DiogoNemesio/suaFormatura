<?php
#################################################################################
## Includes
#################################################################################
include_once('../../../includeNoAuth.php');
require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");

$wn			= new \Zage\Wap\Numero();
$numeros	= $wn->listaComWhatsApp();
$total		= sizeof($numeros);

$arquivo	= dirname ( __FILE__ ) . "/numeros.sql";


try {
	/** Abre o arquivo somente para leitura **/
	$handle         = fopen($arquivo, "w");

	foreach ($numeros as $numero) {
		fwrite($handle,"UPDATE ZGWAP_NUMERO N SET IND_TEM_WA = 1 WHERE DDD='".$numero["ddd"]."' AND NUMERO = '".$numero["numero"]."';".PHP_EOL);
	}
	
	/** Fecha o arquivo **/
	fclose($handle);

} catch (\Exception $e) {
	\Zage\App\Erro::halt('Código do Erro: "getConteudoArquivo": '.$e->getMessage());
}

<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}

$wn			= new \Zage\Wap\Numero();
$arquivo	= dirname ( __FILE__ ) . "/prefixos.txt";
try {
	/** Abre o arquivo somente para leitura **/
	$handle         = fopen($arquivo, "r");
		
	/** Lê o conteudo do arquivo **/
	while (($buffer = fgets($handle, 4096)) !== false) {
		$prefixo = str_replace(PHP_EOL, "", $buffer);
		$prefixo	= trim($prefixo);
		$prefixo	= chop($prefixo);
		
		for ($i = 0; $i < 9999; $i++) {
			
			$numero = "82".$prefixo.str_pad($i,4,"0",STR_PAD_LEFT);

			$retorno = $wn->cria($numero);
			
			if ($retorno == 1) {
			echo "Número: ".$numero." Já cadastrado\n";
			} elseif ($retorno == 2) {
			echo "Número: ".$numero." inválido\n";
			} elseif ($retorno == null) {
			echo "Cadastrado número: ".$numero."\n";
			}
					
		}
		
		
	}

	if (!feof($handle)) {
		echo "Error: unexpected fgets() fail\n";
	}
	/** Fecha o arquivo **/
	fclose($handle);

} catch (\Exception $e) {
	\Zage\App\Erro::halt('Código do Erro: "getConteudoArquivo": '.$e->getMessage());
}

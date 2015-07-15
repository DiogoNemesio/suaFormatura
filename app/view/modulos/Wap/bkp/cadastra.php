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

$dir = realpath ( dirname ( __FILE__ ) . '/../' ) . "/txt/";

if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (filetype($dir . $file) == "file") {
				$arquivo	= $dir . $file;
				try {
					/** Abre o arquivo somente para leitura **/
					$handle         = fopen($arquivo, "r");
					
					/** Lê o conteudo do arquivo **/
					while (($buffer = fgets($handle, 4096)) !== false) {
						$numero = str_replace(PHP_EOL, "", $buffer);
						$numero	= trim($numero);
						$numero	= chop($numero);
						$retorno = $wn->cria($numero);
						
						if ($retorno == 1) {
							echo "Número: ".$numero." Já cadastrado\n";
						} elseif ($retorno == 2) {
							echo "Número: ".$numero." inválido\n";
						} elseif ($retorno == null) {
							echo "Cadastrado número: ".$numero."\n";
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
				
				
				
			}
		}
		closedir($dh);
	}
}

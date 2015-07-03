<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	define('DOC_ROOT', realpath(dirname( __FILE__ ) . '/../') . "/" );
	include_once(DOC_ROOT . 'includeNoAuth.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log;

#################################################################################
## Busca os arquivos que ainda não foram importados
#################################################################################
$codTipoArquivo		= "RTB";
$codStatus			= "A";
$atividade			= "IMP_RET_BANCARIO";
$codAtividade		= \Zage\Utl\Atividade::buscaPorIdentificacao($atividade);
if (!$codAtividade)	{
	$log->err("Atividade '".$atividade."' não encontrada !! (".__FILE__.")");
	exit;
}
$fila				= $em->getRepository('\Entidades\ZgappFilaImportacao')->findBy(array('codStatus' => $codStatus,'codTipoArquivo' => $codTipoArquivo ,'codAtividade' => $codAtividade),array('dataImportacao' => "ASC"));

for ($i = 0; $i < sizeof($fila); $i++) {
	
	#################################################################################
	## Alterar o status para Iniciando a importação
	#################################################################################
	\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'IN');
	
	#################################################################################
	## Descobrindo a classe que ira gerenciar o arquivo
	#################################################################################
	$classe	= "\\Zage\\Fin\\Arquivos\\Layout\\".$fila[$i]->getVariavel();
	$file	= CLASS_PATH . "/Zage/Fin/Arquivos/Layout/".$fila[$i]->getVariavel().'.php';
	
	if (!file_exists($file)) {
		#################################################################################
		## Cancelar a importação
		#################################################################################
		\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'C');
		$log->err("Classe não encontrada (".$file."), código da fila: ".$fila[$i]->getCodigo().' a importação será cancelada');
		
	}else{
		#################################################################################
		## Alterar o status para analisando o arquivo
		#################################################################################
		\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'AN');
		try {
			$layout	= new $classe;
			$layout->loadFile($fila[$i]->getArquivo());
			$layout->valida($fila[$i]->getCodigo());
			
			if ($layout->estaValido() == true) {
				#################################################################################
				## Alterar o status para OK
				#################################################################################
				\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'OK');
				print_r($layout->detalhes);
			}else{
				#################################################################################
				## Salvar o PDF de erro
				#################################################################################
				\Zage\App\Fila::salvaResumo($fila[$i]->getCodigo(),$layout->getResumoPDF());
				
				#################################################################################
				## Alterar o status para OK
				#################################################################################
				\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'E');
			}
				
		
		} catch (\Exception $e) {
			
					
			#################################################################################
			## Alterar o status para "Com Erro"
			#################################################################################
			\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'E');
			$log->err("ATIVIDADE: (".$atividade.") -> Código da fila: ".$fila[$i]->getCodigo().' com erro: '.$e->getMessage());
			continue;
		}
	}
	
}
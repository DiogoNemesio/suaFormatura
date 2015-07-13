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
global $em,$system,$tr,$log,$db;


$daniel			= $em->getRepository('\Entidades\ZgsegUsuario')->findOneBy(array('codigo' => 1));
$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
$notificacao->setMensagem("Apenas Testando");
$notificacao->setAssunto("Teste: ".date('d/m/Y h:i:s'));
$notificacao->setCodUsuario($daniel);
$notificacao->associaUsuario(1);
$notificacao->enviaWa();
$notificacao->salva();

/*$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO);
$notificacao->setAssunto("Test");
$notificacao->setMensagem("Teste de notificação de organização");
$notificacao->associaOrganizacao(1);
$notificacao->enviaWa();
$notificacao->salva();
*/

exit;

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
	## Define a variável de banco @ZG_USER para atribuir as transações ao usuário que enviou o arquivo
	#################################################################################
	$db->setLoggedUser($fila[$i]->getCodUsuario()->getCodigo());

	#################################################################################
	## Define a variável de banco @ZG_ORG 
	#################################################################################
	$db->setOrganizacao($fila[$i]->getCodOrganizacao()->getCodigo());
	
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
			

			#################################################################################
			## Busca a conta corrente que está sendo manipulada
			#################################################################################
			if ($layout->estaValido() == true) {
				$contaCorrente			= \Zage\Fin\Conta::busca($fila[$i]->getCodOrganizacao()->getCodigo(), $layout->header["AGENCIA"], $layout->header["CONTA_CORRENTE"]);
				if (!$contaCorrente)		$layout->adicionaErro('Conta "'.$layout->header["CONTA_CORRENTE"].'" da agência "'.$layout->header["AGENCIA"].'" não localizada no sistema !!!', 0, "0", 0);
			}
			
			if ($layout->estaValido() == true) {
				

				#################################################################################
				## Faz o loop nos detalhes para fazer a baixa 
				#################################################################################
				foreach ($layout->detalhes as $det) {

					#################################################################################
					## Busca a conta que será baixada
					#################################################################################
					$oConta		= \Zage\Fin\ContaReceber::buscaPorNossoNumero($contaCorrente->getCodigo(),$det["NOSSO_NUMERO"]);

					if (!$oConta)	{
						$layout->adicionaErro('Nosso Número não localizado "'.$det["NOSSO_NUMERO"].'" !!!', $det["SEQUENCIAL_REGISTRO"], "1", 0);
						continue;
					}elseif (empty($det["CODIGO_LIQUIDACAO"])) {
						$layout->adicionaAviso("",$det["SEQUENCIAL_REGISTRO"],"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') ainda não foi liquidada pelo cliente !!!');
						continue;
					}else{

						#################################################################################
						## Verifica se essa baixa já foi realizada
						#################################################################################
						$histBaixa		= $em->getRepository('\Entidades\ZgfinHistoricoRec')->findOneBy(array('codContaRec' => $oConta->getCodigo(),'documento' => $fila[$i]->getNome() ,'seqRetornoBancario' => $det["SEQUENCIAL_REGISTRO"]));
						if ($histBaixa)	{
							$layout->adicionaAviso("",$det["SEQUENCIAL_REGISTRO"],"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') já foi processada por esse arquivo, registro desprezado !!!');
							continue;
						}
						
						$conta		= new \Zage\Fin\ContaReceber();
						
						#################################################################################
						## Forma de pagamento Original
						#################################################################################
						$codFormaPag	= ($oConta->getCodFormaPagamento()) ? $oConta->getCodFormaPagamento()->getCodigo() : null;
						
						#################################################################################
						## Data de Efetivação
						#################################################################################
						$dataRec		= $det["DATA_OCORRENCIA"];
						
						#################################################################################
						## Valor recebido
						#################################################################################
						$valor			= $det["VALOR"];
						
						#################################################################################
						## Efetiva o recebimento
						#################################################################################
						$em->getConnection()->beginTransaction();
						try {
							$erro		= $conta->recebe($oConta,$contaCorrente->getCodigo(),$codFormaPag,$dataRec->format($system->config["data"]["dateFormat"]),$valor,0,0,0,$fila[$i]->getNome(),"RTB",$det["SEQUENCIAL_REGISTRO"]);
							
							$em->flush();
							$em->clear();
							$em->getConnection()->commit();
						} catch (\Exception $e) {
							$em->getConnection()->rollback();
							$layout->adicionaErro($e->getMessage(), $det["SEQUENCIAL_REGISTRO"], "1", 0);
						}
						
						if ($erro) {
							$layout->adicionaErro($erro, $det["SEQUENCIAL_REGISTRO"], "1", 0);
						}else{
							$layout->adicionaMensagem("",$det["SEQUENCIAL_REGISTRO"],"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') baixa no valor de '.\Zage\App\Util::to_money($valor).' efetuada com sucesso !!!');
						}
					}
					
				}
				//echo "Header: \n";
				//print_r($layout->header);
				
				//echo "Detalhes: \n";
				//print_r($layout->detalhes);
				
				//echo "Trailler: \n";
				//print_r($layout->trailler);

				#################################################################################
				## Alterar o status para OK
				#################################################################################
				\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'OK');
				
				#################################################################################
				## Salvar o PDF de resumo
				#################################################################################
				\Zage\App\Fila::salvaResumo($fila[$i]->getCodigo(),$layout->getResumoPDF());
				
				
			}else{
				#################################################################################
				## Salvar o PDF de resumo
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
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

#################################################################################
## Busca os arquivos que ainda não foram importados
#################################################################################
$codTipoArquivo		= "RTB";
$codStatus			= "A";
$atividade			= "IMP_RET_BANCARIO_BOLETO";
$codTipoBaixa		= "RTB";
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
	## Descobrindo a classe que irá gerenciar o arquivo
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
			$retorno	= new $classe;
			$retorno->loadFile($fila[$i]->getArquivo());
			$retorno->valida($fila[$i]->getCodigo());

			if ($retorno->estaValido() == true) {
				
				#################################################################################
				## Gerar as liquidações
				#################################################################################
				$log->debug("Arquivo validado com sucesso");
				$retorno->geraLiquidacoes();
				
				#################################################################################
				## Verificar qual o banco, para buscar a conta de forma adequada
				#################################################################################
				$log->debug("Banco do arquivo: ".$retorno->getCodBanco());
				$log->debug("Agencia: ".$retorno->getAgencia());
				$log->debug("Conta Corrente: ".$retorno->getContaCorrente());
				$log->debug("Código do Cedente: ".$retorno->getCodCedente());
				
				#################################################################################
				## Busca a conta corrente que está sendo manipulada
				#################################################################################
				if ($retorno->getCodCedente()) {
					$contaCorrente			= \Zage\Fin\Conta::buscaPorCedente($fila[$i]->getCodOrganizacao()->getCodigo(), $retorno->getAgencia(), $retorno->getCodCedente());
				}else{
					$contaCorrente			= \Zage\Fin\Conta::busca($fila[$i]->getCodOrganizacao()->getCodigo(), $retorno->getAgencia(), $retorno->getContaCorrente());
				}
				if (!$contaCorrente)		$retorno->adicionaErro('Conta "'.$retorno->getContaCorrente().'" da agência "'.$retorno->getAgencia().'" não localizada no sistema !!!', 0, "0", 0);
				
				if ($contaCorrente)			$log->info("Conta Corrente encontrada");
				
			}else{
				$log->debug("Arquivo inválido");
			}
			
			
			
			if ($retorno->estaValido() == true) {

				#################################################################################
				## Faz o loop nas liquidacoes para fazer a baixa
				#################################################################################
				for ($l = 0; $l < $retorno->getNumLiquidacoes(); $l++) {
					$log->info("Liquidação [".$l."]: ".serialize($retorno->liquidacoes[$l]));

					#################################################################################
					## Resgatar as variáveis
					#################################################################################
					$nossoNumero			= $retorno->liquidacoes[$l]->getNossoNumero();
					$oDataLiq				= $retorno->liquidacoes[$l]->getDataLiquidacao();
					$sequencial				= $retorno->liquidacoes[$l]->getSequencial();
					$codLiquidacao			= $retorno->liquidacoes[$l]->getCodLiquidacao();
					$valorPago				= $retorno->liquidacoes[$l]->getValorPago();
					$valorDesconto			= $retorno->liquidacoes[$l]->getValorDesconto();
					$valorBoleto			= $retorno->liquidacoes[$l]->getValorBoleto();
					$valorIOF				= $retorno->liquidacoes[$l]->getValorIOF();
					$valorJuros				= $retorno->liquidacoes[$l]->getValorJuros();
					$valorLiquido			= $retorno->liquidacoes[$l]->getValorLiquido();
					$valorOutrosCreditos	= $retorno->liquidacoes[$l]->getValorOutrosCreditos();
					$valorOutrasDespesas	= $retorno->liquidacoes[$l]->getValorOutrasDespesas();
					
					#################################################################################
					## Validações
					#################################################################################
					if (!$nossoNumero) {
						$retorno->adicionaErro('Nosso Número não informado no arquivo !!!', $sequencial, "1", 0);
						continue;
					}
					
					if (!$oDataLiq) {
						$retorno->adicionaErro('Data de liquidação não informada no arquivo !!!', $sequencial, "1", 0);
						continue;
					}

					if (!$valorPago) {
						$retorno->adicionaErro('Valor da liquidação não informado no arquivo !!!', $sequencial, "1", 0);
						continue;
					}
					
					#################################################################################
					## Busca a conta que será baixada
					#################################################################################
					$oConta		= \Zage\Fin\ContaReceber::buscaPorNossoNumero($contaCorrente->getCodigo(),$nossoNumero);
					
					if (!$oConta)	{
						$retorno->adicionaErro('Nosso Número não localizado "'.$nossoNumero.'" !!!', $sequencial, "1", 0);
						continue;
					}elseif (empty($codLiquidacao)) {
						$retorno->adicionaAviso("",$sequencial,"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') ainda não foi liquidada pelo cliente !!!');
						continue;
					}else{
					
						#################################################################################
						## Verifica se essa baixa já foi realizada
						#################################################################################
						$histBaixa		= $em->getRepository('\Entidades\ZgfinHistoricoRec')->findOneBy(array('codContaRec' => $oConta->getCodigo(),'documento' => $fila[$i]->getNome() ,'seqRetornoBancario' => $sequencial));
						if ($histBaixa)	{
							$retorno->adicionaAviso("",$sequencial,"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') já foi processada por esse arquivo, registro desprezado !!!');
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
						$dataRec		= $oDataLiq->format($system->config["data"]["dateFormat"]);
					
						#################################################################################
						## Data de Efetivação
						#################################################################################
						$valorOutros	= 0;
						$valorDescJuros	= \Zage\App\Util::to_float($oConta->getValorDescontoJuros());
						$valorDescMora	= \Zage\App\Util::to_float($oConta->getValorDescontoMora());
						
						#################################################################################
						## Efetiva o recebimento
						#################################################################################
						$em->getConnection()->beginTransaction();
						try {
							$erro		= $conta->recebe($oConta,$contaCorrente->getCodigo(),$codFormaPag,$dataRec,$valorPago,$valorJuros,0,$valorDesconto,$valorOutros,$valorDescJuros,$valorDescMora,$fila[$i]->getNome(),$codTipoBaixa,$sequencial,null,null);
								
							$em->flush();
							$em->clear();
							$em->getConnection()->commit();
						} catch (\Exception $e) {
							$em->getConnection()->rollback();
							$retorno->adicionaErro($e->getMessage(), $sequencial, "1", 0);
						}
					
						if ($erro) {
							$retorno->adicionaErro($erro, $sequencial, "1", 0);
						}else{
							$retorno->adicionaMensagem("",$sequencial,"1",'Conta: "'.$oConta->getDescricao().'" Parcela: ('.$oConta->getParcela()."/".$oConta->getNumParcelas().') baixa no valor de '.\Zage\App\Util::to_money($valorPago).' efetuada com sucesso !!!');
						}
					}
						
				}
				
				#################################################################################
				## Alterar o status para OK
				#################################################################################
				\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'OK');
				
				#################################################################################
				## Salvar o PDF de resumo
				#################################################################################
				\Zage\App\Fila::salvaResumo($fila[$i]->getCodigo(),$retorno->getResumoPDF());
				
			}else{
				#################################################################################
				## Salvar o PDF de resumo
				#################################################################################
				\Zage\App\Fila::salvaResumo($fila[$i]->getCodigo(),$retorno->getResumoPDF());
				
				#################################################################################
				## Alterar o status para Erro
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
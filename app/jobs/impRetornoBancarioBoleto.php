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
	## Resgatar o Tipo de Layout de arquivo e a conta corrente da variável
	#################################################################################
	$aVar						= explode("|",$fila[$i]->getVariavel());
	$codTipoArquivoLayout		= $aVar[0];
	$codConta					= $aVar[1];

	#################################################################################
	## Verificar se o Layout e a conta existem
	#################################################################################
	$oTipoArquivoLayout			= $em->getRepository('\Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $codTipoArquivoLayout)); 
	$oContaFila					= $em->getRepository('\Entidades\ZgfinConta')->findOneBy(array('codigo' => $codConta));
	
	if (!$oTipoArquivoLayout)	{
		$log->err("0xUU8*iasd: Importação cancelada por não encontrar o Layout informado (".$codTipoArquivoLayout."), código da fila: ".$fila[$i]->getCodigo());
		\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'C');
	}
	
	if (!$oContaFila)	{
		$log->err("0xUU8*iase: Importação cancelada por não encontrar a conta corrente informada (".$codConta."), código da fila: ".$fila[$i]->getCodigo());
		\Zage\App\Fila::alteraStatus($fila[$i]->getCodigo(), 'C');
	}
	
	
	#################################################################################
	## Descobrindo a classe que irá gerenciar o arquivo
	#################################################################################
	$classe	= "\\Zage\\Fin\\Arquivos\\Layout\\".$oTipoArquivoLayout->getCodigo();
	$file	= CLASS_PATH . "/Zage/Fin/Arquivos/Layout/".$oTipoArquivoLayout->getCodigo().'.php';
	
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
				if (!$contaCorrente)		$retorno->adicionaErro(0,1,0,'Conta "'.$retorno->getContaCorrente().'" da agência "'.$retorno->getAgencia().'" não localizada no sistema !!!');
				
				if ($contaCorrente)			{
					#################################################################################
					## Verificar se a conta encontrada é a mesma no qual o arquivo foi importado
					#################################################################################
					if ($contaCorrente->getCodigo() != $oContaFila->getCodigo()) {
						$retorno->adicionaErro(0,1,0,'Arquivo de retorno não pertence a conta corrente selecionada na importação');
						$log->err('0xKlll*%2s: Arquivo de retorno não pertence a conta corrente selecionada na importação, código da fila: '.$fila[$i]->getCodigo(). ' Conta da fila: '.$oContaFila->getCodigo().' Conta do arquivo: '.$contaCorrente->getCodigo(). " Usuário que importou o arquivo: ".$fila[$i]->getCodUsuario()->getUsuario());
					}

					
					#################################################################################
					## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
					#################################################################################
					$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($fila[$i]->getCodOrganizacao()->getCodigo());
						
					#################################################################################
					## Verificar se a conta encontrada é de uma organização gerenciada pela organização
					## que importou o arquivo
					#################################################################################
					$aOrg			= array($fila[$i]->getCodOrganizacao()->getCodigo());
					if ($oFmtAdm)	$aOrg[]		= $oFmtAdm->getCodigo();
					$oContas		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $aOrg));
					$aContas			= array();
					for ($j = 0; $j < sizeof($oContas); $j++) {
						$aContas[$oContas[$j]->getCodigo()]		= $oContas[$j]->getCodigo();
					}
					if (array_key_exists($contaCorrente->getCodigo(), $aContas) == false) {
						$retorno->adicionaErro(0,1,0,'Conta corrente do arquivo não pertence a organização');
						$log->err('0x09Kilasd: Conta corrente do arquivo não pertence a organização, código da fila: '.$fila[$i]->getCodigo(). ' Conta do arquivo: '.$contaCorrente->getCodigo(). " Usuário que importou o arquivo: ".$fila[$i]->getCodUsuario()->getUsuario());
					}
				}
				
			}else{
				$log->debug("Arquivo inválido");
			}
			
			
			
			if ($retorno->estaValido() == true) {

				#################################################################################
				## Faz o loop nas liquidacoes para fazer a baixa
				#################################################################################
				for ($l = 0; $l < $retorno->getNumLiquidacoes(); $l++) {
					//$log->info("Liquidação [".$l."]: ".serialize($retorno->liquidacoes[$l]));
					

					#################################################################################
					## Resgatar as variáveis
					#################################################################################
					$nossoNumero			= $retorno->liquidacoes[$l]->getNossoNumero();
					$oDataLiq				= $retorno->liquidacoes[$l]->getDataLiquidacao();
					$sequencial				= $retorno->liquidacoes[$l]->getSequencial();
					$codLiquidacao			= $retorno->liquidacoes[$l]->getCodLiquidacao();
					$valorPago				= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorPago());
					$valorDesconto			= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorDesconto());
					$valorBoleto			= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorBoleto());
					$valorIOF				= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorIOF());
					$valorJuros				= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorJuros());
					$valorLiquido			= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorLiquido());
					$valorOutrosCreditos	= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorOutrosCreditos());
					$valorOutrasDespesas	= \Zage\App\Util::to_float($retorno->liquidacoes[$l]->getValorOutrasDespesas());
					
					$log->debug("Liquidação [".$l."]: NossoNumero: ".$nossoNumero." ValorPago: ".$valorPago." ValorJuros: ".$valorJuros." Desconto: ".$valorDesconto." ValorLíquido: ".$valorLiquido." ValorBoleto: ".$valorBoleto);
					
					$valorBaixa				= ($valorPago - $valorJuros);
					
					#################################################################################
					## Validações
					#################################################################################
					if (!$nossoNumero) {
						$retorno->adicionaErro(0,$sequencial,0,'Nosso Número não informado no arquivo !!!');
						continue;
					}
					
					if (!$oDataLiq) {
						$retorno->adicionaErro(0,$sequencial,0,'Data de liquidação não informada no arquivo !!!');
						continue;
					}

					if (!$valorPago) {
						$retorno->adicionaErro(0,$sequencial,0,'Valor da liquidação não informado no arquivo !!!');
						continue;
					}
					
					#################################################################################
					## Busca a conta que será baixada
					#################################################################################
					$oConta		= \Zage\Fin\ContaReceber::buscaPorNossoNumero($contaCorrente->getCodigo(),$nossoNumero);
					
					if (!$oConta)	{
						$retorno->adicionaErro(0,$sequencial,0,'Nosso Número não localizado "'.$nossoNumero.'" !!!');
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
							$erro		= $conta->recebe($oConta,$contaCorrente->getCodigo(),$codFormaPag,$dataRec,$valorBaixa,$valorJuros,0,$valorDesconto,$valorOutros,$valorDescJuros,$valorDescMora,$fila[$i]->getNome(),$codTipoBaixa,$sequencial,null,null);
								
							$em->flush();
							$em->clear();
							$em->getConnection()->commit();
						} catch (\Exception $e) {
							$em->getConnection()->rollback();
							$retorno->adicionaErro(0,$sequencial,0,$e->getMessage());
						}
					
						if ($erro) {
							$retorno->adicionaErro(0,$sequencial,0,$erro);
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
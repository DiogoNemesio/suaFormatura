<?php
use Entidades\ZgfinSeqCodTransacao;
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}
 
#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);

if (isset($_POST['codConvExtra']))		$codConvExtra		= \Zage\App\Util::antiInjection($_POST['codConvExtra']);
if (isset($_POST['codEvento']))			$codEvento			= \Zage\App\Util::antiInjection($_POST['codEvento']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['quantDisp']))			$quantDisp			= \Zage\App\Util::antiInjection($_POST['quantDisp']);
if (isset($_POST['quantConv']))			$quantConv			= \Zage\App\Util::antiInjection($_POST['quantConv']);

if (!isset($codConvExtra))				$codConvExtra		= array();
if (!isset($codEvento))					$codEvento			= array();
if (!isset($valor))						$valor				= array();
if (!isset($quantDisp))					$quantDisp			= array();
if (!isset($quantConv))					$quantConv			= array();

$valorTotal = 0;

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** FORMA PAGAMENTO **/
if (!isset($codFormaPag) || empty($codFormaPag)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione a forma de pagamento.");
	$err	= 1;
}

/** VALIDAR SE AS QUANTIDADES ESTÃO DE ACORDO COM O LIMITE **/
$indTemQtde = 0;
for ($i = 0; $i < sizeof($codEvento); $i++) {
	if (isset($quantConv[$i]) && !empty($quantConv[$i])) {
		//Verificar se a quantidade não está nula
		if ($quantConv[$i] != ''){
			$indTemQtde = 1;
		}
		
		//Resgatar as configurações do tipo de evento
		$oEventoConf = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codEvento' => $codEvento[$i] ));
		
		if (!$oEventoConf){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Ops!! Não encontramos as configurações do convite extra. Caso o problema persista entre em contato com o nosso suporte.");
			$err	= 1;
		}else{
			$quantConv[$i]	= (int) $quantConv[$i];
			//Resgatar a quantidade de convites disponíveis para esse evento
			$qtdeConvDis	= 10;//\Zage\Fmt\Convite::qtdeConviteDispFormando($codFormando, $oEventoConf->getCodEvento());
			if ($qtdeConvDis < $quantConv[$i]){
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"A quantidade para evento ".$oEventoConf->getcodEvento()->getCodTipoEvento()->getDescricao()." está maior que o disponível.");
				$err	= 1;
			}else{
				$valorTotalConv = ($valorTotalConv) + ($quantConv[$i] * $oEventoConf->getValor());
			}
		}
	}
}


/** QUANTIDADE CONVITES **/
if ($indTemQtde == 0) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Nenhum evento foi selecionado!");
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	$em->getConnection()->beginTransaction();
	
	#################################################################################
	## CODIGO DE TRANSAÇÃO
	#################################################################################
	$codTransacaoVenda = \Zage\Adm\Sequencial::proximoValor(ZgfinSeqCodTransacao);
	
	#################################################################################
	## SALVAR CABEÇALHO DA VENDA
	#################################################################################
	$oConviteVenda	= new \Entidades\ZgfmtConviteExtraVenda();

	#################################################################################
	## RESGATAR CONTA RECEBIMENTO
	#################################################################################
	$oConf			= $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codVendaTipo' => 'I'));
	$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $oConf->getCodContaBoleto()->getCodigo()));
	
	/** RESGATAR O VALOR DA TAXA DE CONVENIENCIA PARA O TIPO PRESENCIAL **/
	$taxas 		= \Zage\Fmt\Convite::calcTaxaConveniencia('I', $oConta->getCodigo(), $codFormaPag);
	$taxaConv	= $taxas['COVENIENCIA'];
	$taxaBol 	= $taxas['BOLETO'];
	
	//Adicionar ao valor total a taxa de conveniencia
	$valorTotal = $valorTotalConv + $taxaConv + $taxaBol;
	
	#################################################################################
	## RESGATAR OBJETOS
	#################################################################################
	$oFormando		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa()));
	$oFormaPag		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
	$oTipoVenda		= $em->getRepository('Entidades\ZgfmtConviteExtraVendaTipo')->findOneBy(array('codigo' => I));
	
	#################################################################################
	## SETAR VALORES
	#################################################################################
	$oConviteVenda->setCodOrganizacao($oOrg);
	$oConviteVenda->setCodFormando($oFormando);
	$oConviteVenda->setCodVendaTipo($oTipoVenda);
	$oConviteVenda->setCodFormaPagamento($oFormaPag);
	$oConviteVenda->setCodTransacao($codTransacaoVenda);
	$oConviteVenda->setCodContaRecebimento($oConta);
	$oConviteVenda->setValorTotal($valorTotal);
	$oConviteVenda->setTaxaConveniencia(null);
	$oConviteVenda->setDataCadastro(new DateTime(now));
	
	$em->persist($oConviteVenda);
	
	#################################################################################
	## SALVAR OS ITENS DA VENDA
	#################################################################################
	
	$linha = 0;
	$html  = null;
	for ($i = 0; $i < sizeof($codEvento); $i++) {
	 	//Resgatar as configurações do tipo de evento
		$oEventoConf = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codEvento' => $codEvento[$i]));
	 	 
		#################################################################################
		## CASO A QUANTIDADE ESTEJA NULA PARA O EVENTO CONTINUAR
		#################################################################################
		if ($quantConv[$i] == ''){
			continue;
		}
		
		#################################################################################
	 	## SETAR VALORES
	 	#################################################################################
		$oConviteItem	= new \Entidades\ZgfmtConviteExtraVendaItem();
		
	 	$oConviteItem->setCodVenda($oConviteVenda);
	 	$oConviteItem->setCodEvento($oEventoConf->getCodEvento());
	 	$oConviteItem->setQuantidade($quantConv[$i]);
	 	$oConviteItem->setValorUnitario($oEventoConf->getValor());
	 	
	 	$em->persist($oConviteItem);
	 	
	 	//Montar tabela que será enviada por email
	 	$linha = $i + 1;
	 	$html .= '<tr style="background-color:#f9f9f9;padding:0; border:1px solid #ddd;text-align: center;">';
	 	$html .= '<td style="padding: 10px;">'.$linha.'</td>';
	 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>';
	 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getQuantidade().'</td>';
	 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getValorUnitario().'</td>';
		$html .= '</tr>';
		
	}
	
	#################################################################################
	## SALVAR NO FINANCEIRO
	#################################################################################
	#################################################################################
	## Definir os valores fixos
	#################################################################################
	$codGrpAssociacao	= '1';
	
	/** DATA DE VENCIMENTO **/
	$oVendaConf = $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codFormatura' => $system->getCodOrganizacao() , 'codVendaTipo' => I));
	
	if ($oVendaConf && $codFormaPag == 'BOL'){
		
		if ($oVendaConf->getDiasVencimentoBoleto() > 0){
			$dataVenc			= date($system->config["data"]["dateFormat"],strtotime("+".$oVendaConf->getDiasVencimentoBoleto()." days")); // Adicionar o número de dias configurado para geração de boleto
		}else{
			$dataVenc			= date($system->config["data"]["dateFormat"]); 
		}
		
	}else{
		$dataVenc			= date($system->config["data"]["dateFormat"]); 
	}
	
	//$qtdeVendida		= ($qtdeVendida < $oRifa->getQtdeObrigatorio()) ? $oRifa->getQtdeObrigatorio() : $qtdeVendida;
	//$valorTotal			= ($qtdeVendida * $oRifa->getValorUnitario());
	$codTipoRec			= "U";
	$parcela			= 1;
	$codRecPer			= null;
	$valorJuros			= 0;
	$valorMora			= 0;
	$valorDesconto		= 0;
	$valorOutros		= 0;
	$numParcelas		= 1;
	$parcelaInicial		= 1;
	$obs				= null;
	$descricao			= 'Venda de convite extra';
	$indValorParcela	= null;
	$indSomenteVis		= 1;
	
	#################################################################################
	## Resgatar os parâmetros da categoria
	#################################################################################
	$codCatConvite			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_CONVITE_EXTRA");
	$codCatBoleto			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
	$codCatConveniencia		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_CONVENIENCIA");
	//$codCentroCustoConvite	= ($oRifa->getCodCentroCusto()) ? $oRifa->getCodCentroCusto()->getCodigo() : null;
	
	#################################################################################
	## Ajustar o array de valores de rateio
	#################################################################################
	$_pctRateio			= array();
	$_valorRateio		= array();
	$_codCategoria		= array();
	$codCentroCusto		= array();
	$_codRateio			= array();
	
	//Convite
	$pctConvite 		= round((100 * $valorTotalConv) / $valorTotal,2);
	$_pctRateio[]		= $pctConvite;		
	$_valorRateio[]		= $valorTotalConv;
	$_codCategoria[]	= $codCatConvite;
	$_codCentroCusto[]	= null;
	$_codRateio[]		= null;
	
	//Custo do boleto
	if ($taxaBol > 0){
		$pctBoleto	 		= round((100 * $taxaBol) / $valorTotal,2);
		$_pctRateio[] 		= $pctBoleto;
		$_valorRateio[]		= $taxaBol;
		$_codCategoria[]	= $codCatBoleto;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
	}
	
	//Custo do boleto
	if ($taxaConv > 0){
		$pctConv	 		= round((100 * $taxaConv) / $valorTotal,2);
		$_pctRateio[] 		= $pctConv;
		$_valorRateio[]		= $taxaConv;
		$_codCategoria[]	= $codCatConveniencia;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
	}
	
	$pctRateio		= $_pctRateio;
	$valorRateio	= $_valorRateio;
	$codCategoria	= $_codCategoria;
	$codCentroCusto	= $_codCentroCusto;
	$codRateio		= $_codRateio;
	$aValor			= array($valorTotal);
	//$dataVenc		= date($system->config["data"]["dateFormat"]);
	$aData			= array($dataVenc);
	
	#################################################################################
	## Ajustar os campos do tipo CheckBox
	#################################################################################
	$flagRecebida		= 1;
	$flagReceberAuto	= 0;
	
	#################################################################################
	## Buscar a pessoa associada ao formando
	#################################################################################
	//$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$codUsuario);
	
	#################################################################################
	## Criar o objeto do contas a Receber
	#################################################################################
	$conta		= new \Zage\Fin\ContaReceber();
	
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	//$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	$oStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => "A"));
	$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
	$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => $codRecPer));
	$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => $codTipoRec));
	//$oContaRec	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codContaRec));
	
	#################################################################################
	## Ajustar os valores
	#################################################################################
	$valorTotal		= \Zage\App\Util::toPHPNumber($valorTotal);
	
	#################################################################################
	## Escrever os valores no objeto
	#################################################################################
	$conta->setCodOrganizacao($oOrg);
	$conta->setCodFormaPagamento($oFormaPag);
	$conta->setCodStatus($oStatus);
	$conta->setCodMoeda($oMoeda);
	$conta->setCodPessoa($oFormando);
	//$conta->setNumero($numero);
	$conta->setDescricao($descricao);
	$conta->setValor($valorTotal);
	$conta->setValorJuros($valorJuros);
	$conta->setValorMora($valorMora);
	$conta->setValorDesconto($valorDesconto);
	$conta->setValorOutros($valorOutros);
	$conta->setDataVencimento($dataVenc);
	$conta->setDocumento('dewdwe');
	$conta->setObservacao($obs);
	$conta->setNumParcelas($numParcelas);
	$conta->setParcelaInicial($parcelaInicial);
	$conta->setParcela($parcela);
	$conta->setCodPeriodoRecorrencia($oPeriodo);
	$conta->setCodTipoRecorrencia($oTipoRec);
	//$conta->setIntervaloRecorrencia($intervaloRec);
	$conta->setCodConta($oConta);
	$conta->setIndReceberAuto($flagReceberAuto);
	$conta->_setflagRecebida($flagRecebida);
	$conta->_setIndValorParcela($indValorParcela);
	$conta->_setValorTotal($valorTotal);
	$conta->setCodGrupoAssociacao($codGrpAssociacao);
	//$conta->setIndSomenteVisualizar($indSomenteVis);
	$conta->setCodTransacao($codTransacaoVenda);
	
	$conta->_setArrayValores($aValor);
	$conta->_setArrayDatas($aData);
	$conta->_setArrayCodigosRateio($codRateio);
	$conta->_setArrayCategoriasRateio($codCategoria);
	$conta->_setArrayCentroCustoRateio($codCentroCusto);
	$conta->_setArrayValoresRateio($valorRateio);
	$conta->_setArrayPctRateio($pctRateio);

	#################################################################################
	## SALVAR
	#################################################################################
	try {
		$erro	= $conta->salva();
	
		if ($erro) {
			$log->err("Erro ao salvar: ".$erro);
			throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
			$em->getConnection()->rollback();
			$em->clear();			
			exit;
		}else{
			$em->flush();
			$em->clear();
			$em->getConnection()->commit();
		}
	} catch (Exception $e) {
		$em->getConnection()->rollback();
		$log->debug("Erro ao salvar o convite:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
	
	
	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 

echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteVenda->getCodigo());

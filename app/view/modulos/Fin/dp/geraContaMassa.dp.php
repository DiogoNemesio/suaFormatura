<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $em,$log,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['dataVenc']))			$dataVenc			= \Zage\App\Util::antiInjection($_POST['dataVenc']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['codContaRec']))		$codContaRec		= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['codTipoValor']))		$codTipoValor		= \Zage\App\Util::antiInjection($_POST['codTipoValor']);
if (isset($_POST['numMeses']))			$numMeses			= \Zage\App\Util::antiInjection($_POST['numMeses']);
if (isset($_POST['indValorExtra']))		$indValorExtra		= \Zage\App\Util::antiInjection($_POST['indValorExtra']);
if (isset($_POST['numMesesMax']))		$numMesesMax		= \Zage\App\Util::antiInjection($_POST['numMesesMax']);
if (isset($_POST['aSelFormandos']))		$aSelFormandos		= \Zage\App\Util::antiInjection($_POST['aSelFormandos']);
$aSelFormandos		= explode(",",$aSelFormandos);

#################################################################################
## Validar a data de vencimento
#################################################################################
if (\Zage\App\Util::validaData($dataVenc, $system->config["data"]["dateFormat"]) == false) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Data de Vencimento inválida"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Data de Vencimento inválida")));
	exit;
}

#################################################################################
## Validar o número de meses
#################################################################################
if (!$numMeses) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Número de meses deve ser informado"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Número de meses deve ser informado")));
	exit;
}

if ($numMeses > $numMesesMax) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Número de meses deve caber no intervalo até a data da Formatura"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Número de meses deve caber no intervalo até a data da Formatura")));
	exit;
}

#################################################################################
## Resgata as informações do banco
#################################################################################
try {

	$formandos	= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if (sizeof($formandos) == 0) 	\Zage\App\Erro::halt($tr->trans('Formando[s] não encontrado !!!'));


#################################################################################
## Ajustar os campos de valores
#################################################################################
$valor		= \Zage\App\Util::to_float($valor);

#################################################################################
## Cria o array de vencimentos e de valores
#################################################################################
$aValor			= array();
$aData			= array();
$_valParcela	= round($valor / $numMeses,2);
list ($dia, $mes, $ano) = split ('[/.-]', $dataVenc);
for ($i = 0; $i < $numMeses; $i++) {
	$_mes			= date("m",mktime(0, 0, 0, ($mes + $i), 1 , $ano));
	$_ano			= date("Y",mktime(0, 0, 0, ($mes + $i), 1 , $ano));
	$numDays		= cal_days_in_month(CAL_GREGORIAN, $_mes, $_ano);
	
	if ($dia > $numDays) {
		$date		= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes + $i, $numDays , $ano));
	}else{
		$date		= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, $mes + $i, $dia , $ano));
	}
	
	$aData[]	= $date;
	
	if ($codTipoValor == "M") {
		$aValor[]	= $valor;
	}else{
		if ($i == ($numMeses-1)) {
			$valorParcela	= round($valor - ($_valParcela * ($numMeses - 1)),2);	
			$log->info("Ultima parcela -> Valor da parcela: ".$valorParcela);
		}else{
			$valorParcela	= $_valParcela;
		}
		
		$log->info("I = $i, Valor da parcela: ".$valorParcela);
		
		$aValor[]	= $valorParcela;
	}
}


#################################################################################
## Criar as variáveis
#################################################################################
if ($codTipoValor == "M") {
	$indValorParcela	= 1;
}else{
	$indValorParcela	= null;
}

$codTipoRec		= ($numMeses == 1)  ? "U" : "P";
$parcela		= ($numMeses == 1)  ? 1 : null;
$codRecPer		= "M";
$taxaAdmin		= \Zage\App\Util::to_float($oOrgFmt->getValorPorFormando());
$taxaBoleto		= ($codFormaPag == "BOL") ? \Zage\App\Util::to_float($oOrgFmt->getValorPorBoleto()) : 0;
$taxaUso		= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$valorJuros		= 0;
$valorMora		= 0;
$valorDesconto	= 0;
$valorOutros	= ($indValorExtra)	? ($taxaAdmin + $taxaBoleto + $taxaUso) : 0;
$numParcelas	= $numMeses;
$parcelaInicial	= 1;
$indAut			= 1;
$obs			= null;

#################################################################################
## Resgatar os parâmetros da categoria
#################################################################################
$codCatMensalidade		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
$codCatPortal			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
$codCatBoleto			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
$codCatOutrasTaxas		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");

#################################################################################
## Calcular valor Total e Valor da Parcela
#################################################################################
$valorTotal	= 0;
for ($i = 0; $i < sizeof($aValor); $i++) {
	$valorTotal += (\Zage\App\Util::to_float($aValor[$i]) + \Zage\App\Util::to_float($valorOutros));
}
if ($codTipoValor != "M") {
	$valor		= round($valor / $numMeses,2);
}
$valorParcela	= $valor + $valorMora + $valorJuros + $valorOutros - $valorDesconto;


#################################################################################
## Ajustar o array de valores de rateio
#################################################################################
if (!$indValorExtra) {
	$pctRateio		= array(100);
	$valorRateio	= array($valorParcela);
	$codCategoria	= array($codCatMensalidade);
	$codCentroCusto	= array("");
	$codRateio		= array("");
	

}else{
	$pctRateio[]		= round(100*$valor/$valorParcela,2);
	$valorRateio[]		= $valor;
	$codCategoria[]		= $codCatMensalidade;
	$codCentroCusto[]	= null;
	$codRateio[]		= null;
	
	if ($taxaAdmin)		{
		$pctRateio[]		= round(100*$taxaAdmin/$valorParcela,2);
		$valorRateio[]		= $taxaAdmin;
		$codCategoria[]		= $codCatOutrasTaxas;
		$codCentroCusto[]	= null;
		$codRateio[]		= null;
	}

	if ($taxaBoleto)		{
		$pctRateio[]		= round(100*$taxaBoleto/$valorParcela,2);
		$valorRateio[]		= $taxaBoleto;
		$codCategoria[]		= $codCatBoleto;
		$codCentroCusto[]	= null;
		$codRateio[]		= null;
	}
	
	if ($taxaUso)		{
		$pctRateio[]		= round(100*$taxaUso/$valorParcela,2);
		$valorRateio[]		= $taxaUso;
		$codCategoria[]		= $codCatPortal;
		$codCentroCusto[]	= null;
		$codRateio[]		= null;
	}
}

#################################################################################
## Ajustar os campos do tipo CheckBox
#################################################################################
$flagRecebida		= 0;
$flagReceberAuto	= 0;
$flagAlterarSeq		= 0;

#################################################################################
## Iniciar a transação no banco
#################################################################################
$em->getConnection()->beginTransaction();

#################################################################################
## Faz o loop nas parcelas para montar a tabela
#################################################################################
for ($i = 0 ;$i < sizeof($formandos); $i++) {

	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $formandos[$i]->getCpf()));
	if (!$oPessoa) {
		$erro	= "Não foi possível encontrar a Pessoa referente ao Formando: ".$formandos[$i]->getCodigo();
		$log->err($erro);
		$em->getConnection()->rollback();
		$em->clear();
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
		exit;
	}
	
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrg		= $em->getReference('Entidades\ZgadmOrganizacao'				,$system->getCodOrganizacao());
	$oForma		= $em->getReference('Entidades\ZgfinFormaPagamento' 			,$codFormaPag);
	$oStatus	= $em->getReference('Entidades\ZgfinContaStatusTipo'			,"A");
	$oMoeda		= $em->getReference('Entidades\ZgfinMoeda'						,1);
	$oPeriodo	= $em->getReference('Entidades\ZgfinContaRecorrenciaPeriodo'	,$codRecPer);
	$oTipoRec	= $em->getReference('Entidades\ZgfinContaRecorrenciaTipo'		,$codTipoRec);
	$oContaRec	= $em->getReference('Entidades\ZgfinConta'						,$codContaRec);
	
	#################################################################################
	## Criar o objeto do contas a Receber
	#################################################################################
	$conta		= new \Zage\Fin\ContaReceber();

	#################################################################################
	## Criar as variáveis das parcelas
	#################################################################################
	$descricao		= "Mensalidade";
	
	#################################################################################
	## Escrever os valores no objeto
	#################################################################################
	$conta->setCodOrganizacao($oOrg);
	$conta->setCodFormaPagamento($oForma);
	$conta->setCodStatus($oStatus);
	$conta->setCodMoeda($oMoeda);
	$conta->setCodPessoa($oPessoa);
	$conta->setNumero(null);
	$conta->setDescricao($descricao);
	$conta->setValor($valor);
	$conta->setValorJuros($valorJuros);
	$conta->setValorMora($valorMora);
	$conta->setValorDesconto($valorDesconto);
	$conta->setValorOutros($valorOutros);
	$conta->setDataVencimento($dataVenc);
	$conta->setDocumento(null);
	$conta->setObservacao($obs);
	$conta->setNumParcelas($numParcelas);
	$conta->setParcelaInicial($parcelaInicial);
	$conta->setParcela($parcela);
	$conta->setCodPeriodoRecorrencia($oPeriodo);
	$conta->setCodTipoRecorrencia($oTipoRec);
	$conta->setIntervaloRecorrencia(1);
	$conta->setCodConta($oContaRec);
	$conta->setIndReceberAuto($flagReceberAuto);
	$conta->_setflagRecebida($flagRecebida);
	$conta->_setIndValorParcela($indValorParcela);
	$conta->_setValorTotal($valorTotal);
	
	$conta->_setArrayValores($aValor);
	$conta->_setArrayDatas($aData);
	$conta->_setArrayCodigosRateio($codRateio);
	$conta->_setArrayCategoriasRateio($codCategoria);
	$conta->_setArrayCentroCustoRateio($codCentroCusto);
	$conta->_setArrayValoresRateio($valorRateio);
	$conta->_setArrayPctRateio($pctRateio);
	
	$conta->_setIndAlterarSeq(0);
	
	
	#################################################################################
	## Coloca na fila do doctrine
	#################################################################################
	try {
		$erro	= $conta->salva();
	
		if ($erro) {
			$log->err("Erro ao salvar: ".$erro);
			$em->getConnection()->rollback();
			$em->clear();
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
		}
	
	
	} catch (\Exception $e) {
		$log->err("Erro: ".$e->getMessage());
		$em->getConnection()->rollback();
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
	
}

#################################################################################
## Salva efetivamente no banco
#################################################################################
try {
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	
} catch (\Exception $e) {
	$log->err("Erro: ".$e->getMessage());
	$em->getConnection()->rollback();
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||');
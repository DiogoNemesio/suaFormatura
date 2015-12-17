<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$log,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['id']))					$id						= \Zage\App\Util::antiInjection($_POST['id']);
if (isset($_POST['dataVenc']))				$dataVenc				= \Zage\App\Util::antiInjection($_POST['dataVenc']);
if (isset($_POST['codFormaPag']))			$codFormaPag			= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['codContaRec']))			$codContaRec			= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['numMeses']))				$numMeses				= \Zage\App\Util::antiInjection($_POST['numMeses']);
if (isset($_POST['indValorExtra']))			$indValorExtra			= \Zage\App\Util::antiInjection($_POST['indValorExtra']);
if (isset($_POST['numMesesMax']))			$numMesesMax			= \Zage\App\Util::antiInjection($_POST['numMesesMax']);
if (isset($_POST['aSelEventos']))			$aSelEventos			= \Zage\App\Util::antiInjection($_POST['aSelEventos']);
if (isset($_POST['aValorEventos']))			$aValorEventos			= \Zage\App\Util::antiInjection($_POST['aValorEventos']);
if (isset($_POST['aValor']))				$aValor					= \Zage\App\Util::antiInjection($_POST['aValor']);
if (isset($_POST['aData']))					$aData					= \Zage\App\Util::antiInjection($_POST['aData']);
if (isset($_POST['aTaxas']))				$aTaxas					= \Zage\App\Util::antiInjection($_POST['aTaxas']);
if (isset($_POST['aTotal']))				$aTotal					= \Zage\App\Util::antiInjection($_POST['aTotal']);
if (isset($_POST['totalGeral']))			$totalGeral				= \Zage\App\Util::antiInjection($_POST['totalGeral']);
if (isset($_POST['taxaBol']))				$taxaBol				= \Zage\App\Util::antiInjection($_POST['taxaBol']);
if (isset($_POST['taxaAdmin']))				$taxaAdmin				= \Zage\App\Util::antiInjection($_POST['taxaAdmin']);
if (isset($_POST['codTipoConta']))			$codTipoConta			= \Zage\App\Util::antiInjection($_POST['codTipoConta']);


#################################################################################
## Criar o array de seleção de eventos
#################################################################################
$aSelEventos		= explode(",",$aSelEventos);
$aValorEventos		= explode(",",$aValorEventos);

//$log->info("aSelEventos: ".serialize($aSelEventos));
//$log->info("aValorEventos: ".serialize($aValorEventos));

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codFormando)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de Parâmetros 2'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de Parâmetros 2')));
	exit;
}

#################################################################################
## Verificar se o usuário existe
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codFormando));
if (!$oUsuario) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Formando não existe'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Formando não existe')));
	exit;
}

#################################################################################
## Resgatar o status da associação com a Formatura
#################################################################################
$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codFormando,'codOrganizacao' => $system->getCodOrganizacao()));
$codStatus	= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getCodigo() : null;
$codPerfil	= ($oStatus->getCodPerfil()) ? $oStatus->getCodPerfil(): null;
if (!$codPerfil) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Perfil inválido para o Formando'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Perfil inválido para o Formando')));
	exit;
}

#################################################################################
## Verificar o status da associação a Formatura, para definir se poderá ou não
## desistir da formatura
#################################################################################
switch ($codStatus) {
	case "A":
	case "P":
	case "B":
		$podeDesistir	= true;
		break;
	default:
		$podeDesistir	= false;
		break;
}

if (!$podeDesistir)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Tentativa indevida de desistência: 0x8jga62'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Tentativa indevida de desistência: 0x8jga62')));
	exit;
}

#################################################################################
## Verificar se o usuário tem perfil de formando nessa organização
#################################################################################
if ($codPerfil->getCodTipoUsuario()->getCodigo() != "F") {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Esse usuário não é um formando'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Esse usuário não é um formando')));
	exit;
}

#################################################################################
## Resgatar o valor por formando
#################################################################################
$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
if ($oOrgFmt->getValorPrevistoTotal() && $oOrgFmt->getQtdePrevistaFormandos()){
	$valorFormatura = round(($oOrgFmt->getValorPrevistoTotal()/$oOrgFmt->getQtdePrevistaFormandos()),2);
}else{
	$valorFormatura	= 0;
}

#################################################################################
## Resgatar os valores ja pago por esse formando
#################################################################################
$aPago				= \Zage\Fmt\Financeiro::getValorPagoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$aProvisionado		= \Zage\Fmt\Financeiro::getValorProvisionadoUnicoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$valPagoMensalidade	= \Zage\App\Util::to_float($aPago["mensalidade"]);
$valPagoSistema		= \Zage\App\Util::to_float($aPago["sistema"]);
$taxaUso			= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$taxaAdmin			= \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao());
$diaVencimento		= ($oOrgFmt->getDiaVencimento()) ? $oOrgFmt->getDiaVencimento() : 5;
$dataVenc			= date($system->config["data"]["dateFormat"],mktime(0, 0, 0, date('m') + 1, $diaVencimento , date('Y')));

$dataConclusao		= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Data de Conclusão não informada"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Data de Conclusão não informada")));
	exit;
}

$hoje				= new DateTime('now');
$dataAtivacao		= ($oStatus->getDataCadastro()) ? $oStatus->getDataCadastro() : null;
if (!$dataAtivacao)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Formando: ".$oUsuario->getNome()." sem data de Ativação !!!"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Formando: ".$oUsuario->getNome()." sem data de Ativação !!!")));
	exit;
}

$interval1			= $dataAtivacao->diff($hoje);
$interval2			= $dataConclusao->diff($dataAtivacao);
$interval3			= $dataConclusao->diff($hoje);

$numMesesUso		= (($interval1->format('%y') * 12) + $interval1->format('%m'));
$numMesesTotal		= (($interval2->format('%y') * 12) + $interval2->format('%m'));
$numMesesConc		= (($interval3->format('%y') * 12) + $interval3->format('%m'));

$valDevidoSistema	= round($numMesesUso * $taxaUso,2);
$valTotalSistema	= round($numMesesTotal * $taxaUso,2);

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

if ($numMeses != sizeof($aValor)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Número de meses deve ser igual ao tamanho do array de valores"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Número de meses deve ser igual ao tamanho do array de valores")));
	exit;
}

#################################################################################
## Validar as datas de vencimento
#################################################################################
if ($numMeses != sizeof($aData)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Número de meses deve ser igual ao tamanho do array de Vencimentos"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Número de meses deve ser igual ao tamanho do array de Vencimentos")));
	exit;
}else{
	for ($i = 0; $i < sizeof($aData); $i++) {
		if (\Zage\App\Util::validaData($aData[$i], $system->config["data"]["dateFormat"]) == false) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Data de Vencimento inválida"));
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Data de Vencimento inválida")));
			exit;
		}
	}
}


#################################################################################
## Ajustar as variáveis Fixas
#################################################################################
$codTipoRec			= ($numMeses == 1)  ? "U" : "P";
$parcela			= ($numMeses == 1)  ? 1 : null;
$codRecPer			= "M";
$valorJuros			= 0;
$valorMora			= 0;
$valorDesconto		= 0;
$numParcelas		= $numMeses;
$parcelaInicial		= 1;
$obs				= null;
$indValorParcela	= 1;

#################################################################################
## Resgatar os parâmetros da categoria
#################################################################################
$codCatMensalidade		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
$codCatPortal			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
$codCatBoleto			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
$codCatOutrasTaxas		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
$codCatDevMensalidade	= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_MENSALIDADE");
$codCatDevSistema		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_SISTEMA");

#################################################################################
## Calcular valor Total e Valor da Parcela, e montar o array de outrosValores
#################################################################################
$aOutrosValores		= array();
$valorTotal			= 0;
for ($i = 0; $i < sizeof($aValor); $i++) {
	$valorOutros			= \Zage\App\Util::to_float($aTaxas[$i]);
	$aOutrosValores[]		=  $valorOutros;
	$valorTotal 			+= (\Zage\App\Util::to_float($aValor[$i]) + $valorOutros);
}

$valorOutros		= round(\Zage\App\Util::to_float($valorOutros),2);
$valorTotal			= round(\Zage\App\Util::to_float($valorTotal),2);
$totalGeral			= round(\Zage\App\Util::to_float($totalGeral),2);

#################################################################################
## Valida o valor total
#################################################################################
if (floatval($valorTotal)	!= floatval($totalGeral))	{
	$log->err("Valor total calculado: ".$valorTotal);
	$log->err("Valor total Informado: ".$totalGeral);
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Somatório dos valores totais das parcelas difere do valor total informado'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Somatório dos valores totais das parcelas difere do valor total informado')));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de conta: $codTipoConta !!!"));
echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Tipo de conta: $codTipoConta !!!")));
exit;


#################################################################################
## Ajustar o array de valores de rateio
#################################################################################
$pctRateio			= array();
$valorRateio		= array();
$codCategoria		= array();
$codCentroCusto		= array();
$codRateio			= array();

#################################################################################
## Criar o array de acordo com o número de parcelas
#################################################################################
for ($i = 0; $i < $numMeses; $i++) {
	$_valor				= \Zage\App\Util::to_float($aValor[$i]);
	$_taxaAdmin			= \Zage\App\Util::to_float($taxaAdmin);
	$_taxaBol			= \Zage\App\Util::to_float($taxaBol);
	$_sistema			= \Zage\App\Util::to_float($aSistema[$i]);
	
	$_valParcela		= ($_valor + $_taxaAdmin + $_taxaBol + $_sistema);
	
	$_pctRateio			= array();
	$_valorRateio		= array();
	$_codCategoria		= array();
	$_codCentroCusto	= array();
	$_codRateio			= array();
	
	$_pctRateio[]		= round(100*$_valor/$_valParcela,2);
	$_valorRateio[]		= $_valor;
	$_codCategoria[]	= $codCatMensalidade;
	$_codCentroCusto[]	= null;
	$_codRateio[]		= null;
	
	if ($indValorExtra && $_taxaAdmin)		{
		$_pctRateio[]		= round(100*$_taxaAdmin/$_valParcela,2);
		$_valorRateio[]		= $_taxaAdmin;
		$_codCategoria[]	= $codCatOutrasTaxas;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
	}
	
	if ($indValorExtra && $_taxaBol)		{
		$_pctRateio[]		= round(100*$_taxaBol/$_valParcela,2);
		$_valorRateio[]		= $_taxaBol;
		$_codCategoria[]	= $codCatBoleto;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
	}
	
	if ($_sistema)		{
		$_pctRateio[]		= round(100*$_sistema/$_valParcela,2);
		$_valorRateio[]		= $_sistema;
		$_codCategoria[]	= $codCatPortal;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
	}
	
	$pctRateio[$i]		= $_pctRateio;
	$valorRateio[$i]	= $_valorRateio;
	$codCategoria[$i]	= $_codCategoria;
	$codCentroCusto[$i]	= $_codCentroCusto;
	$codRateio[$i]		= $_codRateio;
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
	$conta->_setIndAlterarSeq($flagAlterarSeq);
	$conta->_setValorTotal($valorTotal);
	
	$conta->_setArrayValores($aValor);
	$conta->_setArrayOutrosValores($aOutrosValores);
	$conta->_setArrayDatas($aData);
	$conta->_setArrayCodigosRateio($codRateio);
	$conta->_setArrayCategoriasRateio($codCategoria);
	$conta->_setArrayCentroCustoRateio($codCentroCusto);
	$conta->_setArrayValoresRateio($valorRateio);
	$conta->_setArrayPctRateio($pctRateio);
	
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
## Define a variável de sessão da listagem de contas para a data do primeiro vencimento
#################################################################################
$_SESSION["_CRLIS_dataFiltro"]	= $dataVenc;

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
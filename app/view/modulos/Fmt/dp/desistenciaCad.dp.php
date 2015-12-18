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
if (isset($_POST['codTipoBaseCalculo']))	$codTipoBaseCalculo		= \Zage\App\Util::antiInjection($_POST['codTipoBaseCalculo']);
if (isset($_POST['pctMulta']))				$pctMulta				= \Zage\App\Util::antiInjection($_POST['pctMulta']);

//$log->info("POST DesistênciaCad: ".serialize($_POST));

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
## Validar o número de meses, somente se for gerar alguma conta
#################################################################################
if ($codTipoConta != "N") {
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
}


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
## Resgatar o CodPessoa do formando nessa organização
#################################################################################
$oPessoa		= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(), $codFormando);
$codPessoa		= $oPessoa->getCodigo();
if (!$codPessoa)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Não conseguimos encontrar o CPF do Formando no financeiro'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Não conseguimos encontrar o CPF do Formando no financeiro')));
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
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Não consegui resgatar o valor total da formatura"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Não consegui resgatar o valor total da formatura")));
	exit;
}

#################################################################################
## Resgatar os valores ja pago por esse formando
#################################################################################
$aPago				= \Zage\Fmt\Financeiro::getValorPagoFormando($system->getCodOrganizacao(),$oUsuario->getCpf());
$valPagoMensalidade	= \Zage\App\Util::to_float($aPago["mensalidade"]);
$valPagoSistema		= \Zage\App\Util::to_float($aPago["sistema"]);
$taxaUso			= \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao()));
$taxaAdmin			= \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao());

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
## Calcular o valor da multa
#################################################################################
if ($codTipoBaseCalculo == "P")	{
	$baseCalculo		= $valPagoMensalidade;
}else{
	$baseCalculo		= $valorFormatura;
}
$valMulta				= round($baseCalculo * $pctMulta / 100,2);
if ($valMulta < 0)		{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Erro no calculo do valor da multa"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Erro no calculo do valor da multa")));
	exit;
}

#################################################################################
## Calcular o saldo devido de sistema
#################################################################################
if (sizeof($aSelEventos) > 0) { 	// Desistência parcial
	$saldoSistema		= ($valTotalSistema - $valPagoSistema);
}else{						// Desistência total
	$saldoSistema		= ($valDevidoSistema - $valPagoSistema);
}

#################################################################################
## Calcular o saldo da Mensalidade
#################################################################################
$totalEventos		= 0;
for ($i = 0; $i < sizeof($aValorEventos); $i++) {
	$totalEventos	+= \Zage\App\Util::to_float($aValorEventos[$i]);
}

#################################################################################
## Calcular o saldo da Mensalidade
#################################################################################
$saldoMensalidade	= ($totalEventos - $valPagoMensalidade);

#################################################################################
## Calcular o saldo final 
#################################################################################
$saldoFinal			= round(\Zage\App\Util::to_float($valMulta + $saldoMensalidade + $saldoSistema),2);

#################################################################################
## Verificar se o parâmetro de tipo de conta está de acordo com o que foi calculado na tela
#################################################################################
if (($saldoFinal > 0) && ($codTipoConta != "R") ) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Parâmetro codTipoConta com erros, código: 0x9a345"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro codTipoConta com erros, código: 0x9a345")));
	exit;
}elseif (($saldoFinal == 0) && ($codTipoConta != "N") ) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Parâmetro codTipoConta com erros, código: 0x9a346"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro codTipoConta com erros, código: 0x9a346")));
	exit;
}elseif (($saldoFinal < 0) && ($codTipoConta != "P") ) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Parâmetro codTipoConta com erros, código: 0x9a347"));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro codTipoConta com erros, código: 0x9a347")));
	exit;
}

#################################################################################
## Converter o saldo final para positivo
#################################################################################
$saldoFinal		= ($saldoFinal < 0) ? $saldoFinal * -1 : $saldoFinal;

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
$indValorParcela	= null;

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
$valorTaxas			= 0;
for ($i = 0; $i < sizeof($aValor); $i++) {
	$valorOutros			= \Zage\App\Util::to_float($aTaxas[$i]);
	$valorTaxas				+= \Zage\App\Util::to_float($aTaxas[$i]);
	$aOutrosValores[]		=  $valorOutros;
	$valorTotal 			+= (\Zage\App\Util::to_float($aValor[$i]) + $valorOutros);
}

$valorOutros		= round(\Zage\App\Util::to_float($valorOutros),2);
$valorTaxas			= round(\Zage\App\Util::to_float($valorTaxas),2);
$valorTotal			= round(\Zage\App\Util::to_float($valorTotal),2);
$totalGeral			= round(\Zage\App\Util::to_float($totalGeral),2);
$saldoFinal			= round(\Zage\App\Util::to_float($saldoFinal),2);

#################################################################################
## Valida o valor total
#################################################################################
//$log->err("Valor total calculado: ".$valorTotal);
//$log->err("Valor total Informado: ".$totalGeral);
if (floatval($valorTotal)	!= floatval($totalGeral))	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Somatório dos valores totais das parcelas difere do valor total informado'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Somatório dos valores totais das parcelas difere do valor total informado')));
	exit;
}

//$log->err("Saldo Final: ".$saldoFinal);
//$log->err("Valor Taxas: ".$valorTaxas);

if (floatval($saldoFinal + $valorTaxas)	!= floatval($totalGeral))	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Saldo final difere to total geral, erro: 0x9ga5163'));
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Saldo final difere to total geral, erro: 0x9ga5163')));
	exit;
}

#################################################################################
## Calcular quais contas devem ser a Receber e a Pagar
#################################################################################
if ($saldoSistema		== 0)	{
	$contaSistema		= "N";
}elseif ($saldoSistema 	< 0) {
	$contaSistema		= "P";
}else{
	$contaSistema		= "R";
}

if (($saldoMensalidade + $valMulta)		== 0)	{
	$contaMensalidade		= "N";
}elseif (($saldoMensalidade + $valMulta)< 0) {
	$contaMensalidade		= "P";
}else{
	$contaMensalidade		= "R";
}

if ($contaMensalidade == $contaSistema) {
	if ($contaMensalidade	== "N") {
		$numContas			= 0;
	}else{
		$numContas			= 1;
	}
}else{
	if (($contaMensalidade == "N") || ($contaSistema == "N")) {
		$numContas			= 1;
	}else{
		$numContas			= 3;
	}
		
}

#################################################################################
## Ajustar os valores dos saldos para positivo
## Atribuir o valor de multa ao saldo da Mensalidade
#################################################################################
$saldoMensalidade			= (($saldoMensalidade + $valMulta) 	< 0) ? ($saldoMensalidade + $valMulta) 	* -1 : ($saldoMensalidade + $valMulta);
$saldoSistema				= ($saldoSistema 		< 0) ? $saldoSistema 		* -1 : $saldoSistema;



#################################################################################
## Iniciar a transação no banco
#################################################################################
$em->getConnection()->beginTransaction();

#################################################################################
## Configurações de geração de contas, apenas se for necessário
#################################################################################
if ($numContas	> 0) {

	#################################################################################
	## Gerar o código de transação das contas
	#################################################################################
	$codTransacao		= \Zage\Adm\Sequencial::proximoValor('ZgfinSeqCodTransacao');
	
	#################################################################################
	## Gerar a conta de devolução de mensalidade (PAGAR)
	## São 3 casos que irá contemplar
	## 1) Precisa devolver Mensalidade e Sistema
	## 2) Precisa devolver Mensalidade apenas e não existe saldo de sistema (SaldoSistema = 0)
	## 3) Precisa devolver Mensalidade, porém precisa receber o saldo de sistema
	#################################################################################
	if ($contaMensalidade == "P") {

		#################################################################################
		## Variável para descobrir se será necessário gerar mais de uma conta de devolução
		#################################################################################
		$indGeraOutraDevolucao	= false;
		
		#################################################################################
		## Ajustar o array de valores de rateio
		#################################################################################
		$pctRateio			= array();
		$valorRateio		= array();
		$codCategoria		= array();
		$codCentroCusto		= array();
		$codRateio			= array();

		#################################################################################
		## Calcular os percentuais de devolução de sistema e Mensalidade
		#################################################################################
		if ($contaMensalidade == "P" && $contaSistema == "P") {
				
			#################################################################################
			## Caso 1: Precisa devolver Mensalidade e Sistema
			## Somente 1 conta a pagar com as 2 devoluções (2 categorias)
			#################################################################################
			$pctMensalidade			= round(($saldoMensalidade	* 100) / $saldoFinal,2);
			$pctSistema				= round(($saldoSistema 		* 100) / $saldoFinal,2);
				
		}elseif ($contaMensalidade	== "P") {
			#################################################################################
			## Caso 2: Precisa devolver Mensalidade apenas e não existe saldo de sistema (SaldoSistema = 0)
			## Somente 1 conta a pagar com a devolução da mensalidade
			#################################################################################
			$pctMensalidade			= 100;
			$pctSistema				= 0;
				
		}else{
			#################################################################################
			## Caso 3: Precisa devolver Mensalidade, porém precisa receber o saldo de sistema
			## serão 2 contas a pagar, cada conta terá 100% de rateio de cada valor
			#################################################################################
			$pctMensalidade			= 100;
			$pctSistema				= 0;
			$indGeraOutraDevolucao	= true;
				
		}
		
		#################################################################################
		## Criar o array de acordo com o número de parcelas
		#################################################################################
		for ($i = 0; $i < $numMeses; $i++) {

			$_valor				= \Zage\App\Util::to_float($aValor[$i]);
			$_taxaBol			= \Zage\App\Util::to_float($taxaBol);
			$_taxaAdmin			= \Zage\App\Util::to_float($taxaAdmin);
			if (($_taxaAdmin + $_taxaBol) != $aTaxas[$i]) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Calculo de taxas indevido, erro: 0xp8yh554'));
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Calculo de taxas indevido, erro: 0xp8yh554')));
				exit;
			}
			
			$_valParcela		= ($_valor + $_taxaAdmin + $_taxaBol);
			$pctTaxas			= round((($_taxaAdmin + $_taxaBol) * 100) / $_valParcela,2);
			$pctBol				= round(($_taxaBol * 100) / $_valParcela,2);
			$pctAdmin			= $pctTaxas - $pctBol;
			$pctValores			= 100 - $pctTaxas;
			
				
			#################################################################################
			## Aplicar o percentual somente de valores aos percentuais de sistema e mensalidade
			## ou seja, retirar o percentual de taxas
			#################################################################################
			$pctMensalidade		= round($pctMensalidade * $pctValores / 100,2);
			$pctSistema			= $pctValores - $pctMensalidade;
					
			$_pctRateio			= array();
			$_valorRateio		= array();
			$_codCategoria		= array();
			$_codCentroCusto	= array();
			$_codRateio			= array();
		
			$_pctRateio[]		= $pctMensalidade;
			$_valorRateio[]		= round($_valor*$pctMensalidade/100,2);
			$_codCategoria[]	= $codCatDevMensalidade;
			$_codCentroCusto[]	= null;
			$_codRateio[]		= null;
		

			if ($pctSistema)		{
				$_pctRateio[]		= $pctSistema;
				$_valorRateio[]		= round($_valor*$pctSistema/100,2);
				$_codCategoria[]	= $codCatDevSistema;
				$_codCentroCusto[]	= null;
				$_codRateio[]		= null;
			}
				
			if ($pctAdmin)		{
				$_pctRateio[]		= $pctAdmin;
				$_valorRateio[]		= $_taxaAdmin;
				$_codCategoria[]	= $codCatOutrasTaxas;
				$_codCentroCusto[]	= null;
				$_codRateio[]		= null;
			}
		
			if ($pctBol)		{
				$_pctRateio[]		= $pctBol;
				$_valorRateio[]		= $_taxaBol;
				$_codCategoria[]	= $codCatBoleto;
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
		## Resgata os objetos (chave estrangeiras)
		#################################################################################
		$oPessoa	= $em->getReference('Entidades\ZgfinPessoa'						,$codPessoa);
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
		$devMen		= new \Zage\Fin\ContaPagar();
		
		#################################################################################
		## Criar as variáveis das parcelas
		#################################################################################
		$descricao		= "Devolução de Mensalidade (Desistência)";
		
		#################################################################################
		## Escrever os valores no objeto
		#################################################################################
		$devMen->setCodOrganizacao($oOrg);
		$devMen->setCodFormaPagamento($oForma);
		$devMen->setCodStatus($oStatus);
		$devMen->setCodMoeda($oMoeda);
		$devMen->setCodPessoa($oPessoa);
		$devMen->setNumero(null);
		$devMen->setDescricao($descricao);
		$devMen->setValor($saldoFinal);
		$devMen->setValorJuros($valorJuros);
		$devMen->setValorMora($valorMora);
		$devMen->setValorDesconto($valorDesconto);
		$devMen->setValorOutros($valorOutros);
		$devMen->setDataVencimento($dataVenc);
		$devMen->setDocumento(null);
		$devMen->setObservacao($obs);
		$devMen->setNumParcelas($numParcelas);
		$devMen->setParcelaInicial($parcelaInicial);
		$devMen->setParcela($parcela);
		$devMen->setCodPeriodoRecorrencia($oPeriodo);
		$devMen->setCodTipoRecorrencia($oTipoRec);
		$devMen->setIntervaloRecorrencia(1);
		$devMen->setCodConta($oContaRec);
		$devMen->setIndReceberAuto($flagReceberAuto);
		$devMen->_setflagRecebida($flagRecebida);
		$devMen->_setIndValorParcela($indValorParcela);
		$devMen->_setIndAlterarSeq($flagAlterarSeq);
		$devMen->_setValorTotal($valorTotal);
		$devMen->setCodTransacao($codTransacao);
		
		$devMen->_setArrayValores($aValor);
		$devMen->_setArrayOutrosValores($aOutrosValores);
		$devMen->_setArrayDatas($aData);
		$devMen->_setArrayCodigosRateio($codRateio);
		$devMen->_setArrayCategoriasRateio($codCategoria);
		$devMen->_setArrayCentroCustoRateio($codCentroCusto);
		$devMen->_setArrayValoresRateio($valorRateio);
		$devMen->_setArrayPctRateio($pctRateio);
		
		#################################################################################
		## Coloca na fila do doctrine
		#################################################################################
		try {
			$erro	= $devMen->salva();
		
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
	

}




$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de conta: $codTipoConta !!!"));
echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Tipo de conta: $codTipoConta !!!")));
exit;

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
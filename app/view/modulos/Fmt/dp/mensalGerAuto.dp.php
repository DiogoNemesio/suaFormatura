<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr;


#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata a variável FID com a lista de formandos selecionados
#################################################################################
if (isset($_POST['fid']))	$fid = \Zage\App\Util::antiInjection($_POST["fid"]);
if (!isset($fid))			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de parâmetros 2'))));

#################################################################################
## Descompacta o FID
#################################################################################
\Zage\App\Util::descompactaId($fid);

if (!isset($aSelFormandos))			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Falta de parâmetros 3'))));

#################################################################################
## Gera o array de formandos selecionados a partir da string
#################################################################################
$aSelFormandos				= explode(",",$aSelFormandos);

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codContaRec']))		$codContaRec			= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['indValorExtra']))		{
	$indValorExtra			= \Zage\App\Util::antiInjection($_POST['indValorExtra']);
}else{
	$indValorExtra			= null;
}

#################################################################################
## Validar a conta de recebimento
#################################################################################
try {
	$oConta				= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));
	if (!$oConta)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro 4 inválido'))));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
 

#################################################################################
## Resgatar os dados dos formandos selecionados
#################################################################################
try {
	$formandos				= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos));
	$oOrgFmt				= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Buscar o orçamento aceite, caso exista um, pois ele será usado como base
## Para calcular o valor pendente a ser gerado
## Se não existir, emitir um erro
#################################################################################
$orcamento				= \Zage\Fmt\Orcamento::getVersaoAceita($system->getCodOrganizacao());
if (!$orcamento)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Nenhum orçamento aceito"))));
$valorOrcado			= \Zage\App\Util::to_float($oOrgFmt->getValorPrevistoTotal());
$qtdFormandosBase		= (int) $oOrgFmt->getQtdePrevistaFormandos();
$valTotalFormando		= round($valorOrcado / $qtdFormandosBase,2);


#################################################################################
## Montar o array de retorno de parcelas geradas
#################################################################################
$aContrato		= array();
$aDataAtivacao	= array();
$aFormaPag		= array();
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Verificar se esse usuário é formando na organização atual
	#################################################################################
	if (\Zage\Seg\Usuario::ehFormando($system->getCodOrganizacao(), $formandos[$i]->getCodigo()) != true) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FF')));
	}
	
	#################################################################################
	## Verificar se já foi gerada alguma mensalidade para algum formando
	#################################################################################
	$temMensalidade				= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(), $formandos[$i]->getCodigo());
	if ($temMensalidade)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FE')));

	#################################################################################
	## Resgatar as informações do contrato
	#################################################################################
	$aContrato[$i]				= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $formandos[$i]->getCodigo()));
	if (!$aContrato[$i])		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FD')));
	
	#################################################################################
	## Resgatar as informações de forma de pagamento do contrato
	#################################################################################
	$oFormaPag					= ($aContrato[$i]->getCodFormaPagamaento()) ? $aContrato[$i]->getCodFormaPagamaento() : null;
	if (!$oFormaPag)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FC')));
	$aFormaPag[$i]				= $oFormaPag;

	#################################################################################
	## Resgatar o status da associação com a Formatura
	#################################################################################
	$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $formandos[$i]->getCodigo(),'codOrganizacao' => $system->getCodOrganizacao()));
	
	#################################################################################
	## Resgatar a data de ativação do usuário, para calcular o número de meses de uso
	## do sistema
	#################################################################################
	$dataAtivacao		= ($oStatus->getDataCadastro()) ? $oStatus->getDataCadastro() : null;
	if (!$dataAtivacao)	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3748FB, data de ativação inválida')));
	$aDataAtivacao[$i]	= $dataAtivacao; 
	
}

#################################################################################
## Resgatar a data de conclusão prevista do Orçamento aceito
#################################################################################
$dataConclusao		= $oOrgFmt->getDataConclusao();
if (!$dataConclusao)	\Zage\App\Erro::halt("Data de Conclusão não informada");

#################################################################################
## Resgatar os parâmetros da categoria
#################################################################################
$codCatMensalidade		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
$codCatPortal			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
$codCatBoleto			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
$codCatOutrasTaxas		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");

#################################################################################
## Calcular valores das taxas
#################################################################################
$indRepTaxaSistema		= ($oOrgFmt->getIndRepassaTaxaSistema() !== null) ? $oOrgFmt->getIndRepassaTaxaSistema()	: 1;
$taxaAdmin				= ($indValorExtra)		? \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao())		: 0;;
$taxaUso				= ($indRepTaxaSistema)	? \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao())) : 0;
$taxaBol				= ($indValorExtra)		? \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorBoleto($oConta->getCodigo())) : 0;

#################################################################################
## Gerar as mensalidades
#################################################################################
$aParcGer		= array();
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Valor total do sistema, referente a todo período de uso, com base na data de 
	## ativação do usuário
	#################################################################################
	$dataAtivacao		= $aDataAtivacao[$i];
	$interval			= $dataConclusao->diff($dataAtivacao);
	$numMesesUso		= (($interval->format('%y') * 12) + $interval->format('%m'));
	$valTotalSistema	= round($numMesesUso * $taxaUso,2);

	#################################################################################
	## Calcular os valores das taxas da parcela
	#################################################################################
	$codFormaPag		= $aFormaPag[$i]->getCodigo();
	$valParcTaxaAdmin	= $taxaAdmin;
	$valParcTaxaBol		= ($codFormaPag == "BOL") ? $taxaAdmin : 0;
	

	#################################################################################
	## Calcular o valor da mensalidade 
	#################################################################################
	$valMensalidade		= $valTotalFormando - $valTotalSistema;
	$pctLiqSistema		= round(($valTotalSistema * 100) / $valTotalFormando,2);
	$pctLiqMensalidade	= 100 - $pctLiqSistema;
	
	#################################################################################
	## Criar o array de valores de rateio
	#################################################################################
	$pctRateio			= array();
	$valorRateio		= array();
	$codCategoria		= array();
	$codCentroCusto		= array();
	$codRateio			= array();
	
	#################################################################################
	## Resgatar as parcelas do contrato
	#################################################################################
	$valorTotal			= 0;
	$parcelas			= $em->getRepository('Entidades\ZgfmtContratoFormandoParcela')->findBy(array('codContrato' => $aContrato[$i]->getCodigo()));
	$numParcelas		= sizeof($parcelas);
	$ultimaParcela		= ($numParcelas - 1);
	$valParcSisAcu		= 0;
	$valParcMenAcu		= 0;
	$aOutrosValores		= array();
	for ($p = 0; $p < sizeof($parcelas); $p++) {
		
		$valLiqParcela			= \Zage\App\Util::to_float($parcelas[$p]->getValor());
		
		#################################################################################
		## Calcular os valores referente a sistema e mensalidade em cada parcela
		## O Calculo será feito de acordo com o percentual que o sistema tem no valor 
		## da Parcela
		#################################################################################
		if ($p	== $ultimaParcela) {
			$valParcSistema			= ($valTotalSistema - $valParcSisAcu);
			$valParcMensalidade		= ($valMensalidade - $valParcMenAcu);
		}else{
			$valParcSistema			= round($valLiqParcela * $pctLiqSistema / 100,2);
			$valParcMensalidade		= $valLiqParcela - $valParcSistema;
			$valParcSisAcu			+= $valParcSistema;
			$valParcMenAcu			+= $valParcMensalidade;
		}
		
		#################################################################################
		## Calcular o valor total da parcela
		#################################################################################
		$valParcTaxas			= $valParcTaxaAdmin + $valParcTaxaBol;
		$valorParcela			= $valParcMensalidade + $valParcSistema + $valParcTaxas; 
		$valorTotal				+= $valorParcela; 

		#################################################################################
		## Montar o array de outrosValores
		#################################################################################
		$valorOutros			= round(\Zage\App\Util::to_float($valParcSistema + $valParcTaxas),2); 
		$aOutrosValores[]		=  $valorOutros;

		$_pctRateio			= array();
		$_valorRateio		= array();
		$_codCategoria		= array();
		$_codCentroCusto	= array();
		$_codRateio			= array();
		
		$_pctRateio[]		= round(100*$valParcMensalidade/$valorParcela,2);
		$_valorRateio[]		= $valParcMensalidade;
		$_codCategoria[]	= $codCatMensalidade;
		$_codCentroCusto[]	= null;
		$_codRateio[]		= null;
		
		if ($valParcTaxaAdmin > 0)	{
			$_pctRateio[]		= round(100*$valParcTaxaAdmin/$valorParcela,2);
			$_valorRateio[]		= $valParcTaxaAdmin;
			$_codCategoria[]	= $codCatOutrasTaxas;
			$_codCentroCusto[]	= null;
			$_codRateio[]		= null;
		}
		
		if ($valParcTaxaBol > 0)		{
			$_pctRateio[]		= round(100*$valParcTaxaBol/$valorParcela,2);
			$_valorRateio[]		= $valParcTaxaBol;
			$_codCategoria[]	= $codCatBoleto;
			$_codCentroCusto[]	= null;
			$_codRateio[]		= null;
		}
		
		if ($valParcSistema	> 0)		{
			$_pctRateio[]		= round(100*$valParcSistema/$valorParcela,2);
			$_valorRateio[]		= $valParcSistema;
			$_codCategoria[]	= $codCatPortal;
			$_codCentroCusto[]	= null;
			$_codRateio[]		= null;
		}
		
		$pctRateio[$p]			= $_pctRateio;
		$valorRateio[$p]		= $_valorRateio;
		$codCategoria[$p]		= $_codCategoria;
		$codCentroCusto[$p]		= $_codCentroCusto;
		$codRateio[$p]			= $_codRateio;
		
		
	}
	
	
	#################################################################################
	## Ajustar as variáveis Fixas
	#################################################################################
	$codTipoRec		= ($numParcelas == 1)  ? "U" : "P";
	$parcela		= ($numParcelas == 1)  ? 1 : null;
	$codRecPer		= "M";
	$valorJuros		= 0;
	$valorMora		= 0;
	$valorDesconto	= 0;
	$parcelaInicial	= 1;
	$obs			= null;
	
	#################################################################################
	## Ajustar os campos do tipo CheckBox
	#################################################################################
	$flagRecebida		= 0;
	$flagReceberAuto	= 0;
	$flagAlterarSeq		= 0;
	
	
	#################################################################################
	## Montar o array JSON de retorno
	#################################################################################
	$aParcGer[$formandos[$i]->getCodigo()]["NOME"]			= $formandos[$i]->getNome();
	$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS"]	= $numParcelas;
	$aParcGer[$formandos[$i]->getCodigo()]["VALOR_TOTAL"]	= $valorTotal;
	$aParcGer[$formandos[$i]->getCodigo()]["FORMA_PAG"]		= $aContrato[$i]->getCodFormaPagamento()->getDescricao();
	
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa	= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$formandos[$i]->getCodigo());
	if (!$oPessoa) {
		$erro	= "Não foi possível encontrar a Pessoa referente ao Formando: ".$formandos[$i]->getCodigo();
		$log->err($erro);
		$em->getConnection()->rollback();
		$em->clear();
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x871FF, Pessoa não encontrada')));
	}
	
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrg		= $em->getReference('Entidades\ZgadmOrganizacao'				,$system->getCodOrganizacao());
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
	$conta->setCodFormaPagamento($aFormaPag[$i]);
	$conta->setCodStatus($oStatus);
	$conta->setCodMoeda($oMoeda);
	$conta->setCodPessoa($oPessoa);
	$conta->setNumero(null);
	$conta->setDescricao($descricao);
	$conta->setValor($valorTotal);
	$conta->setValorJuros($valorJuros);
	$conta->setValorMora($valorMora);
	$conta->setValorDesconto($valorDesconto);
	$conta->setValorOutros($valorOutros);
	
	######### parei aqui
	
	
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

echo '0'.\Zage\App\Util::encodeUrl('||'.json_encode($aParcGer));
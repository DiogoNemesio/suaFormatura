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
global $system,$em,$tr,$log;


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
if (isset($_POST['dataBase']))			$dataBase			= \Zage\App\Util::antiInjection($_POST['dataBase']);
if (isset($_POST['codContaRec']))		$codContaRec		= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['indValorExtra']))		{
	$indValorExtra			= \Zage\App\Util::antiInjection($_POST['indValorExtra']);
}else{
	$indValorExtra			= null;
}

#################################################################################
## Validar a dataBase
#################################################################################
if (!$dataBase)				die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Data base deve ser informada'))));
if (\Zage\App\Util::validaData($dataBase, $system->config["data"]["dateFormat"]) == false) die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Data base inválida'))));

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
	$formandos				= $em->getRepository('Entidades\ZgsegUsuario')->findBy(array('codigo' => $aSelFormandos),array('nome' => 'ASC'));
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
$totalPorFormando		= round($valorOrcado / $qtdFormandosBase,2);

#################################################################################
## Calcular o valor já provisionado por formando
#################################################################################
$oValorProv				= \Zage\Fmt\Financeiro::getValorProvisionadoPorFormando($system->getCodOrganizacao());

#################################################################################
## Montar o array para facilitar a impressão no grid dos valores provisionados
#################################################################################
$aValorProv				= array();
for ($i = 0; $i < sizeof($oValorProv); $i++) {
	$total														= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]) + \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aValorProv[$oValorProv[$i][0]->getCgc()]["MENSALIDADE"]	= \Zage\App\Util::to_float($oValorProv[$i]["mensalidade"]);
	$aValorProv[$oValorProv[$i][0]->getCgc()]["SISTEMA"]		= \Zage\App\Util::to_float($oValorProv[$i]["sistema"]);
	$aValorProv[$oValorProv[$i][0]->getCgc()]["TOTAL"]			= $total;
}

$log->info("Array de valores provisionados: ".serialize($aValorProv));

#################################################################################
## Montar o array de retorno de parcelas geradas
#################################################################################
$aContrato		= array();
$aDataAtivacao	= array();
$aFormaPag		= array();
$aPessoa		= array();
$aSaldo			= array();
for ($i = 0; $i < sizeof($formandos); $i++) {

	#################################################################################
	## Verificar se esse usuário é formando na organização atual
	#################################################################################
	if (\Zage\Seg\Usuario::ehFormando($system->getCodOrganizacao(), $formandos[$i]->getCodigo()) != true) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3749FF')));
	}
	
	#################################################################################
	## Resgata o registro da Pessoa associada ao Formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$formandos[$i]->getCodigo());
	if (!$oPessoa) 		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x871FB, Pessoa não encontrada')));
	$aPessoa[$i]		= $oPessoa;
	
	#################################################################################
	## Verificar se já foi gerada alguma mensalidade para algum formando
	#################################################################################
	$temMensalidade				= \Zage\Fmt\Financeiro::temMensalidadeGerada($system->getCodOrganizacao(), $oPessoa->getCodigo());
	if (!$temMensalidade)		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3749FE')));

	#################################################################################
	## Resgatar as informações do contrato
	#################################################################################
	$aContrato[$i]				= $em->getRepository('Entidades\ZgfmtContratoFormando')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codFormando' => $formandos[$i]->getCodigo()));
	if (!$aContrato[$i])		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3749FD')));
	
	#################################################################################
	## Resgatar as informações de forma de pagamento do contrato
	#################################################################################
	$oFormaPag					= ($aContrato[$i]->getCodFormaPagamento()) ? $aContrato[$i]->getCodFormaPagamento() : null;
	if (!$oFormaPag)			die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3749FC')));
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
	if (!$dataAtivacao)	die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Violação de acesso, 0x3749FB, data de ativação inválida')));
	$aDataAtivacao[$i]	= $dataAtivacao; 
	
	#################################################################################
	## Saldo a gerar
	#################################################################################
	$valProvisionado			= (isset($aValorProv[$formandos[$i]->getCpf()]["TOTAL"])) ? $aValorProv[$formandos[$i]->getCpf()]["TOTAL"] : 0;
	$saldo						= round($totalPorFormando - $valProvisionado,2);
	$aSaldo[$i]					= $saldo;
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
$taxaAdmin				= ($indValorExtra)		? \Zage\App\Util::to_float($oOrgFmt->getTaxaAdministracao())		: 0;
$taxaUso				= ($indRepTaxaSistema)	? \Zage\App\Util::to_float(\Zage\Adm\Contrato::getValorLicenca($system->getCodOrganizacao())) : 0;
$taxaBol				= ($indValorExtra)		? \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorBoleto($oConta->getCodigo())) : 0;
if (!$taxaAdmin)		$taxaAdmin	= 0;
if (!$taxaUso)			$taxaUso	= 0;
if (!$taxaBol)			$taxaBol	= 0;

#################################################################################
## Iniciar a transação no banco
#################################################################################
$em->getConnection()->beginTransaction();

#################################################################################
## Atualizar / Gerar as Parcelas
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
	## Calcular se falta provisionar algo de sistema, pois o prazo da formatura
	## pode ter sido extendido
	#################################################################################
	$saldoSistema		= round($valTotalSistema - $aValorProv[$formandos[$i]->getCpf()]["SISTEMA"],2);
	
	#################################################################################
	## Calcular o saldo de mensalidade, será o saldo a provisionar - o saldo de sistema
	#################################################################################
	$saldoMensalidade	= round($aSaldo[$i]		- $saldoSistema,2);
	
	if (($saldoMensalidade < 0) || ($saldoSistema < 0))  {
		$log->err("0x9FF432: ValTotalSistema: $valTotalSistema, SaldoSistema: $saldoSistema, SaldoMensalidade: $saldoMensalidade, Saldo: ".$aSaldo[$i]);
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities('Erro ao calcular o saldo devido, entre em contato com o suporte,e informe o erro: 0x9FF432')));
	}
	
	
	#################################################################################
	## Calcular os valores das taxas da parcela
	#################################################################################
	$codFormaPag		= $aFormaPag[$i]->getCodigo();
	$valParcTaxaAdmin	= $taxaAdmin;
	$valParcTaxaBol		= ($codFormaPag == "BOL") ? $taxaBol : 0;

	#################################################################################
	## Resgatar as parcelas em aberto
	#################################################################################
	$valorTotal			= 0;
	$parcelas			= \Zage\Fmt\Financeiro::getMensalidadesEmAberto($system->getCodOrganizacao(),$aPessoa[$i]->getCodigo(),$dataBase);
	$numParcelas		= sizeof($parcelas);
	$ultimaParcela		= ($numParcelas - 1);
	$valParcSisAcu		= 0;
	$valParcMenAcu		= 0;
	
	#################################################################################
	## Caso existam parcelas, distribuir o saldo nas parcelas, caso contrário
	## criar uma conta extra com o saldo 
	#################################################################################
	if ($numParcelas > 0) {
		for ($p = 0; $p < sizeof($parcelas); $p++) {
			
			
			#################################################################################
			## Resgata o valor de cada categoria que está na conta
			#################################################################################
			$valAtualSistema			= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorSistemaConta($parcelas[$p]->getCodigo()));	
			$valAtualMensalidade		= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorMensalidadeConta($parcelas[$p]->getCodigo()));
			$valParcTaxaAdmin			= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorTaxaAdmConta($parcelas[$p]->getCodigo()));
			$valParcTaxaBol				= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::getValorBoletoConta($parcelas[$p]->getCodigo()));

			$log->info("Parcela: $p, AtualSistema: $valAtualSistema, AtualMensalidade: $valAtualMensalidade, TxAdm: $valParcTaxaAdmin, TxBol: $valParcTaxaBol");
			
			#################################################################################
			## Calcular os valores referente a sistema e mensalidade em cada parcela
			## distribuir os valores por igual nas parcelas, deixando a diferença na última parcela 
			#################################################################################
			if ($p	== $ultimaParcela) {
				$valParcSistema			= $valAtualSistema		+ ($saldoSistema 		- $valParcSisAcu);
				$valParcMensalidade		= $valAtualMensalidade	+ ($saldoMensalidade	- $valParcMenAcu);
			}else{
				$_valParcSistema		= round($saldoSistema 		/ $numParcelas,2);
				$_valParcMensalidade	= round($saldoMensalidade 	/ $numParcelas,2);
				$valParcSistema			= $valAtualSistema		+ $_valParcSistema;
				$valParcMensalidade		= $valAtualMensalidade 	+ $_valParcMensalidade;
				$valParcSisAcu			+= $_valParcSistema;
				$valParcMenAcu			+= $_valParcMensalidade;
			}

			$log->info("Parcela: $p, Acumulado de Sistema: $valParcSisAcu, Acumulado de Mensalidade: $valParcMenAcu");
				
			
			#################################################################################
			## Inicialização dos arrays
			#################################################################################
			$aValor				= array();
			$aData				= array();
			$aOutrosValores		= array();
				
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

			$log->info("Parcela: $p ValorParcela: $valorParcela, ValorTotal: $valorTotal, OutrosValores:  $valorOutros");
			
			$_pctRateio				= array();
			$_valorRateio			= array();
			$_codCategoria			= array();
			$_codCentroCusto		= array();
			$_codRateio				= array();
			
			$_pctRateio[]			= round(100*$valParcMensalidade/$valorParcela,2);
			$_valorRateio[]			= $valParcMensalidade;
			$_codCategoria[]		= $codCatMensalidade;
			$_codCentroCusto[]		= null;
			$_codRateio[]			= null;
			
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
	
			#################################################################################
			## Criar o array de valores e vencimentos
			#################################################################################
			$aValor[0]				= $valParcMensalidade;
			$aData[0]				= $parcelas[$p]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
			
			#################################################################################
			## Ajustar as variáveis Fixas
			#################################################################################
			$codTipoRec		= $parcelas[$p]->getCodTipoRecorrencia()->getCodigo();
			$parcela		= $parcelas[$p]->getParcela();
			$codRecPer		= $parcelas[$p]->getCodPeriodoRecorrencia()->getCodigo();
			$valorJuros		= \Zage\App\Util::to_float($parcelas[$p]->getValorJuros());
			$valorMora		= \Zage\App\Util::to_float($parcelas[$p]->getValorMora());
			$valorDesconto	= \Zage\App\Util::to_float($parcelas[$p]->getValorDesconto());
			$parcelaInicial	= $parcelas[$p]->getParcelaInicial();
			$obs			= $parcelas[$p]->getObservacao();
			$intervaloRec	= $parcelas[$p]->getIntervaloRecorrencia();
				
			#################################################################################
			## Ajustar os campos do tipo CheckBox
			#################################################################################
			$flagRecebida		= 0;
			$flagReceberAuto	= $parcelas[$p]->getIndReceberAuto();
			$flagAlterarSeq		= 0;

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
			$descricao		= $parcelas[$p]->getDescricao();
			
			#################################################################################
			## Escrever os valores no objeto
			#################################################################################
			$conta->_setCodConta($parcelas[$p]->getCodigo());
			$conta->setCodOrganizacao($oOrg);
			$conta->setCodFormaPagamento($aFormaPag[$i]);
			$conta->setCodStatus($oStatus);
			$conta->setCodMoeda($oMoeda);
			$conta->setCodPessoa($aPessoa[$i]);
			$conta->setNumero(null);
			$conta->setDescricao($descricao);
			$conta->setValor($valParcMensalidade);
			$conta->setValorJuros($valorJuros);
			$conta->setValorMora($valorMora);
			$conta->setValorDesconto($valorDesconto);
			//$conta->setValorOutros($valorOutros);
			$conta->setDataVencimento($aData[0]);
			$conta->setDocumento(null);
			$conta->setObservacao($obs);
			$conta->setNumParcelas($numParcelas);
			$conta->setParcelaInicial($parcelaInicial);
			$conta->setParcela($parcela);
			$conta->setCodPeriodoRecorrencia($oPeriodo);
			$conta->setCodTipoRecorrencia($oTipoRec);
			$conta->setIntervaloRecorrencia($intervaloRec);
			$conta->setCodConta($oContaRec);
			$conta->setIndReceberAuto($flagReceberAuto);
			$conta->_setflagRecebida($flagRecebida);
			$conta->_setIndValorParcela(0);
			$conta->_setIndAlterarSeq($flagAlterarSeq);
			$conta->_setValorTotal($valorParcela);
			
			$conta->_setArrayValores($aValor);
			$conta->_setArrayOutrosValores($aOutrosValores);
			$conta->_setArrayDatas($aData);
			$conta->_setArrayCodigosRateio($_codRateio);
			$conta->_setArrayCategoriasRateio($_codCategoria);
			$conta->_setArrayCentroCustoRateio($_codCentroCusto);
			$conta->_setArrayValoresRateio($_valorRateio);
			$conta->_setArrayPctRateio($_pctRateio);
			
			#################################################################################
			## Coloca na fila do doctrine
			#################################################################################
			try {
				$erro	= $conta->salva();
			
				if ($erro) {
					$log->err("Erro ao salvar: ".$erro);
					$em->getConnection()->rollback();
					$em->clear();
					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
					exit;
				}
			
			
			} catch (\Exception $e) {
				$log->err("Erro: ".$e->getMessage());
				$em->getConnection()->rollback();
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
				
			
			
		}
		
		#################################################################################
		## Montar o array JSON de retorno
		#################################################################################
		$aParcGer[$formandos[$i]->getCodigo()]["NOME"]				= $formandos[$i]->getNome();
		$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS_GER"]	= 0;
		$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS_ATU"]	= $numParcelas;
		$aParcGer[$formandos[$i]->getCodigo()]["VALOR_TOTAL"]		= $valorTotal;
		$aParcGer[$formandos[$i]->getCodigo()]["FORMA_PAG"]			= $aContrato[$i]->getCodFormaPagamento()->getDescricao();
		
	}else{
		

		#################################################################################
		## Calcular os valores referente a sistema e mensalidade 
		#################################################################################
		$valParcSistema			= $saldoSistema;
		$valParcMensalidade		= $saldoMensalidade;

		#################################################################################
		## Calcular o valor total da parcela
		#################################################################################
		$valParcTaxaAdmin		= $taxaAdmin;
		$valParcTaxaBol			= ($codFormaPag == "BOL") ? $taxaBol : 0;
		$valParcTaxas			= $valParcTaxaAdmin + $valParcTaxaBol;
		$valorParcela			= $valParcMensalidade + $valParcSistema + $valParcTaxas;
		$valorTotal				= $valorParcela;
		
		#################################################################################
		## Inicialização dos arrays
		#################################################################################
		$aValor					= array();
		$aData					= array();
		$aOutrosValores			= array();
		
		#################################################################################
		## Montar o array de outrosValores
		#################################################################################
		$valorOutros			= round(\Zage\App\Util::to_float($valParcSistema + $valParcTaxas),2);
		$aOutrosValores[]		=  $valorOutros;
			
		$_pctRateio				= array();
		$_valorRateio			= array();
		$_codCategoria			= array();
		$_codCentroCusto		= array();
		$_codRateio				= array();
			
		$_pctRateio[]			= round(100*$valParcMensalidade/$valorParcela,2);
		$_valorRateio[]			= $valParcMensalidade;
		$_codCategoria[]		= $codCatMensalidade;
		$_codCentroCusto[]		= null;
		$_codRateio[]			= null;
			
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
		
		#################################################################################
		## Criar o array de valores e vencimentos
		#################################################################################
		$aValor[0]				= $valParcMensalidade;
		$aData[0]				= $dataBase;
			
		#################################################################################
		## Ajustar as variáveis Fixas
		#################################################################################
		$codTipoRec				= "U";
		$parcela				= 1;
		$codRecPer				= "M";
		$valorJuros				= 0;
		$valorMora				= 0;
		$valorDesconto			= 0;
		$parcelaInicial			= 1;
		$obs					= "Atualização de orçamento";
		$intervaloRec			= null;
		
		#################################################################################
		## Ajustar os campos do tipo CheckBox
		#################################################################################
		$flagRecebida			= 0;
		$flagReceberAuto		= 0;
		$flagAlterarSeq			= 0;
		
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
		$descricao		= "Mensalidade extra";
			
		#################################################################################
		## Escrever os valores no objeto
		#################################################################################
		$conta->setCodOrganizacao($oOrg);
		$conta->setCodFormaPagamento($aFormaPag[$i]);
		$conta->setCodStatus($oStatus);
		$conta->setCodMoeda($oMoeda);
		$conta->setCodPessoa($aPessoa[$i]);
		$conta->setNumero(null);
		$conta->setDescricao($descricao);
		$conta->setValor($valorTotal);
		$conta->setValorJuros($valorJuros);
		$conta->setValorMora($valorMora);
		$conta->setValorDesconto($valorDesconto);
		//$conta->setValorOutros($valorOutros);
		$conta->setDataVencimento($aData[0]);
		$conta->setDocumento(null);
		$conta->setObservacao($obs);
		$conta->setNumParcelas($numParcelas);
		$conta->setParcelaInicial($parcelaInicial);
		$conta->setParcela($parcela);
		$conta->setCodPeriodoRecorrencia($oPeriodo);
		$conta->setCodTipoRecorrencia($oTipoRec);
		$conta->setIntervaloRecorrencia($intervaloRec);
		$conta->setCodConta($oContaRec);
		$conta->setIndReceberAuto($flagReceberAuto);
		$conta->_setflagRecebida($flagRecebida);
		$conta->_setIndValorParcela(0);
		$conta->_setIndAlterarSeq($flagAlterarSeq);
		$conta->_setValorTotal($valorTotal);
			
		$conta->_setArrayValores($aValor);
		$conta->_setArrayOutrosValores($aOutrosValores);
		$conta->_setArrayDatas($aData);
		$conta->_setArrayCodigosRateio($_codRateio);
		$conta->_setArrayCategoriasRateio($_codCategoria);
		$conta->_setArrayCentroCustoRateio($_codCentroCusto);
		$conta->_setArrayValoresRateio($_valorRateio);
		$conta->_setArrayPctRateio($_pctRateio);
			
		#################################################################################
		## Coloca na fila do doctrine
		#################################################################################
		try {
			$erro	= $conta->salva();
				
			if ($erro) {
				$log->err("Erro ao salvar: ".$erro);
				$em->getConnection()->rollback();
				$em->clear();
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
				exit;
			}
				
				
		} catch (\Exception $e) {
			$log->err("Erro: ".$e->getMessage());
			$em->getConnection()->rollback();
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
			exit;
		}
		
		#################################################################################
		## Montar o array JSON de retorno
		#################################################################################
		$aParcGer[$formandos[$i]->getCodigo()]["NOME"]				= $formandos[$i]->getNome();
		$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS_GER"]	= 1;
		$aParcGer[$formandos[$i]->getCodigo()]["NUM_PARCELAS_ATU"]	= 0;
		$aParcGer[$formandos[$i]->getCodigo()]["VALOR_TOTAL"]		= $valorTotal;
		$aParcGer[$formandos[$i]->getCodigo()]["FORMA_PAG"]			= $aContrato[$i]->getCodFormaPagamento()->getDescricao();
		
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
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.json_encode($aParcGer));
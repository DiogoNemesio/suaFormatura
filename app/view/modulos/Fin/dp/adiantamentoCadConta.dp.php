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
if (isset($_POST['id']))				$id					= \Zage\App\Util::antiInjection($_POST['id']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['dataVenc']))			$dataVenc			= \Zage\App\Util::antiInjection($_POST['dataVenc']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['obs']))				$obs				= \Zage\App\Util::antiInjection($_POST['obs']);
if (isset($_POST['codContaPag']))		$codContaPag		= \Zage\App\Util::antiInjection($_POST['codContaPag']);

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
if (!isset($codPessoa)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de Parâmetros 2"));
	die ('1'.\Zage\App\Util::encodeUrl('||'));
}

#################################################################################
## Resgata os dados da Pessoa
#################################################################################
$oPessoa		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa));
if (!$oPessoa)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Pessoa não encontrada"));
	die ('1'.\Zage\App\Util::encodeUrl('||'));
}

#################################################################################
## Resgata o saldo da pessoa em questão
#################################################################################
$saldo			= \Zage\Fin\Adiantamento::getSaldo($system->getCodOrganizacao(), $codPessoa);

#################################################################################
## Validar o valor
#################################################################################
if (!$valor)		{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Valor deve ser informado"));
	die ('1'.\Zage\App\Util::encodeUrl('||'));
}elseif ($valor > $saldo)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Saldo insuficiente para realizar a operação"));
	die ('1'.\Zage\App\Util::encodeUrl('||'));
}else{
	$valor		= \Zage\App\Util::to_float($valor);
}

#################################################################################
## Criar o objeto do contas a pagar
#################################################################################
$conta		= new \Zage\Fin\ContaPagar();

#################################################################################
## Resgatar a categora de outras devoluções
#################################################################################
$codCatDev		= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_OUTRAS");

#################################################################################
## Criar os arrays
#################################################################################
$codCategoria		= array($codCatDev);
$codRateio			= array(null);
$codCentroCusto		= array(null);
$valorRateio		= array($valor);
$pctRateio			= array(100);
$aValor				= array($valor);
$aData				= array($dataVenc);

#################################################################################
## Ajustar os valores
#################################################################################
$valorDesconto		= 0;
$valorJuros			= 0;
$valorMora			= 0;
$valorOutros		= 0;
$valorTotal			= $valor;

#################################################################################
## Resgata os objetos (chave estrangeiras)
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
$oStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => "A"));
$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => "M"));
$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => "U"));
$oContaPag	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaPag));

#################################################################################
## Validação de rateio
#################################################################################
if (!is_array($codCategoria)) {
	$erro = $tr->trans('"Categoria" deve ser um array');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codRateio)) {
	$erro = $tr->trans('"Código dos rateios" deve ser um array');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codCentroCusto)) {
	$erro = $tr->trans('"Centro de Custo" deve ser um array');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codCentroCusto) != sizeof($codCategoria)) {
	$erro = $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Categorias');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($valorRateio)) {
	$erro = $tr->trans('"Valor de Rateio" deve ser um array');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($pctRateio)) {
	$erro = $tr->trans('"Percentual de Rateio" deve ser um array');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($pctRateio)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Percentuais');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Centro de Custo');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Códigos de Rateio');
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}


#################################################################################
## Configurações do adiantamento
#################################################################################
$codTipoBaixa		= "ADI";
$usarAdiantamento	= 1;

#################################################################################
## Ajustar os campos do tipo CheckBox
#################################################################################
$flagPaga		= 0;
$flagPagarAuto	= 0;
$flagAlterarSeq	= 0;

#################################################################################
## Ajustar as variáveis que não são fixas na tela (Podem não existir)
#################################################################################
$indValorParcela	= null;

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
$conta->setNumParcelas(1);
$conta->setParcelaInicial(1);
$conta->setParcela(1);
$conta->setCodPeriodoRecorrencia($oPeriodo);
$conta->setCodTipoRecorrencia($oTipoRec);
$conta->setIntervaloRecorrencia(1);
$conta->setCodConta($oContaPag);
$conta->setIndPagarAuto($flagPagarAuto);
$conta->_setFlagPaga($flagPaga);
$conta->_setIndValorParcela($indValorParcela);
$conta->_setValorTotal($valorTotal);
$conta->setCodGrupoConta($grupoMov);
$conta->_setFlagPaga(0);

$conta->_setArrayValores($aValor);
$conta->_setArrayDatas($aData);
$conta->_setArrayCodigosRateio($codRateio);
$conta->_setArrayCategoriasRateio($codCategoria);
$conta->_setArrayCentroCustoRateio($codCentroCusto);
$conta->_setArrayValoresRateio($valorRateio);
$conta->_setArrayPctRateio($pctRateio);
$conta->_setIndAlterarSeq($flagAlterarSeq);

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	$erro	= $conta->salva();
	
	if ($erro) {
		$log->err("Erro ao salvar conta de devolução: ".$erro);
		$em->getConnection()->rollback();
		$em->clear();
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
		exit;
	}else{

		#################################################################################
		## Liquidar a conta por adiantamento
		#################################################################################
		$oContaPag		= $conta->_getObject();
		$erro 			= $conta->paga($oContaPag, $codContaPag, $codFormaPag, $dataVenc, $valor, $valorJuros, $valorMora, $valorDesconto, $valorOutros, null, $codTipoBaixa,null,$usarAdiantamento);
		
		if ($erro)	{
			$log->err("Erro ao salvar a baixa da devolução: ".$erro);
			$em->getConnection()->rollback();
			$em->clear();
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
				
		}else{
			$em->flush();
			$em->clear();
			$em->getConnection()->commit();
		}
		
	}
	
	
} catch (\Exception $e) {
	$log->err("Erro: ".$e->getMessage());
	$em->getConnection()->rollback();
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$conta->_getCodigo().'|'.$conta->getNumero().'|'.$conta->getCodStatus()->getCodigo().'|'.$conta->getCodStatus()->getDescricao());
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
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['codPessoa']))			$codPessoa			= \Zage\App\Util::antiInjection($_POST['codPessoa']);
if (isset($_POST['numParcelas']))		$numParcelas		= \Zage\App\Util::antiInjection($_POST['numParcelas']);
if (isset($_POST['parcelaInicial']))	$parcelaInicial		= \Zage\App\Util::antiInjection($_POST['parcelaInicial']);
if (isset($_POST['intervaloRec']))		$intervaloRec		= \Zage\App\Util::antiInjection($_POST['intervaloRec']);
if (isset($_POST['codMoeda']))			$codMoeda			= \Zage\App\Util::antiInjection($_POST['codMoeda']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['valorJuros']))		$valorJuros			= \Zage\App\Util::antiInjection($_POST['valorJuros']);
if (isset($_POST['valorMora']))			$valorMora			= \Zage\App\Util::antiInjection($_POST['valorMora']);
if (isset($_POST['valorDesconto']))		$valorDesconto		= \Zage\App\Util::antiInjection($_POST['valorDesconto']);
if (isset($_POST['valorOutros']))		$valorOutros		= \Zage\App\Util::antiInjection($_POST['valorOutros']);
if (isset($_POST['dataVenc']))			$dataVenc			= \Zage\App\Util::antiInjection($_POST['dataVenc']);
if (isset($_POST['documento']))			$documento			= \Zage\App\Util::antiInjection($_POST['documento']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['obs']))				$obs				= \Zage\App\Util::antiInjection($_POST['obs']);
if (isset($_POST['codTipoRec']))		$codTipoRec			= \Zage\App\Util::antiInjection($_POST['codTipoRec']);
if (isset($_POST['codPeriodoRec']))		$codPeriodoRec		= \Zage\App\Util::antiInjection($_POST['codPeriodoRec']);
if (isset($_POST['codContaRec']))		$codContaRec		= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['flagReceberAuto']))	$flagReceberAuto	= \Zage\App\Util::antiInjection($_POST['flagReceberAuto']);
if (isset($_POST['flagRecebida']))			$flagRecebida			= \Zage\App\Util::antiInjection($_POST['flagRecebida']);
if (isset($_POST['flagAlterarSeq']))	$flagAlterarSeq		= \Zage\App\Util::antiInjection($_POST['flagAlterarSeq']);
if (isset($_POST['codTipoValor']))		$codTipoValor		= \Zage\App\Util::antiInjection($_POST['codTipoValor']);
if (isset($_POST['valorTotal']))		$valorTotal			= \Zage\App\Util::antiInjection($_POST['valorTotal']);

#################################################################################
## Resgata os arrays passados pelo formulario
#################################################################################
if (isset($_POST['codRateio']))			$codRateio			= $_POST['codRateio'];
if (isset($_POST['codCentroCusto']))	$codCentroCusto		= $_POST['codCentroCusto'];
if (isset($_POST['codCategoria']))		$codCategoria		= $_POST['codCategoria'];
if (isset($_POST['valorRateio']))		$valorRateio		= $_POST['valorRateio'];
if (isset($_POST['pctRateio']))			$pctRateio			= $_POST['pctRateio'];
if (isset($_POST['aData']))				$aData				= $_POST['aData'];
if (isset($_POST['aValor']))			$aValor				= $_POST['aValor'];

#################################################################################
## Criar o objeto do contas a pagar
#################################################################################
$conta		= new \Zage\Fin\ContaReceber();

#################################################################################
## Resgata os objetos (chave estrangeiras)
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
$oStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => "A"));
$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codPessoa));
$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => $codPeriodoRec));
$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => $codTipoRec));
$oContaRec	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));

#################################################################################
## Validação de rateio
#################################################################################
if (!is_array($codCategoria)) {
	$erro = $tr->trans('"Categoria" deve ser um array');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codRateio)) {
	$erro = $tr->trans('"Código dos rateios" deve ser um array');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codCentroCusto)) {
	$erro = $tr->trans('"Centro de Custo" deve ser um array');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codCentroCusto) != sizeof($codCategoria)) {
	$erro = $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Categorias');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($valorRateio)) {
	$erro = $tr->trans('"Valor de Rateio" deve ser um array');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($pctRateio)) {
	$erro = $tr->trans('"Percentual de Rateio" deve ser um array');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($pctRateio)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Percentuais');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Centro de Custo');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Códigos de Rateio');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!isset($codConta) || empty($codConta)) {
	$erro = $tr->trans('Parâmetro COD_CONTA deve ser informado !!!');
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
	
}


#################################################################################
## Ajustar os valores
#################################################################################
if (empty($valorDesconto))	$valorDesconto		= 0;
if (empty($valorJuros))		$valorJuros			= 0;
if (empty($valorMora))		$valorMora			= 0;
if (empty($valorOutros))	$valorOutros		= 0;
$valorTotal		= \Zage\App\Util::toPHPNumber($valorTotal);

#################################################################################
## Ajustar os campos do tipo CheckBox
#################################################################################
$flagRecebida		= (isset($flagRecebida)) 		? 1 : 0;
$flagReceberAuto	= (isset($flagReceberAuto)) 	? 1 : 0;
$flagAlterarSeq	= (isset($flagAlterarSeq)) 	? 1 : 0;

#################################################################################
## Ajustar as variáveis que não são fixas na tela (Podem não existir)
#################################################################################
if (isset($codTipoValor) && $codTipoValor == "P") {
	$indValorParcela	= 1;
}else{
	$indValorParcela	= null;
}


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
$conta->setDocumento($documento);
$conta->setObservacao($obs);
$conta->setNumParcelas($numParcelas);
$conta->setParcelaInicial($parcelaInicial);
$conta->setParcela(1);
$conta->setCodPeriodoRecorrencia($oPeriodo);
$conta->setCodTipoRecorrencia($oTipoRec);
$conta->setIntervaloRecorrencia($intervaloRec);
$conta->setCodConta($oContaRec);
$conta->setIndReceberAuto($flagReceberAuto);
$conta->_setFlagRecebida($flagRecebida);
$conta->_setIndValorParcela($indValorParcela);
$conta->_setValorTotal($valorTotal);

$conta->_setArrayValores($aValor);
$conta->_setArrayDatas($aData);
$conta->_setArrayCodigosRateio($codRateio);
$conta->_setArrayCategoriasRateio($codCategoria);
$conta->_setArrayCentroCustoRateio($codCentroCusto);
$conta->_setArrayValoresRateio($valorRateio);
$conta->_setArrayPctRateio($pctRateio);

$aSelContas		= explode(",",$codConta);
$conta->_setCodConta($aSelContas);
$conta->_setIndAlterarSeq(0);

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {
	
	$erro	= $conta->substitui();
	
	if ($erro) {
		$log->err("Erro ao salvar: ".$erro);
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
	
	
} catch (\Exception $e) {
	$log->err("Erro: ".$e->getMessage());
	$em->getConnection()->rollback();
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
if (isset($_edit) && (!empty($_edit))) $system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$conta->_getCodigo().'|'.$conta->getNumero().'|'.$conta->getCodStatus()->getCodigo().'|'.$conta->getCodStatus()->getDescricao());
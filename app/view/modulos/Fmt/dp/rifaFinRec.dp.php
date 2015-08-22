<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $em,$log,$system;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codUsuario']))		$codUsuario			= \Zage\App\Util::antiInjection($_POST['codUsuario']);


if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['numero']))			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);

if (isset($_POST['codStatus']))			$codStatus			= \Zage\App\Util::antiInjection($_POST['codStatus']);

if (isset($_POST['dataVenc']))			$dataVenc			= \Zage\App\Util::antiInjection($_POST['dataVenc']);
if (isset($_POST['dataAut']))			$dataAut			= \Zage\App\Util::antiInjection($_POST['dataAut']);
if (isset($_POST['indAut']))			$indAut				= \Zage\App\Util::antiInjection($_POST['indAut']);
if (isset($_POST['documento']))			$documento			= \Zage\App\Util::antiInjection($_POST['documento']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['obs']))				$obs				= \Zage\App\Util::antiInjection($_POST['obs']);
if (isset($_POST['codTipoRec']))		$codTipoRec			= \Zage\App\Util::antiInjection($_POST['codTipoRec']);
if (isset($_POST['codPeriodoRec']))		$codPeriodoRec		= \Zage\App\Util::antiInjection($_POST['codPeriodoRec']);
if (isset($_POST['codContaRec']))		$codContaRec		= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['flagReceberAuto']))	$flagReceberAuto	= \Zage\App\Util::antiInjection($_POST['flagReceberAuto']);
if (isset($_POST['flagRecebida']))		$flagRecebida		= \Zage\App\Util::antiInjection($_POST['flagRecebida']);
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
if (isset($_POST['aData']))				$codRateio			= $_POST['aData'];
if (isset($_POST['aValor']))			$aValor				= $_POST['aValor'];

$codRateio 			= array();
$codCentroCusto 	= array();
$codCategoria		= array();
$valorRateio		= array();
$pctRateio			= array();
$codRateio			= array();
$aValor				= array();

#################################################################################
## Criar o objeto do contas a Receber
#################################################################################
$conta		= new \Zage\Fin\ContaReceber();
$conta->_setCodConta($codConta);

#################################################################################
## Resgata os objetos (chave estrangeiras)
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
$oStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => "A"));
$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));

$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'cgc' => $oUsuario->getCpf()));

$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => $codPeriodoRec));
$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => $codTipoRec));
$oContaRec	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codContaRec));

$log->debug($codPessoa);
#################################################################################
## Validação de rateio
#################################################################################
if (!is_array($codCategoria)) {
	$erro = $tr->trans('"Categoria" deve ser um array');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codRateio)) {
	$erro = $tr->trans('"Código dos rateios" deve ser um array');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($codCentroCusto)) {
	$erro = $tr->trans('"Centro de Custo" deve ser um array');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codCentroCusto) != sizeof($codCategoria)) {
	$erro = $tr->trans('Quantidade de Centro de Custos difere da Quantidade de Categorias');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($valorRateio)) {
	$erro = $tr->trans('"Valor de Rateio" deve ser um array');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (!is_array($pctRateio)) {
	$erro = $tr->trans('"Percentual de Rateio" deve ser um array');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($pctRateio)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Percentuais');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($valorRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Centro de Custo');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

if (sizeof($codRateio) != sizeof($codCentroCusto)) {
	$erro = $tr->trans('Quantidade de Valores de Rateio difere da Quantidade de Códigos de Rateio');
	if (isset($_edit) && (!empty($_edit)))	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
	exit;
}

#################################################################################
## Ajustar os valores
#################################################################################
if (empty($valorDesconto))	$valorDesconto		= 0;
if (empty($valorJuros))		$valorJuros			= 0;
if (empty($valorMora))		$valorMora			= 0;
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
$conta->setcodOrganizacao($oOrg);
$conta->setCodFormaPagamento($oForma);
$conta->setCodStatus($oStatus);
$conta->setCodMoeda($oMoeda);
$conta->setCodPessoa($oPessoa);
$conta->setNumero($numero);
$conta->setDescricao($descricao);
$conta->setValor($valor);
$conta->setValorJuros($valorJuros);
$conta->setValorMora($valorMora);
$conta->setValorDesconto($valorDesconto);
$conta->setDataVencimento($dataVenc);
$conta->setDocumento($documento);
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
$conta->_setIndValorParcela($indValorParcela);
$conta->_setValorTotal($valorTotal);

$conta->_setArrayValores($aValor);
$conta->_setArrayDatas($aData);
$conta->_setArrayCodigosRateio($codRateio);
$conta->_setArrayCategoriasRateio($codCategoria);
$conta->_setArrayCentroCustoRateio($codCentroCusto);
$conta->_setArrayValoresRateio($valorRateio);
$conta->_setArrayPctRateio($pctRateio);

if (!empty($codConta)) {
	$conta->_setCodConta($codConta);
	$conta->_setIndAlterarSeq($flagAlterarSeq);
}else{
	$conta->_setIndAlterarSeq(0);
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {
	
	$erro	= $conta->salva();
	
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
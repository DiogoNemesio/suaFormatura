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

//$log->debug("POST Transf:".serialize($_POST));

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['_edit']))				$_edit				= \Zage\App\Util::antiInjection($_POST['_edit']);
if (isset($_POST['codTransf']))			$codTransf			= \Zage\App\Util::antiInjection($_POST['codTransf']);
if (isset($_POST['numero']))			$numero				= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['parcela']))			$parcela			= \Zage\App\Util::antiInjection($_POST['parcela']);
if (isset($_POST['codStatus']))			$codStatus			= \Zage\App\Util::antiInjection($_POST['codStatus']);
if (isset($_POST['numParcelas']))		$numParcelas		= \Zage\App\Util::antiInjection($_POST['numParcelas']);
if (isset($_POST['parcelaInicial']))	$parcelaInicial		= \Zage\App\Util::antiInjection($_POST['parcelaInicial']);
if (isset($_POST['intervaloRec']))		$intervaloRec		= \Zage\App\Util::antiInjection($_POST['intervaloRec']);
if (isset($_POST['codMoeda']))			$codMoeda			= \Zage\App\Util::antiInjection($_POST['codMoeda']);
if (isset($_POST['dataEmissao']))		$dataEmissao		= \Zage\App\Util::antiInjection($_POST['dataEmissao']);
if (isset($_POST['dataLiq']))			$dataLiq			= \Zage\App\Util::antiInjection($_POST['dataLiq']);
if (isset($_POST['dataAut']))			$dataAut			= \Zage\App\Util::antiInjection($_POST['dataAut']);
if (isset($_POST['indAut']))			$indAut				= \Zage\App\Util::antiInjection($_POST['indAut']);
if (isset($_POST['documento']))			$documento			= \Zage\App\Util::antiInjection($_POST['documento']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['obs']))				$obs				= \Zage\App\Util::antiInjection($_POST['obs']);
if (isset($_POST['codTipoRec']))		$codTipoRec			= \Zage\App\Util::antiInjection($_POST['codTipoRec']);
if (isset($_POST['codPeriodoRec']))		$codPeriodoRec		= \Zage\App\Util::antiInjection($_POST['codPeriodoRec']);
if (isset($_POST['codContaOrig']))		$codContaOrig		= \Zage\App\Util::antiInjection($_POST['codContaOrig']);
if (isset($_POST['codContaDest']))		$codContaDest		= \Zage\App\Util::antiInjection($_POST['codContaDest']);
if (isset($_POST['flagPagarAuto']))		$flagPagarAuto		= \Zage\App\Util::antiInjection($_POST['flagPagarAuto']);
if (isset($_POST['flagPaga']))			$flagPaga			= \Zage\App\Util::antiInjection($_POST['flagPaga']);
if (isset($_POST['flagAlterarSeq']))	$flagAlterarSeq		= \Zage\App\Util::antiInjection($_POST['flagAlterarSeq']);
if (isset($_POST['valorTotal']))		$valorTotal			= \Zage\App\Util::antiInjection($_POST['valorTotal']);

#################################################################################
## Resgata os arrays passados pelo formulario
#################################################################################
if (isset($_POST['aData']))		$aData		= $_POST['aData'];
if (isset($_POST['aValor']))	$aValor		= $_POST['aValor'];

#################################################################################
## Criar o objeto do contas a pagar
#################################################################################
$transf		= new \Zage\Fin\Transferencia();
$transf->_setCodTransferencia($codTransf);

#################################################################################
## Resgata os objetos (chave estrangeiras)
#################################################################################
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
$oStatus	= $em->getRepository('Entidades\ZgfinTransferenciaStatusTipo')->findOneBy(array('codigo' => "P"));
$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => $codPeriodoRec));
$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => $codTipoRec));
$oContaOrig	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codContaOrig));
$oContaDest	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codContaDest));

#################################################################################
## Ajustar os valores
#################################################################################
$valorTotal		= \Zage\App\Util::toPHPNumber($valorTotal);

#################################################################################
## Ajustar os campos do tipo CheckBox
#################################################################################
$flagPaga		= (isset($flagPaga)) 		? 1 : 0;
$flagPagarAuto	= (isset($flagPagarAuto)) 	? 1 : 0;
$flagAlterarSeq	= (isset($flagAlterarSeq)) 	? 1 : 0;

#################################################################################
## Ajustar as variáveis que não são fixas na tela (Podem não existir)
#################################################################################

#################################################################################
## Escrever os valores no objeto
#################################################################################
$transf->setCodOrganizacao($oOrg);
$transf->setCodFormaPagamento($oForma);
$transf->setCodStatus($oStatus);
$transf->setCodMoeda($oMoeda);
$transf->setNumero($numero);
$transf->setDescricao($descricao);
$transf->setDocumento($documento);
$transf->setObservacao($obs);
$transf->setNumParcelas($numParcelas);
$transf->setParcelaInicial($parcelaInicial);
$transf->setParcela($parcela);
$transf->setCodPeriodoRecorrencia($oPeriodo);
$transf->setCodTipoRecorrencia($oTipoRec);
$transf->setIntervaloRecorrencia($intervaloRec);
$transf->setCodContaOrigem($oContaOrig);
$transf->setCodContaDestino($oContaDest);
$transf->setIndTransferirAuto($flagPagarAuto);
$transf->_setFlagRealizada($flagPaga);
$transf->_setValorTotal($valorTotal);

$transf->_setArrayValores($aValor);
$transf->_setArrayDatas($aData);

if (!empty($codTransf)) {
	$transf->_setCodTransferencia($codTransf);
	$transf->_setIndAlterarSeq($flagAlterarSeq);
}else{
	$transf->_setIndAlterarSeq(0);
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {
	
	$erro	= $transf->salva();
	
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
echo '0'.\Zage\App\Util::encodeUrl('|'.$transf->_getCodTransferencia().'|'.$transf->getNumero().'|'.$transf->getCodStatus()->getCodigo().'|'.$transf->getCodStatus()->getDescricao());
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
if (isset($_POST['codUsuario']))		$codUsuario			= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['codContaRec']))		$codContaRec		= \Zage\App\Util::antiInjection($_POST['codContaRec']);
if (isset($_POST['valorTotal']))		$valorTotal			= \Zage\App\Util::antiInjection($_POST['valorTotal']);
if (isset($_POST['valorRecebido']))		$valorRecebido		= \Zage\App\Util::antiInjection($_POST['valorRecebido']);
if (isset($_POST['codRifa']))			$codRifa			= \Zage\App\Util::antiInjection($_POST['codRifa']);
if (isset($_POST['qtdeVendida']))			$qtdeVendida			= \Zage\App\Util::antiInjection($_POST['qtdeVendida']);


#################################################################################
## Validar os parâmetros
#################################################################################
$oRifa			= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));


#################################################################################
## Definir os valores fixos
#################################################################################
$codGrpAssociacao	= "RIFA_".$oRifa->getCodigo(). "_".$oUsuario->getCodigo();
$dataVenc			= date($system->config["data"]["dateFormat"]); 
$qtdeVendida		= ($qtdeVendida < $oRifa->getQtdeObrigatorio()) ? $oRifa->getQtdeObrigatorio() : $qtdeVendida;
$valorTotal			= ($qtdeVendida * $oRifa->getValorUnitario());
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
$descricao			= 'Venda de ('.$qtdeVendida.') bilhetes da Rifa: "'.$oRifa->getNome().'"';
$indValorParcela	= null;
$indSomenteVis		= 1;


#################################################################################
## Verificar se a conta já foi gerada
#################################################################################
$oConta				= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codGrupoAssociacao' => $codGrpAssociacao));

if (!$oConta)		{


	#################################################################################
	## Resgatar os parâmetros da categoria
	#################################################################################
	$codCatRifa				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_RIFA");
	$codCentroCustoRifa		= ($oRifa->getCodCentroCusto()) ? $oRifa->getCodCentroCusto()->getCodigo() : null; 
	
	#################################################################################
	## Ajustar o array de valores de rateio
	#################################################################################
	$pctRateio		= array(100);
	$valorRateio	= array($valorTotal);
	$codCategoria	= array($codCatRifa);
	$codCentroCusto	= array($codCentroCustoRifa);
	$codRateio		= array("");
	$aValor			= array($valorTotal);
	$aData			= array($dataVenc);
	
	#################################################################################
	## Ajustar os campos do tipo CheckBox
	#################################################################################
	$flagRecebida		= 0;
	$flagReceberAuto	= 0;
	
	#################################################################################
	## Buscar a pessoa associada ao formando
	#################################################################################
	$oPessoa			= \Zage\Fin\Pessoa::getPessoaUsuario($system->getCodOrganizacao(),$codUsuario);
	
	#################################################################################
	## Criar o objeto do contas a Receber
	#################################################################################
	$conta		= new \Zage\Fin\ContaReceber();
	
	#################################################################################
	## Resgata os objetos (chave estrangeiras)
	#################################################################################
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getcodOrganizacao()));
	$oForma		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	$oStatus	= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => "A"));
	$oMoeda		= $em->getRepository('Entidades\ZgfinMoeda')->findOneBy(array('codigo' => 1));
	$oPeriodo	= $em->getRepository('Entidades\ZgfinContaRecorrenciaPeriodo')->findOneBy(array('codigo' => $codRecPer));
	$oTipoRec	= $em->getRepository('Entidades\ZgfinContaRecorrenciaTipo')->findOneBy(array('codigo' => $codTipoRec));
	$oContaRec	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codContaRec));
	
	#################################################################################
	## Ajustar os valores
	#################################################################################
	$valorTotal		= \Zage\App\Util::toPHPNumber($valorTotal);
	
	#################################################################################
	## Escrever os valores no objeto
	#################################################################################
	$conta->setCodOrganizacao($oOrg);
	$conta->setCodFormaPagamento($oForma);
	$conta->setCodStatus($oStatus);
	$conta->setCodMoeda($oMoeda);
	$conta->setCodPessoa($oPessoa);
	//$conta->setNumero($numero);
	$conta->setDescricao($descricao);
	$conta->setValor($valorTotal);
	$conta->setValorJuros($valorJuros);
	$conta->setValorMora($valorMora);
	$conta->setValorDesconto($valorDesconto);
	$conta->setValorOutros($valorOutros);
	$conta->setDataVencimento($dataVenc);
	$conta->setDocumento($oRifa->getCodigo());
	$conta->setObservacao($obs);
	$conta->setNumParcelas($numParcelas);
	$conta->setParcelaInicial($parcelaInicial);
	$conta->setParcela($parcela);
	$conta->setCodPeriodoRecorrencia($oPeriodo);
	$conta->setCodTipoRecorrencia($oTipoRec);
	//$conta->setIntervaloRecorrencia($intervaloRec);
	$conta->setCodConta($oContaRec);
	$conta->setIndReceberAuto($flagReceberAuto);
	$conta->_setflagRecebida($flagRecebida);
	$conta->_setIndValorParcela($indValorParcela);
	$conta->_setValorTotal($valorTotal);
	$conta->setCodGrupoAssociacao($codGrpAssociacao);
	$conta->setIndSomenteVisualizar($indSomenteVis);
	
	$conta->_setArrayValores($aValor);
	$conta->_setArrayDatas($aData);
	$conta->_setArrayCodigosRateio($codRateio);
	$conta->_setArrayCategoriasRateio($codCategoria);
	$conta->_setArrayCentroCustoRateio($codCentroCusto);
	$conta->_setArrayValoresRateio($valorRateio);
	$conta->_setArrayPctRateio($pctRateio);

	
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
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}

	
}

#################################################################################
## Salvar a quantidade de rifas vendidas
#################################################################################
$oRifaFormando		= $em->getRepository('Entidades\ZgfmtRifaFormando')->findOneBy(array('codRifa' => $codRifa, 'codFormando' => $codUsuario));
if (!$oRifaFormando) {
	
	$oRifa			= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
	
	$oRifaFormando	= new \Entidades\ZgfmtRifaFormando();
	$oRifaFormando->setCodRifa($oRifa);
	$oRifaFormando->setCodFormando($oUsuario);
	$oRifaFormando->setQtdeVendida($qtdeVendida);

	$em->persist($oRifaFormando);
}

#################################################################################
## Resgata a conta que será baixada
#################################################################################
$oConta				= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codGrupoAssociacao' => $codGrpAssociacao));

#################################################################################
## Faz a baixa do valor recebido
#################################################################################
$em->getConnection()->beginTransaction();
try {

	$conta		= new \Zage\Fin\ContaReceber();
	$erro		= $conta->recebe($oConta,$codContaRec,$codFormaPag,$dataVenc,$valorRecebido,$valorJuros,$valorMora,$valorDesconto,$valorOutros,null,"MAN",null);

	if ($erro != false) {
		$em->getConnection()->rollback();
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
		exit;
	}

	$em->flush();
	$em->clear();
	$em->getConnection()->commit();


} catch (\Exception $e) {
	$em->getConnection()->rollback();
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

 
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConta->getCodigo().'|'.$oConta->getNumero().'|'.$oConta->getCodStatus()->getCodigo().'|'.$oConta->getCodStatus()->getDescricao());
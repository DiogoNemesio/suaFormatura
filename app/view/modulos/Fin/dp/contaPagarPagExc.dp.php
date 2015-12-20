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
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);
if (isset($_POST['codHist']))			$codHist			= \Zage\App\Util::antiInjection($_POST['codHist']);

#################################################################################
## Verifica se os parâmetros foram passados corretamente
#################################################################################
if (!isset($codConta) || empty($codConta)) die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros 1"))));
if (!isset($codHist) || empty($codHist)) die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de parâmetros 2"))));

#################################################################################
## Resgata as informações da conta
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
if (!$oConta) die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Conta não encontrada"))));

#################################################################################
## Resgata as informações da Baixa
#################################################################################
$oHist		= $em->getRepository('Entidades\ZgfinHistoricoPag')->findOneBy(array('codigo' => $codHist));
if (!$oHist) die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pagamento não encontrado"))));

#################################################################################
## Verificar se a conta e histórico informados são da Organização atual
#################################################################################
if ($oConta->getCodOrganizacao()->getCodigo() !== $system->getCodOrganizacao()) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Exclusão de baixa indevida, ERR: 01x941"))));	
}

#################################################################################
## Verificar se a baixa pertence a conta informada
#################################################################################
if ($codConta != $oHist->getCodContaPag()->getCodigo()) die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Exclusão de baixa indevida, ERR: 01x947"))));

#################################################################################
## Resgata o perfil da conta
#################################################################################
$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;
if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "EXB")) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Pagemento não pode ser excluído"))));
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	#################################################################################
	## Salva o status atual da conta
	#################################################################################
	$codStatusAtual		= $oConta->getCodStatus()->getCodigo();
	
	$conta		= new \Zage\Fin\ContaPagar();
	$erro		= $conta->excluiBaixa($oConta,$oHist);
	
	if ($erro != false) {
		$em->getConnection()->rollback();
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
		exit;
	}
	
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();
	
	
	#################################################################################
	## Resgata as informações da conta
	#################################################################################
	$oConta		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
	
	#################################################################################
	## Controle de gravação
	#################################################################################
	$_indSalvar			= false;
	
	#################################################################################
	## Resgata o novo status
	#################################################################################
	$codStatusNovo		= \Zage\Fin\ContaPagar::recalculaStatus($oConta);
	
	#################################################################################
	## Apagar a data de liquidação caso a conta estiver liquidada
	#################################################################################
	if ($codStatusAtual	== "L" && $codStatusNovo !== "L") {
		$oConta->setDataLiquidacao(null);
		$_indSalvar			= true;
	}
	
	#################################################################################
	## Mudar o status
	#################################################################################
	if ($codStatusAtual	!= $codStatusNovo) {
		//$log->info("RecExc.dp: codStatusAtual: ".$codStatusAtual. " codStatusNovo: ".$codStatusNovo);
		$oStatusNovo		= $em->getRepository('Entidades\ZgfinContaStatusTipo')->findOneBy(array('codigo' => $codStatusNovo));
		$oConta->setCodStatus($oStatusNovo);
		$_indSalvar			= true;
	}
	
	if ($_indSalvar	== true) {
		//$log->info("RecExc.dp: Irei salvar e dar commit ");
		$em->getConnection()->beginTransaction();
		$em->flush();
		$em->clear();
		$em->getConnection()->commit();
	}
	
	
	
	$mensagem	= $tr->trans("Baixa excluída com sucesso");
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codConvVenda']))		$codConvVenda		= \Zage\App\Util::antiInjection($_POST['codConvVenda']);
if (isset($_POST['codFormando']))		$codFormando		= \Zage\App\Util::antiInjection($_POST['codFormando']);
if (isset($_POST['codFormaPag']))		$codFormaPag		= \Zage\App\Util::antiInjection($_POST['codFormaPag']);
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);

if (isset($_POST['codConvExtra']))		$codConvExtra		= \Zage\App\Util::antiInjection($_POST['codConvExtra']);
if (isset($_POST['codTipoEvento']))		$codTipoEvento		= \Zage\App\Util::antiInjection($_POST['codTipoEvento']);
if (isset($_POST['taxaConv']))			$taxaConv			= \Zage\App\Util::antiInjection($_POST['taxaConv']);
if (isset($_POST['valor']))				$valor				= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['quantDisp']))			$quantDisp			= \Zage\App\Util::antiInjection($_POST['quantDisp']);
if (isset($_POST['quantConv']))			$quantConv			= \Zage\App\Util::antiInjection($_POST['quantConv']);
if (isset($_POST['codTipoEvento']))		$codEvento			= \Zage\App\Util::antiInjection($_POST['codTipoEvento']);

if (!isset($codConvExtra))				$codConvExtra		= array();
if (!isset($codTipoEvento))				$codTipoEvento		= array();
if (!isset($taxaConv))					$taxaConv			= array();
if (!isset($valor))						$valor				= array();
if (!isset($quantDisp))					$quantDisp			= array();
if (!isset($quantConv))					$quantConv			= array();
if (!isset($codEvento))					$codEvento			= array();

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;
#################################################################################
## Fazer validação dos campos
#################################################################################
/** FORMANDO **/
if (!isset($codFormando) || empty($codFormando)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione o formando.");
	$err	= 1;
}

/** FORMA PAGAMENTO **/
if (!isset($codFormaPag) || empty($codFormaPag)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione a forma de pagamento.");
	$err	= 1;
}
	
if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	if (isset($codConvVenda) && (!empty($codConvVenda))) {
		$oConviteVenda	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findOneBy(array('codigo' => $codConvVenda));
		if (!$oConviteVenda) $oConviteVenda	= new \Entidades\ZgfmtConviteExtraVenda();
	}else{
		$oConviteVenda	= new \Entidades\ZgfmtConviteExtraVenda();
		$oConviteVenda->setDataCadastro(new DateTime(now));
	}
	 
	#################################################################################
	## RESGATAR OBJETOS
	#################################################################################
	$oFormando		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codFormando));
	//$oTipo			= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipoEvento[$i]));
	$oFormaPag		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codConta));

	for ($i = 0; $i < sizeof($codConvExtra); $i++) {
		$valorTotal += $quantConv[$i] * $valor[$i];
		

		if (isset($quantConv) || !empty($quantConv)) {
			$convDis	= \Zage\Fmt\Convite::listaConviteDispFormando($codFormando, $codEvento[$i]);
			if(empty($convDis) || $convDis == 0) {
				$oConf = $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codTipoEvento' => $codEvento));
				$convDis = $oConf->getQtdeMaxAluno();
			}
			
			$validador = $convDis - $quantConv[$i];
			if($validador < 0){
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Quantidade não pode ser maior que a disponível.");
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 				exit;
			}
		}
	}
	
	#################################################################################
	## SETAR VALORES
	#################################################################################
	$oConviteVenda->setCodFormando($oFormando);
	$oConviteVenda->setCodTransacao(1);
	$oConviteVenda->setCodFormaPagamento($oFormaPag);
	$oConviteVenda->setCodContaRecebimento($oConta);
	$oConviteVenda->setValorTotal($valorTotal);
	$oConviteVenda->setTaxaConveniencia(null);
	 
	$em->persist($oConviteVenda);
	
	for ($i = 0; $i < sizeof($codConvExtra); $i++) {
		if (isset($codConvVenda) && (!empty($codConvVenda))) {
	 		$oConviteItem	= $em->getRepository('Entidades\ZgfmtConviteExtraItem')->findOneBy(array('codigo' => $codConvVenda));
	 		if (!$oConviteItem) $oConviteItem	= new \Entidades\ZgfmtConviteExtraItem();
	 	}else{
	 		$oConviteItem	= new \Entidades\ZgfmtConviteExtraItem();
	 	}
	 	
	 	#################################################################################
	 	## RESGATAR OBJETOS
	 	#################################################################################
	 	//$oConvConf			= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipoEvento[$i]));
	 	$oConvConf			= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codTipoEvento' => $codTipoEvento[$i]));
	 	 
	 	#################################################################################
	 	## SETAR VALORES
	 	#################################################################################
	 	$oConviteItem->setCodVenda($oConviteVenda);
	 	$oConviteItem->setCodConviteConf($oConvConf);
	 	$oConviteItem->setQuantidade($quantConv[$i]);
	 	$oConviteItem->setValorUnitario($valor[$i]);
	 	
	 	$em->persist($oConviteItem);
	}
	#################################################################################
	## SALVAR
	#################################################################################
	$em->flush();
	$em->clear();

} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteVenda->getCodigo());

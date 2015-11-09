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

/** CONTA RECEBIMENTO **/
if (!isset($codConta) || empty($codConta)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Selecione a conta de recebimento.");
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
	$em->getConnection()->beginTransaction();
	
	if (isset($codConvVenda) && (!empty($codConvVenda))) {
		$oConviteVenda	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findOneBy(array('codigo' => $codConvVenda));
		if (!$oConviteVenda) $oConviteVenda	= new \Entidades\ZgfmtConviteExtraVenda();
	}else{
		$oConviteVenda	= new \Entidades\ZgfmtConviteExtraVenda();
	}
	 
	#################################################################################
	## RESGATAR OBJETOS
	#################################################################################
	$oFormando		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codFormando));
	$oFormaPag		= $em->getRepository('Entidades\ZgfinFormaPagamento')->findOneBy(array('codigo' => $codFormaPag));
	$oConta			= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codConta));

	for ($i = 0; $i < sizeof($codConvExtra); $i++) {
		#################################################################################
		## Formatar os campos
		#################################################################################
		$valor[$i]			= \Zage\App\Util::to_float($valor[$i]);
		$quantConv[$i]		= (int) $quantConv[$i];
		
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
	$oConviteVenda->setDataCadastro(new DateTime(now));
	
	$em->persist($oConviteVenda);
	
	$linha = 0;
	$html  = null;
	for ($i = 0; $i < sizeof($codConvExtra); $i++) {
		#################################################################################
		## Formatar os campos
		#################################################################################
		$valor[$i]			= \Zage\App\Util::to_float($valor[$i]);
		$quantConv[$i]		= (int) $quantConv[$i];
		
		if( isset($quantConv[$i]) && !empty($quantConv[$i]) && $quantConv[$i] != 0 ){
			if (isset($codConvVenda) && (!empty($codConvVenda))) {
		 		$oConviteItem	= $em->getRepository('Entidades\ZgfmtConviteExtraItem')->findOneBy(array('codigo' => $codConvVenda));
		 		if (!$oConviteItem) $oConviteItem	= new \Entidades\ZgfmtConviteExtraItem();
		 	}else{
		 		$oConviteItem	= new \Entidades\ZgfmtConviteExtraItem();
		 	}
		 	
		 	#################################################################################
		 	## RESGATAR OBJETOS
		 	#################################################################################
		 	$oConvConf			= $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codTipoEvento' => $codTipoEvento[$i]));
		 	 
		 	#################################################################################
		 	## SETAR VALORES
		 	#################################################################################
		 	$oConviteItem->setCodVenda($oConviteVenda);
		 	$oConviteItem->setCodConviteConf($oConvConf);
		 	$oConviteItem->setQuantidade($quantConv[$i]);
		 	$oConviteItem->setValorUnitario($valor[$i]);
		 	
		 	$em->persist($oConviteItem);
		 	
		 	$linha = $i + 1;
		 	$html .= '<tr style="background-color:#f9f9f9;padding:0; border:1px solid #ddd;text-align: center;">';
		 	$html .= '<td style="padding: 10px;">'.$linha.'</td>';
		 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getCodConviteConf()->getCodTipoEvento()->getDescricao().'</td>';
		 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getQuantidade().'</td>';
		 	$html .= '<td style="padding: 10px;">'.$oConviteItem->getValorUnitario().'</td>';
		 	$html .= '</tr>';
		 	
		}else{
			continue;
		}
	}

	#################################################################################
	## SALVAR
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o convite:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
	
	#################################################################################
	## Gerar a notificação
	#################################################################################
	/********** Enviar notificação *********/
	### Gerar notificacao enviar email ###
	$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
	$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'RIFA_CONF_COMPRA'));
	$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_ANONIMO);
	$notificacao->setAssunto("Confirmação compra do convite");
	$notificacao->setCodRemetente($oRemetente);
		
	//$notificacao->associaUsuario($oHistEmail->getCodUsuario()->getCodigo());
	$notificacao->enviaSistema();
	$notificacao->enviaEmail();
	$notificacao->setEmail($oFormando->getEmail());
	$notificacao->setCodTemplate($template);
		
	$notificacao->adicionaVariavel('COD_TRASACAO'		,$oConviteItem->getCodVenda()->getCodTransacao());
	$notificacao->adicionaVariavel('FORMA_PAGAMENTO'	,$oConviteItem->getCodVenda()->getCodFormaPagamento()->getDescricao());
	$notificacao->adicionaVariavel('CONTA_RECEBIMENTO'	,$oConviteItem->getCodVenda()->getCodContaRecebimento()->getNome());
	$notificacao->adicionaVariavel('COD_VENDA'			,$oConviteVenda->getCodigo());
	$notificacao->adicionaVariavel('TOTAL'				,$oConviteItem->getCodVenda()->getValorTotal());
	$notificacao->adicionaVariavel('DATA_VENDA'			,$oConviteItem->getCodVenda()->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]));
	$notificacao->adicionaVariavel('NOME'				,$oConviteItem->getCodVenda()->getCodFormando()->getNome());
	$notificacao->adicionaVariavel('EMAIL'				,$oConviteItem->getCodVenda()->getCodFormando()->getEmail());
	$notificacao->adicionaVariavel('HTML_TABLE'			,$html);
		
	$notificacao->salva();

	#################################################################################
	## SALVAR
	#################################################################################
	try {
		$em->flush();
		$em->getConnection()->commit();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o convite:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos processar sua solicitação. Por favor, tente novamente em instantes!! Caso o problema persista entre em contato com o nosso suporte especializado.");
	}
	
} catch (\Exception $e) {
	$em->getConnection()->rollBack();
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConviteVenda->getCodigo());

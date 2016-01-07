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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codConta)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codConta));
if (!$oConta) \Zage\App\Erro::halt('Conta não encontrada');

#################################################################################
## Resgata o perfil da conta
#################################################################################
$codPerfil	= ($oConta->getCodContaPerfil()) ? $oConta->getCodContaPerfil()->getCodigo() : 0;

#################################################################################
## Verifica se a conta pode ser confirmada
#################################################################################
if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $oConta->getCodStatus()->getCodigo(), "CON")) {
	$podePag	= false;
}else{
	$podePag	= true;
}

#################################################################################
## Verifica se a conta pode ser recebida
#################################################################################
if (!$podePag) {
	\Zage\App\Erro::halt($tr->trans('Conta não pode ser confirmada, status não permitido (%s)',array('%s' => $oConta->getCodStatus()->getCodigo())));
}

#################################################################################
## Formata as informações da conta 
#################################################################################
$codFormaPag		= ($oConta->getCodFormaPagamento() != null) ? $oConta->getCodFormaPagamento()->getCodigo() : null;
$codContaPag		= ($oConta->getCodConta() != null) ? $oConta->getCodConta()->getCodigo() : null;
$dataRec			= date($system->config["data"]["dateFormat"]);
$vencimento			= ($oConta->getDataVencimento() != null) 	? $oConta->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
$documento			= "";
$valorBoleto		= \Zage\Fmt\Financeiro::getValorBoletoConta($oConta->getCodigo());


#################################################################################
## Calcula o valor pendente de recebimento
#################################################################################
$contaRec			= new \Zage\Fin\ContaReceber();
$saldoDet			= $contaRec->getSaldoAReceberDetalhado($codConta);
if (!$contaRec->getValorJaRecebido($codConta)) {
	$valor				= \Zage\App\Util::to_float($oConta->getValor());
	$valorDesconto		= \Zage\App\Util::to_float($oConta->getValorDesconto());
	$valorOutros		= \Zage\App\Util::to_float($oConta->getValorOutros());
}else{
	$valor				= \Zage\App\Util::to_float($saldoDet["PRINCIPAL"]);
	$valorDesconto		= 0;
	$valorOutros		= \Zage\App\Util::to_float($saldoDet["OUTROS"]);
	
	#################################################################################
	## Verificar se o outros valores foi pago no valor principal
	#################################################################################
	if (($valor < 0) && (($valor + $valorOutros) == 0)) {
		$valor			= 0;
		$valorOutros	= 0;
	}
}

#################################################################################
## Verificar se a conta está atrasada e calcular o júros e mora caso existam
#################################################################################
if (\Zage\Fin\ContaReceber::estaAtrasada($oConta->getCodigo(), $dataRec) == true) {

	#################################################################################
	## Calcula os valor através da data de referência
	#################################################################################
	$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oConta->getCodigo(), $dataRec);
	$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oConta->getCodigo(), $dataRec);
	
}else{
	$valorJuros			= \Zage\App\Util::to_float($oConta->getValorJuros());
	$valorMora			= \Zage\App\Util::to_float($oConta->getValorMora());
}

#################################################################################
## Atualiza o saldo a receber
#################################################################################
$valorJuros			+= $saldoDet["JUROS"];
$valorMora			+= $saldoDet["MORA"];

#################################################################################
## Gerenciar as URls
#################################################################################
if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}


#################################################################################
## Select da Forma de Pagamento
#################################################################################
try {
	$aFormaPag	= $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
	$oFormaPag	= $system->geraHtmlCombo($aFormaPag,	'CODIGO', 'DESCRICAO',	$codFormaPag, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select da Conta de Crédito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$selected = ($aCntCer[$i]->getCodigo() == $codContaPag) ? "selected" : ""; 
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' $selected>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				$selected = ($aConta[$i]->getCodigo() == $codContaPag) ? "selected" : "";
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' $selected>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME',	$codContaPag, '');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Recebimento de Conta');
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('FORMAS_PAG'			,$oFormaPag);
$tpl->set('CONTAS_CRE'			,$oConta);
$tpl->set('DATA_REC'			,$dataRec);
$tpl->set('VENCIMENTO'			,$vencimento);
$tpl->set('VALOR'				,\Zage\App\Util::formataDinheiro($valor));
$tpl->set('VALOR_JUROS'			,\Zage\App\Util::formataDinheiro($valorJuros));
$tpl->set('VALOR_MORA'			,\Zage\App\Util::formataDinheiro($valorMora));
$tpl->set('VALOR_DESCONTO'		,\Zage\App\Util::formataDinheiro($valorDesconto));
$tpl->set('VALOR_OUTROS'		,\Zage\App\Util::formataDinheiro($valorOutros));
$tpl->set('DOCUMENTO'			,$documento);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('TEMP_JUROS'			,\Zage\App\Util::toMysqlNumber($valorJuros));
$tpl->set('TEMP_MORA'			,\Zage\App\Util::toMysqlNumber($valorMora));
$tpl->set('VALOR_BOLETO'		,\Zage\App\Util::toMysqlNumber($valorBoleto));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

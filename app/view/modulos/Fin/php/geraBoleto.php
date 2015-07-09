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
## Resgata as informações da conta
#################################################################################
$oConta		= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $codConta));

if (!$oConta) {
	\Zage\App\Erro::halt('Conta não encontrada');
	
}

#################################################################################
## Resgata as parcelas em aberto da mesma conta
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->
	findBy(
		array('codOrganizacao' => $system->getcodOrganizacao(), 'codGrupoConta' => $oConta->getCodGrupoConta(),'codStatus' => array('A','P')),
		array('parcela' => 'ASC')
	);

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();


#################################################################################
## Inicia o html da tabela
#################################################################################
$htmlTab	= '';

#################################################################################
## Faz o loop nas parcelas para montar a tabela
#################################################################################
for ($i = 0 ;$i < sizeof($contas); $i++) {

	#################################################################################
	## Valida o status da conta
	#################################################################################
	switch ($contas[$i]->getCodStatus()->getCodigo()) {
		case "A":
		case "P":
			$podeBol	= true;
			break;
		default:
			$podeBol	= false;
			break;
	}
	
	if (!$podeBol) {
		continue;
	}
	
	$codFormaPag		= ($contas[$i]->getCodFormaPagamento() 	!= null) ? $contas[$i]->getCodFormaPagamento()->getCodigo() : null;
	$codContaRec		= ($contas[$i]->getCodConta() 			!= null) ? $contas[$i]->getCodConta() 						: null;
	$vencimento			= ($contas[$i]->getDataVencimento() 	!= null) ? $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
	if (!$vencimento) {
		//continue;
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Vencimento não configurado (%s)',array('%s' => $vencimento)));
	}
	
	if ($codFormaPag	!== "BOL") {
		//continue;
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Forma de pagamento não é BOLETO (%s)',array('%s' => $codFormaPag)));
	}
	
	if (!$codContaRec) {
		//continue;
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Nenhuma conta corrente informada !!!'));
	}else if ($codContaRec->getCodTipo()->getCodigo() !== "CC") {
		//continue;
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Conta de recebimento não é do tipo "CONTA CORRENTE" !!! (%s)',array('%s' => $codContaRec->getCodTipo()->getCodigo())));
	}else if (!$codContaRec->getCodCarteira()) {
		//continue;
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Carteira não configurada na conta corrente !!!'));
	}
	

	#################################################################################
	## Verificar se a conta está atrasada
	#################################################################################
	$vencimento			= $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento);
	
	
	#################################################################################
	## Calcular o vencimento do boleto
	#################################################################################
	if ($numDias > 0) {
		$vencBol		= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
		
		#################################################################################
		## Calcular os dias em atraso com a data de referência o vencimento do boleto
		#################################################################################
		$numDias		= \Zage\Fin\Data::numDiasAtraso($vencimento,$vencBol);
		$htmlAtraso		= $numDias;
	}else{
		$vencBol		= $vencimento;
		$htmlAtraso		= "<i class='fa fa-check-circle green bigger-120'></i>";
	}
	
	#################################################################################
	## Ajustar o número dias para zero caso não esteja vencido
	#################################################################################
	//if ($numDias < 0) 	$numDias	= 0;
	
	
	#################################################################################
	## Calcular o valor e o desconto
	#################################################################################
	if (!$contaRec->getValorJaRecebido($contas[$i]->getCodigo())) {
		$valor				= \Zage\App\Util::to_float($contas[$i]->getValor());
		$valorDesconto		= \Zage\App\Util::to_float($contas[$i]->getValorDesconto());
	}else{
		$valor				= \Zage\App\Util::to_float($contaRec->getSaldoAReceber($contas[$i]->getCodigo()));
		$valorDesconto		= 0;
	}
	
	#################################################################################
	## Calcular o Juros e Mora
	#################################################################################
	if ($numDias <= 0) {
		$valorJuros			= 0;
		$valorMora			= 0;
		$numDias			= 0;
	}else{
		$valJuros			= \Zage\App\Util::to_float($codContaRec->getValorJuros());
		$valMora			= \Zage\App\Util::to_float($codContaRec->getValorMora());
		$pctJuros			= $codContaRec->getPctJuros();
		$pctMora			= $codContaRec->getPctMora();
	
		#################################################################################
		## Dar Prioridada aos valores, depois aos percentuais
		#################################################################################
		if ($valJuros) {
			$valorJuros	= $valJuros;
		}elseif ($pctJuros) {
			$valorJuros	= (($valor * ($pctJuros/100))/30)*$numDias;
		}
	
		if ($valMora)	{
			$valorMora	= $valMora;
		}else{
			$valorMora	= ($valor * ($pctMora/100));
		}
	}
	

	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas();
	$valorJuros			= \Zage\App\Util::to_float($valorJuros);
	$valorMora			= \Zage\App\Util::to_float($valorMora);
	
	#################################################################################
	## Selecionar a parcela que foi clicada
	#################################################################################
	if ($contas[$i]->getCodigo() == $codConta) {
		$checked		= "checked";
	}else{
		$checked		= null;
	}
	
	
	$htmlTab			.= '
	<tr>
		<td class="col-sm-1 center"><label class="position-relative"><input type="checkbox" '.$checked.' name="codContaSel[]" class="ace" value="'.$contas[$i]->getCodigo().'" /><span class="lbl"></span></label></th>
		<td class="col-sm-1 center">'.$parcela.'</td>
		<td class="col-sm-1 center">'.$vencimento.'</td>
		<td class="col-sm-1 center"><input class="form-control datepicker" id="vencimentoID" type="text" name="vencimento['.$contas[$i]->getCodigo().']" maxlength="10" value="'.$vencBol.'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data"></td>
		<td class="col-sm-1 center">'.$htmlAtraso.'</td>
		<td class="col-sm-2 center"><input type="text" name="valor['.$contas[$i]->getCodigo().']" readonly	maxlength="20" value="'.$valor.'" 			autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
		<td class="col-sm-1 center"><input type="text" name="valorJuros['.$contas[$i]->getCodigo().']" 		maxlength="20" value="'.$valorJuros.'"	 	autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
		<td class="col-sm-1 center"><input type="text" name="valorMora['.$contas[$i]->getCodigo().']" 		maxlength="20" value="'.$valorMora.'" 		autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
		<td class="col-sm-1 center"><input type="text" name="valorDesconto['.$contas[$i]->getCodigo().']" 	maxlength="20" value="'.$valorDesconto.'" 	autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
	</tr>
	';	

}

if (!isset($urlVoltar) || (!$urlVoltar)) {
	$urlVoltar			= ROOT_URL . "/Fin/contaReceberLis.php?id=".$id;
}else{
	$urlVoltar			= $urlVoltar . "&id=".$id;
}

#################################################################################
## Resgatar o e-mail do cliente
#################################################################################
if ($oConta->getCodPessoa()) {
	$email 	= $oConta->getCodPessoa()->getEmail();
}else{
	$email	= null;
}


#################################################################################
## Urls
#################################################################################
$urlMidia				= ROOT_URL . "/Fin/geraBoletoMidia.php";

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Geração de Boleto');
$tpl->set('COD_CONTA'			,$codConta);
$tpl->set('EMAIL'				,$email);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('URL_MIDIA'			,$urlMidia);
$tpl->set('TAB_PARCELAS'		,$htmlTab);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


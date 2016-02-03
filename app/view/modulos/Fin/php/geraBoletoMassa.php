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
global $em,$tr,$system;

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
if (!isset($_GET["cid"])) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Descompacta o CID
#################################################################################
$cid			= \Zage\App\Util::antiInjection($_GET["cid"]);
\Zage\App\Util::descompactaId($cid);

#################################################################################
## Monta o array
#################################################################################
if (!isset($aSelContas)) \Zage\App\Erro::halt('Falta de Parâmetros 3');
$aSelContas		= explode(",",$aSelContas);

#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $aSelContas));

if (sizeof($contas) == 0) {
	\Zage\App\Erro::halt($tr->trans('Conta[s] não encontrada !!!'));
}

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
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Status não permitido (%s)',array('%s' => $contas[$i]->getCodStatus()->getCodigo())));
	}
	
	$codFormaPag		= ($contas[$i]->getCodFormaPagamento() 	!= null) ? $contas[$i]->getCodFormaPagamento()->getCodigo() : null;
	$codContaRec		= ($contas[$i]->getCodConta() 			!= null) ? $contas[$i]->getCodConta() 						: null;
	$vencimento			= ($contas[$i]->getDataVencimento() 	!= null) ? $contas[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
	if (!$vencimento) {
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Vencimento não configurado (%s)',array('%s' => $vencimento)));
	}
	
	if ($codFormaPag	!== "BOL") {
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Forma de pagamento não é BOLETO (%s)',array('%s' => $codFormaPag)));
	}
	
	if (!$codContaRec) {
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Nenhuma conta corrente informada !!!'));
	}else if (!$codContaRec->getCodCarteira()) {
		\Zage\App\Erro::halt($tr->trans('Não pode gerar boleto da conta, Carteira não configurada na conta corrente !!!'));
	}
	
	#################################################################################
	## Verificar se a conta está atrasada e calcular o juros e mora caso existam
	#################################################################################
	$saldoDet			= $contaRec->getSaldoAReceberDetalhado($contas[$i]->getCodigo());
	if (\Zage\Fin\ContaReceber::estaAtrasada($contas[$i]->getCodigo(), $hoje) == true) {
		
		#################################################################################
		## Calcula o número de dias de atraso, e o novo vencimento do boleto
		#################################################################################
		$numDias		= \Zage\Fin\Data::numDiasAtraso($vencimento);
		$vencBol		= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
		$htmlAtraso		= $numDias;
		
		#################################################################################
		## Calcula os valor através da data de referência
		#################################################################################
		$valorJuros		= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($contas[$i]->getCodigo(), $vencBol);
		$valorMora		= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($contas[$i]->getCodigo(), $vencBol);
	
		$roJuros		= null;
	}else{
		#################################################################################
		## Resgata o valor de juros da conta
		#################################################################################
		$valorJuros		= \Zage\App\Util::toPHPNumber($contas[$i]->getValorJuros());
		$valorMora		= \Zage\App\Util::toPHPNumber($contas[$i]->getValorMora());
		$numDias		= 0;
		$vencBol		= $vencimento;
		$htmlAtraso		= "<i class='fa fa-check-circle green bigger-120'></i>";
		$roJuros		= "readonly";
	}
	
	#################################################################################
	## Atualiza o saldo a receber
	#################################################################################
	$valorJuros			+= $saldoDet["JUROS"];
	$valorMora			+= $saldoDet["MORA"];
	
	#################################################################################
	## Calcular o valor e o desconto
	#################################################################################
	if (!$contaRec->getValorJaRecebido($contas[$i])) {
		$valor				= \Zage\App\Util::to_float($contas[$i]->getValor());
		$valorDesconto		= \Zage\App\Util::to_float($contas[$i]->getValorDesconto());
		$valorOutros		= \Zage\App\Util::to_float($contas[$i]->getValorOutros());
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
	## Validação dos valores, não pode receber valores negativos
	#################################################################################
	if ($valor 			< 0)	$valor			= 0;
	if ($valorJuros 	< 0)	$valorJuros		= 0;
	if ($valorMora 		< 0)	$valorMora		= 0;
	if ($valorOutros 	< 0)	$valorOutros	= 0;
	if ($valorDesconto 	< 0)	$valorDesconto	= 0;


	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas();
	$valorJuros			= \Zage\App\Util::to_float($valorJuros);
	$valorMora			= \Zage\App\Util::to_float($valorMora);
	$valorTotal			= $valor + $valorJuros + $valorMora + $valorOutros - $valorDesconto;
	$valorOriginal		= \Zage\App\Util::to_float($contas[$i]->getValor()) + \Zage\App\Util::to_float($contas[$i]->getValorOutros()) - \Zage\App\Util::to_float($contas[$i]->getValorDesconto());
	$saldoDevedor		= $saldoDet["PRINCIPAL"] + $saldoDet["OUTROS"];
	$saldoDevedorTotal	= $saldoDevedor + $valorJuros + $valorMora;
	
	#################################################################################
	## Selecionar todas as contas por padrão
	#################################################################################
	$checked		= "checked";
	
	$htmlTab			.= '
	<tr>
		<td class="col-sm-1 center"><label class="position-relative"><input type="checkbox" '.$checked.' name="codContaSel[]" class="ace" value="'.$contas[$i]->getCodigo().'" /><span class="lbl"></span></label></th>
		<td class="col-sm-1 center">'.$parcela.'</td>
		<td class="col-sm-1 center">'.$vencimento.'</td>
		<td class="col-sm-1 center"><input class="form-control datepicker" id="vencimentoID" type="text" name="vencimento['.$contas[$i]->getCodigo().']" maxlength="10" value="'.$vencBol.'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="data"></td>
		<td class="col-sm-1 center">'.$htmlAtraso.'</td>
		<td class="col-sm-1 center"><input class="input-medium" type="text" name="valor['.$contas[$i]->getCodigo().']" 				readonly maxlength="20" value="'.\Zage\App\Util::formataDinheiro($valorOriginal).'" 			autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0"></td>
		<td class="col-sm-1 center"><input class="input-medium" type="text" name="saldoDevedorTotal['.$contas[$i]->getCodigo().']"	readonly maxlength="20" value="'.\Zage\App\Util::formataDinheiro($saldoDevedorTotal).'" 	autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0">  <input type="hidden" name="saldoDevedor['.$contas[$i]->getCodigo().']" value="'.$saldoDevedor.'" /></td>
		<td class="col-sm-1 center"><input class="input-medium" type="text" name="valorJuros['.$contas[$i]->getCodigo().']" 		readonly maxlength="20" value="'.\Zage\App\Util::formataDinheiro($valorJuros).'"	 		autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0" onchange="GeraBolAtualizaSaldoDevedor(\''.$contas[$i]->getCodigo().'\');"></td>
		<td class="col-sm-1 center"><input class="input-medium" type="text" name="valorMora['.$contas[$i]->getCodigo().']" 			readonly maxlength="20" value="'.\Zage\App\Util::formataDinheiro($valorMora).'" 				autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0" onchange="GeraBolAtualizaSaldoDevedor(\''.$contas[$i]->getCodigo().'\');"></td>
		<td class="col-sm-1 center"><input class="input-small" type="text" name="valorDesconto['.$contas[$i]->getCodigo().']" 		readonly maxlength="20" value="'.\Zage\App\Util::formataDinheiro($valorDesconto).'" 			autocomplete="off" required zg-data-toggle="mask" zg-data-mask="dinheiro" zg-data-mask-retira="0" onchange="GeraBolAtualizaSaldoDevedor(\''.$contas[$i]->getCodigo().'\');"></td>
	</tr>
	';	

}

#################################################################################
## Urls
#################################################################################
$urlMidia			= ROOT_URL . "/Fin/geraBoletoMassaMidia.php";
$urlVoltar			= ROOT_URL . "/Fin/geraBoletoLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Geração de Boleto em massa');
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('URL_MIDIA'			,$urlMidia);
$tpl->set('EMAIL'				,$_user->getUsuario());
$tpl->set('TAB_PARCELAS'		,$htmlTab);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


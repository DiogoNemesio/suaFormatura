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
global $system,$em,$log;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_GET['mesRef'])) 		$mesRef			= \Zage\App\Util::antiInjection($_GET['mesRef']);
if (isset($_GET['geraPdf'])) 		$geraPdf		= \Zage\App\Util::antiInjection($_GET['geraPdf']);
if (isset($_GET['avancar'])) 		$avancar		= \Zage\App\Util::antiInjection($_GET['avancar']);
if (isset($_GET['voltar'])) 		$voltar			= \Zage\App\Util::antiInjection($_GET['voltar']);
if (isset($_GET['todos'])) 			$todos			= \Zage\App\Util::antiInjection($_GET['todos']);

#################################################################################
## Resgata as informações do Relatório
#################################################################################
$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $_codMenu_));
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
if (!$oOrgFmt)	\Zage\App\Erro::halt("Organização não é uma formatura");

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio(''	,'A4-L',10,'',15,15,16,16,9,9,'L');

#################################################################################
## Criação do cabeçalho
#################################################################################
//$rel->adicionaCabecalho($info->getNome());
$rel->NaoExibeFiltrosNulo();

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Ajustar o timezone
#################################################################################
date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');

#################################################################################
## Verifica se é pra avançar ou retroceder a data
#################################################################################
if (isset($avancar) && $avancar == 1) {
	$offset	= 1;
}elseif (isset($voltar) && $voltar == 1) {
	$offset	= -1;
}else{
	$offset	= 0;
}

#################################################################################
## Verificar se o mês de referência foi informado
#################################################################################
if (!isset($mesRef) || !$mesRef) $mesRef = date('m/Y');
list ($mes, $ano) = split ('[/.-]', $mesRef);

#################################################################################
## Ajustar o mês de referência com base no offset
#################################################################################
$mesRef				= date('m/Y', mktime (0,0,0,($mes+$offset),1,$ano));
list ($mes, $ano) = split ('[/.-]', $mesRef);
$anoMesRef			= (int) $ano.$mes;


#################################################################################
## Verificar se o mês de referência é maior que a data atual, para não
## permitir visualizar movimentações futuras
#################################################################################
$anoMesAtual		= (int) date('Ym');
if ($anoMesRef > $anoMesAtual) {
	$mes		= date("m");
	$ano		= date("Y");
	$mesRef		= date("m/Y");
	$anoMesRef	= (int) $ano.$mes;
}

#################################################################################
## Calcular se pode avançar e retroceder no mês de referência
## O Maior mês é o mês atual, então bloquear o avanço caso o mês de referência seja o atual
## O Menor Mẽs será a data de cadastro da Formatura, então, bloquear o retroceder
## caso o mês de referência seja igual ao mês de cadastro
#################################################################################
$anoCad			= $oOrg->getDataCadastro()->format('Y');
$mesCad			= $oOrg->getDataCadastro()->format('m');
$anoMesCad		= (int) $anoCad.$mesCad;
$podeRetroceder	= ($anoMesRef <= $anoMesCad) 	? false : true;
$podeAvancar	= ($anoMesRef >= $anoMesAtual) 	? false : true;
$disBtnRet		= (!$podeRetroceder)			? "disabled=disabled" : null;
$disBtnAva		= (!$podeAvancar)				? "disabled=disabled" : null;  

#################################################################################
## Calcular as datas início e fim a partir do mês de referência
#################################################################################
$oDataIni			= mktime(0, 0, 0, $mes, 1, $ano);
$oDataFim			= mktime(0, 0, 0, $mes + 1, 0, $ano);
$oDataBase			= mktime(0, 0, 0, $mes, 0, $ano);
$dataIni			= date($system->config["data"]["dateFormat"],$oDataIni);
$dataFim			= date($system->config["data"]["dateFormat"],$oDataFim);
$dataBase			= date($system->config["data"]["dateFormat"],$oDataBase);

#################################################################################
## Montar o nome do Mês que será exibido
#################################################################################
$texto				= $mesRef . " (".ucfirst(strftime("%B",mktime(0,0,0,$mes,1,null))).")";

//echo "DataInicial: $dataIni, DataFinal: $dataFim, dataBase: $dataBase, MesRef: $mesRef, Texto: $texto, DisBtnRet: $disBtnRet, DisBtnAva: $disBtnAva, AnoMesCad: $anoMesCad, AnoMesRef: $anoMesRef<BR>";

#################################################################################
## Url desse script
#################################################################################
$urlForm			= ROOT_URL . '/Fin/relExtratoFinanceiro.php'; 

#################################################################################
## Calcular os Percentuais de Júros, Mora e convite extra que ficam para a Formatura
#################################################################################
if ($oOrgFmt) {
	$pctJuros		= $oOrgFmt->getPctJurosTurma();
	$pctMora		= $oOrgFmt->getPctMoraTurma();
	$pctConvite		= $oOrgFmt->getPctConviteExtraTurma();
	$pctConviteCer	= 100 - $pctConvite;
}else{
	$pctJuros		= 0;
	$pctMora		= 0;
	$pctConvite		= 0;
	$pctConviteCer	= 100;
}


#################################################################################
## Resgatar o saldo Inicial referente a dataBase
#################################################################################
$saldoInicial		= \Zage\Fmt\Financeiro::calcSaldoFormaturaPorDataBase($system->getCodOrganizacao(),$dataBase);

#################################################################################
## Calcular o saldo atual
#################################################################################
$valorArrecadado	= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorArrecadadoFormatura($system->getCodOrganizacao()));
$valorGasto			= \Zage\App\Util::to_float(\Zage\Fmt\Financeiro::calcValorGastoFormatura($system->getCodOrganizacao()));
$saldoAtual			= \Zage\App\Util::to_float($valorArrecadado	- $valorGasto);

#################################################################################
## Instância do query Builder
#################################################################################
$qb1 	= $em->createQueryBuilder();
$qb2 	= $em->createQueryBuilder();

#################################################################################
## Contas Pagas
#################################################################################
try {
	$qb1->select('hp')
	->from('\Entidades\ZgfinHistoricoPag','hp')
	->leftJoin('\Entidades\ZgfinContaPagar', 'cp', \Doctrine\ORM\Query\Expr\Join::WITH, 'hp.codContaPag = cp.codigo')
	->where($qb1->expr()->andx(
		$qb1->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
		$qb1->expr()->in('cp.codStatus'			, ':status'),
		$qb1->expr()->gte('hp.dataPagamento'	, ':dataIni'),
		$qb1->expr()->lte('hp.dataPagamento'	, ':dataFim')
	))
	->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
	->setParameter('dataIni'		, $oDataIni)
	->setParameter('dataFim'		, $oDataFim)
	->setParameter('status'			, array("L","P"));

	$query 				= $qb1->getQuery();
	$pag				= $query->getResult();

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Contas Recebidas
#################################################################################
try {
	$qb2->select('hr')
	->from('\Entidades\ZgfinHistoricoRec','hr')
	->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'hr.codContaRec = cr.codigo')
	->where($qb2->expr()->andx(
		$qb2->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
		$qb2->expr()->in('cr.codStatus'			, ':status'),
		$qb2->expr()->gte('hr.dataPagamento'	, ':dataIni'),
		$qb2->expr()->lte('hr.dataPagamento'	, ':dataFim')
	))
	->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
	->setParameter('dataIni'		, $oDataIni)
	->setParameter('dataFim'		, $oDataFim)
	->setParameter('status'			, array("L","P"));

	$query 				= $qb2->getQuery();
	$rec				= $query->getResult();

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Formatar os dados do relatório
#################################################################################
$aMov			= array();


#################################################################################
## Formatar os dados do contas a pagar
#################################################################################
for ($i = 0; $i < sizeof($pag); $i++) {
	
	$valor			= \Zage\App\Util::to_float($pag[$i]->getValorPago()) + \Zage\App\Util::to_float($pag[$i]->getValorOutros()) - \Zage\App\Util::to_float($pag[$i]->getValorDesconto()); 
	$juros			= \Zage\App\Util::to_float($pag[$i]->getValorJuros()) + \Zage\App\Util::to_float($pag[$i]->getValorMora());
	$descricao		= $pag[$i]->getContaPag()->getDescricao();
	$documento		= $pag[$i]->getDocumento();
	$parcela		= $pag[$i]->getContaPag()->getParcela() . '/' . $pag[$i]->getContaPag()->getNumParcelas();
	$oData			= $pag[$i]->getDataPagamento();
	$data			= $oData->format($system->config["data"]["dateFormat"]);
	$dataIndex		= (int) $oData->format('Ymd');
	$n				= (isset($aMov[$dataIndex]))	? sizeof($aMov[$dataIndex]) : 0;
	
	#################################################################################
	## Valor Líquido igual ao valor para as contas a pagar,pois o débito é todo da
	## Formatura
	#################################################################################
	$valor			+= $juros;
	$valorLiq		= $valor;
	
	$aMov[$dataIndex][$n]["data"]		= $data;
	$aMov[$dataIndex][$n]["valor"]		= $valor;
	$aMov[$dataIndex][$n]["valorLiq"]	= $valorLiq;
	$aMov[$dataIndex][$n]["descricao"]	= $descricao;
	$aMov[$dataIndex][$n]["parcela"]	= $parcela;
	$aMov[$dataIndex][$n]["documento"]	= $documento;
	$aMov[$dataIndex][$n]["tipo"]		= "D";
}

#################################################################################
## Formatar os dados do contas a receber
#################################################################################
for ($i = 0; $i < sizeof($rec); $i++) {

	$valor			= \Zage\App\Util::to_float($rec[$i]->getValorRecebido()) + \Zage\App\Util::to_float($rec[$i]->getValorOutros()) - \Zage\App\Util::to_float($rec[$i]->getValorDesconto());
	$descricao		= $rec[$i]->getContaRec()->getDescricao();
	$documento		= $rec[$i]->getDocumento();
	$parcela		= $rec[$i]->getContaRec()->getParcela() . '/' . $rec[$i]->getContaRec()->getNumParcelas();
	$oData			= $rec[$i]->getDataRecebimento();
	$data			= $oData->format($system->config["data"]["dateFormat"]);
	$dataIndex		= (int) $oData->format('Ymd');

	#################################################################################
	## Valor Líquido do crédito, é aquele que é contabilizado para a formatura, os casos
	## que o Valor Líquido é diferente do Valor Bruto, são os Juros / Mora e
	## Os Convites Extras (que o cerimonial fica com um percentual)
	## Os valores de Boleto e taxa de administração devem ser retirados
	## Os valores pagos de sistema também
	#################################################################################
	
	#################################################################################
	## Valor de júros / multa da formatura
	#################################################################################
	$valorJuros			= (\Zage\App\Util::to_float($rec[$i]->getValorJuros())	* $pctJuros	/ 100);
	$valorMora			= (\Zage\App\Util::to_float($rec[$i]->getValorMora()) 	* $pctMora	/ 100);
	$jurosLiq			= $valorJuros + $valorMora;

	#################################################################################
	## Verificar se a conta é de convite extra
	#################################################################################
	if (\Zage\Fmt\Convite::contaEhDeConviteExtra($rec[$i]->getCodContaRec()->getCodigo())) {
		$valorLiq				= round(($valor * $pctConvite) /100,2);
	}else{
		$valorLiq				= $valor;
	}
	
	#################################################################################
	## Calcular o valor não líquido de uma conta
	## Os valores não líquidos são eles:
	## Boleto, Sistema e Taxa de Administração
	#################################################################################
	$valorNaoLiq		= \Zage\Fmt\Financeiro::getValorNaoLiquidoConta($rec[$i]->getCodContaRec()->getCodigo());
	
	
	#################################################################################
	## Calcular o valor líquido do Recebimento
	## Valor Liquido é igual ao valor - o valor não líquido + o júros líquido
	#################################################################################
	$valorLiq			-= $valorNaoLiq;
	$valorLiq			+= $jurosLiq;
	
	$n				= (isset($aMov[$dataIndex]))	? sizeof($aMov[$dataIndex]) : 0;

	$aMov[$dataIndex][$n]["data"]		= $data;
	$aMov[$dataIndex][$n]["valor"]		= $valor;
	$aMov[$dataIndex][$n]["valorLiq"]	= $valorLiq;
	$aMov[$dataIndex][$n]["juros"]		= $juros;
	$aMov[$dataIndex][$n]["descricao"]	= $descricao;
	$aMov[$dataIndex][$n]["parcela"]	= $parcela;
	$aMov[$dataIndex][$n]["documento"]	= $documento;
	$aMov[$dataIndex][$n]["tipo"]		= "C";

}

#################################################################################
## Ordenar os dados do relatório pela data
#################################################################################
ksort($aMov);

#################################################################################
## Liberar memória
#################################################################################
unset($rec);
unset($pag);

	
if (sizeof($aMov) > 0) {
	
	#################################################################################
	## Não colocar os tamanhos do campo caso não seja para gerar o PDF
	#################################################################################
	if ($geraPdf	== 1) {
		$w1			= "width: 16%;";
		$w2			= "width: 8%;";
		$w3			= "width: 4%;";
		$w4			= "width: 8%;";
		$w5			= "width: 8%;";
		$iconOK		= "";
		$iconAb		= "(!)";
	}else{
		$w1			= "";
		$w2			= "";
		$w3			= "";
		$w4			= "";
		$w5			= "";
		$iconOK		= "<i class='fa fa-check-circle green'></i>";
		$iconAb		= "<i class='fa fa-exclamation-circle red'></i>";
	}
	
	
	$table	= '<table class="table table-condensed">';
	$table .= '<thead>
				<tr><th style="text-align: center;" colspan="16"><h4>TURMA: '.$oOrg->getNome().' - '.$oOrgFmt->getDataConclusao()->format('Y').' MÊS DE REFERÊNCIA: '.$texto.'</h4></th></tr>
				<tr>
					<th style="text-align: left; '.$w1.'"><strong>FORMANDO</strong></th>
					<th style="text-align: center; '.$w2.'"><strong>MESES ANTERIORES</strong></th>
					';
	$_mes		=	$_mesIni;
	$_ano		=	$_anoIni;
	foreach ($aMeses as $mesAtual) {
		$table .= '<th style="text-align: center; '.$w3.'"><strong>'.$mesAtual.'</strong></th>';
	}
	$table .='		<th style="text-align: right; '.$w4.'"><strong>TOTAL PAGO</strong></th>
					<th style="text-align: right; '.$w5.'"><strong>A PAGAR</strong></th>
				</tr>
				</thead><tbody>';

	foreach ($dadosRel as $lanc) {
		
		#################################################################################
		## Formatar os dados
		#################################################################################
		$anterior	= (isset($lanc["ANTERIOR"]["VALOR_PAGO"])		? $lanc["ANTERIOR"]["VALOR_PAGO"] 	: 0);
		$posterior	= (isset($lanc["POSTERIOR"]["VALOR_PAGO"])		? $lanc["POSTERIOR"]["VALOR_PAGO"] 	: 0);
		$total		= (isset($lanc["VALOR_TOTAL"])	? $lanc["VALOR_TOTAL"] 	: 0);
		$aPagar		= (isset($lanc["VALOR_APAGAR"])	? $lanc["VALOR_APAGAR"] : 0);
		
		$table .= '<tr>
			<td style="text-align: left;">'.$lanc["NOME_PESSOA"].'</td>
			<td style="text-align: center;">'.\Zage\App\Util::to_money($anterior).'</td>
		';
		foreach ($aHtml as $mes => $_html) {
			
			if ($lanc[$mes]["VALOR"] == 0 && $lanc[$mes]["VALOR_PAGO"] == 0) {
				$_icone			= "";
				$_valor			= " - ";
			}else if ($lanc[$mes]["VALOR"] > $lanc[$mes]["VALOR_PAGO"]) {
				if ($mes >= date('Ym')) {
					$_icone		= "";
					$_valor			= \Zage\App\Util::to_money($lanc[$mes]["VALOR_PAGO"]);
				}else{
					$_icone		= $iconAb;
					$_valor			= ($geraPdf	== 1) ? "NÃO PAGA" : \Zage\App\Util::to_money($lanc[$mes]["VALOR_PAGO"]);
				}
			}else{
				$_icone			= ($mes == date('Ym')) ? "" : $iconOK;  
				$_valor			= \Zage\App\Util::to_money($lanc[$mes]["VALOR_PAGO"]);
			}
			
			$valHtml		= $_valor . "&nbsp;".$_icone;
			$table 			.= str_replace("%MES_".$mes."%",$valHtml,$_html);
		}
		$table .= '	<td style="text-align: right;">'.\Zage\App\Util::to_money($total).'</td>
					<td style="text-align: right;">'.\Zage\App\Util::to_money($aPagar).'</td>
		';
		$table .='</tr>';
		
		#################################################################################
		## Atualizar os totalizadores
		#################################################################################
		$valTotal		+= \Zage\App\Util::to_float($lanc["VALOR"]);
		$valTotAPag		+= \Zage\App\Util::to_float($lanc["VALOR"] - $lanc["VALOR_APAGAR"]);
		
	}

	$table .= '	<tr>
					<th style="text-align: left;"><strong>'.$numFormandos.' Formandos</strong></th>
					<th style="text-align: right;" colspan="13"><strong>Totais</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valTotal).'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valTotAPag*-1).'</strong></th>
				</tr>
				</tbody>
				</table>';
	
}else{
	$table	= "<center>nenhuma informação encontrada !!!</center>";
}

if (isset($todos) && ($todos == 1)) {
	$checked	= "checked=checked";
}else{
	$checked	= "";
}

if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
	$html	.= '<h4 align="center"><strong>Extrato Financeiro</strong></h4>';
	$html	.= '<h4 align="center">'.$oOrg->getNome()	.'</h4>';
	$html	.= '<br>';
}else{
	$html	= '<table style="width: 100%;" class="table-condensed">
			<tr><td style="width: 70%;">
				<h4 align="center"><strong>Extrato Financeiro</strong></h4>
				</td>
				<td rowspan="2" align="right" style="width: 30%;">'.$tableTotal.'</td>
			</tr>
			<tr><td style="width: 70%; vertical-align:top;" valign="top"><h6 align="center">'.$oOrg->getNome().'</h6></td></tr>
			</table>
			';
	$html	.= '<br>';
	
	$html	= '
<form id="zgFormRelExtratoFinanceiroID" class="form-horizontal" method="GET" target="_blank" action="'.$urlForm.'" >
<input type="hidden" name="mesRef" 	id="mesRefID" 	value="'.$mesRef.'">
<input type="hidden" name="geraPdf" id="geraPdfID">
<input type="hidden" name="id" value="'.$id.'">
<div class="row">
	<div class="col-sm-12 center">
		<div class="btn-group btn-corner">
			<button type="button" class="btn btn-white btn-sm" '.$disBtnRet.' title="Voltar" id="btnRelExtratoFinanceiroVoltarID" onclick="zgRelExtratoFinanceiroVoltar();">
				<i class="fa fa-angle-double-left "></i>
			</button>
			<span id="relPagamentoPopoverID" style="width: 250px;" class="btn btn-white btn-sm" data-rel="popover" data-placement="bottom" >'.$texto.'</span>
			<button type="button" class="btn btn-white btn-sm tooltip-info" onclick="zgRelExtratoFinanceiroImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
				<i class="fa fa-file-pdf-o red"></i>
			</button>
					
			<button type="button" class="btn btn-white btn-sm" '.$disBtnAva.' title="Avançar" id="btnRelExtratoFinanceiroAvancarID" onclick="zgRelExtratoFinanceiroAvancar();">
				<i class="fa fa-angle-double-right"></i>
			</button>
		</div>
	</div>
	<div class="col-sm-6 pull-right hidden">
		<div class="checkbox pull-left">
			<label>
				<input name="verTodosMeses" id="verTodosMesesID" type="checkbox" '.$checked.' class="ace" onchange="zgRelExtratoFinanceiroTodosMeses();"/>
				<span class="lbl">&nbsp;Ver todos os meses</span>
			</label>
		</div>
	</div>
</div>

<div id="divPopoverRelExtratoFinanceiroContentID" class="hide">
	<div class="col-sm-12" id="divRelExtratoFinanceiroContentMensalID">
		<label class="col-sm-3 control-label">Mês:</label>
		<div class="col-xs-12 col-sm-9">
    		<input class="form-control datepicker col-md-12" readonly id="tempMesFiltroID" onchange="copiaValoresFormRelExtratoFinanceiro();" value="'.$mesRef.'" type="text" maxlength="7" autocomplete="off">
		</div>
	</div>
	<div class="form-group col-sm-12 center">
	&nbsp;
	</div>
	<label class="col-sm-3 control-label">&nbsp;</label>
	<div class="btn-group btn-corner col-sm-9 center">
		<button type="button" class="btn btn-success btn-sm" onclick="relExtratoFinanceiroFilter();">OK</button>
		<button type="button" class="btn btn-danger btn-sm" onclick="fecharPopoverRelExtratoFinanceiro();">Fechar</button>
	</div>
</div>
					
<div id="divPopoverRelExtratoFinanceiroTitleID" class="hide">
	<div class="btn-group btn-corner" style="width: 310px;">
		<p>Selecione o mês de referência</p>
	</div>
</div>
</form>
<br>

<div class="page-header">
	<h4 align="center"><strong>Extrato Financeiro</strong></h4>
	<h4 align="center">'.$oOrg->getNome().'</h4>
</div><!-- /.page-header -->

    				
<script>
	function zgRelExtratoFinanceiroImprimir() {
    	$("#geraPdfID").val(1);
		$("#zgFormRelExtratoFinanceiroID").submit();
	}

	function zgRelExtratoFinanceiroAvancar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef='.$mesRef.'&avancar=1";
		zgLoadUrl(vUrl);
	}

	function zgRelExtratoFinanceiroVoltar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef='.$mesRef.'&voltar=1";
		zgLoadUrl(vUrl);
	}

	function mostraMensalCPLisFiltro() {
		$("#divCPLisFiltroContentMensalID").removeClass("hide");
	}
	
	function copiaValoresFormRelExtratoFinanceiro() {
		var $mes;
		$mes		= $("#tempMesFiltroID").val();
		/** Copiar valores para o outro form **/
		$("#mesRefID").val( $mes );
	
	}
				
	function relExtratoFinanceiroFilter() {
		copiaValoresFormRelExtratoFinanceiro();
		var vMes	= $("#mesRefID").val();
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef="+vMes;
		zgLoadUrl(vUrl);
	}

	function fecharPopoverRelExtratoFinanceiro() {
		$("#relPagamentoPopoverID").popover("hide");
	}

	function zgRelExtratoFinanceiroTodosMeses() {
		var $verTodos	= $("#verTodosMesesID").is(":checked");
		if ($verTodos) {
			$verTodos = 1;
		}else{
			$verTodos = 0;
		}
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef='.$mesRef.'&todos="+$verTodos;
		zgLoadUrl(vUrl);
	}
				
$( document ).ready(function() {
	var tmp = $.fn.popover.Constructor.prototype.show;
	$.fn.popover.Constructor.prototype.show = function () {
	tmp.call(this);
		if (this.options.callback) {
			this.options.callback();
		}
	}
	
	$("[data-rel=popover]").popover({ 
		html:true,
		content: function() {
			return $("#divPopoverRelExtratoFinanceiroContentID").html();
		},
		title: function() {
			return $("#divPopoverRelExtratoFinanceiroTitleID").html();
		},
		callback: function() {
			$("#tempMesFiltroID").datepicker({autoclose: true,format: "mm/yyyy",viewMode: "months", minViewMode: "months"});
		} 
	}).on("show.bs.popover", function () {
		var $tip	= $(this).data("bs.popover").tip();
		$tip.css("max-width", "600px");
	});
	
});
				
</script>

';
	
}

$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';

$html		.= $htmlTable;
$relName	= "Extrato_".$mesRef.".pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}



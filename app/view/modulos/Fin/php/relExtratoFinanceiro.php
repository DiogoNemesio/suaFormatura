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
/*if ($anoMesRef > $anoMesAtual) {
	$mes		= date("m");
	$ano		= date("Y");
	$mesRef		= date("m/Y");
	$anoMesRef	= (int) $ano.$mes;
}*/

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

$disBtnRet		= false;
$disBtnAva		= false;

#################################################################################
## Calcular as datas início e fim a partir do mês de referência
#################################################################################
$oDataIni			= mktime(0, 0, 0, $mes, 1, $ano);
$oDataFim			= mktime(0, 0, 0, $mes + 1, 0, $ano);
$oDataBase			= mktime(0, 0, 0, $mes, 0, $ano);
$dataIni			= date($system->config["data"]["dateFormat"],$oDataIni);
$dataFim			= date($system->config["data"]["dateFormat"],$oDataFim);
$dataBase			= date($system->config["data"]["dateFormat"],$oDataBase);
$dDataIni			= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $dataIni . " 00:00:00");
$dDataFim			= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $dataFim . " 23:59:59");

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
	$pctJuros		= \Zage\App\Util::to_float($oOrgFmt->getPctJurosTurma());
	$pctMora		= \Zage\App\Util::to_float($oOrgFmt->getPctMoraTurma());
	$pctConvite		= \Zage\App\Util::to_float($oOrgFmt->getPctConviteExtraTurma());
}else{
	$pctJuros		= 0;
	$pctMora		= 0;
	$pctConvite		= 0;
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
//echo "Valor Arrecadado: $valorArrecadado, valorGasto: $valorGasto<BR>";
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
		$qb1->expr()->notIn('cp.codStatus'		, ':statusCanc'),
		$qb1->expr()->gte('hp.dataPagamento'	, ':dataIni'),
		$qb1->expr()->lte('hp.dataPagamento'	, ':dataFim')
	))
	->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
	->setParameter('statusCanc'		, array("S","C"))
	->setParameter('dataIni'		, $dDataIni)
	->setParameter('dataFim'		, $dDataFim);

	$query 				= $qb1->getQuery();
	//echo $query->getSQL()."<BR><BR>";
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
		$qb2->expr()->notIn('cr.codStatus'		, ':statusCanc'),
		$qb2->expr()->gte('hr.dataRecebimento'	, ':dataIni'),
		$qb2->expr()->lte('hr.dataRecebimento'	, ':dataFim')
	))
	->setParameter('codOrganizacao'	, $system->getCodOrganizacao())
	->setParameter('statusCanc'		, array("S","C"))
	->setParameter('dataIni'		, $dDataIni)
	->setParameter('dataFim'		, $dDataFim);

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
	$descricao		= $pag[$i]->getCodContaPag()->getDescricao();
	$documento		= $pag[$i]->getDocumento();
	$parcela		= $pag[$i]->getCodContaPag()->getParcela() . '/' . $pag[$i]->getCodContaPag()->getNumParcelas();
	$oData			= $pag[$i]->getDataPagamento();
	$data			= $oData->format($system->config["data"]["dateFormat"]);
	$dataIndex		= (int) $oData->format('Ymd');
	$pessoa			= ($pag[$i]->getCodContaPag()->getCodPessoa()) ? $pag[$i]->getCodContaPag()->getCodPessoa()->getNome() : null;
	$formaPag		= ($pag[$i]->getCodFormaPagamento()) 	? $pag[$i]->getCodFormaPagamento()->getCodigo() : null;
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
	$aMov[$dataIndex][$n]["pessoa"]		= $pessoa;
	$aMov[$dataIndex][$n]["formaPag"]	= $formaPag;
	$aMov[$dataIndex][$n]["tipo"]		= "D";
}

#################################################################################
## Formatar os dados do contas a receber
#################################################################################
for ($i = 0; $i < sizeof($rec); $i++) {

	$valor			= \Zage\App\Util::to_float($rec[$i]->getValorRecebido()) + \Zage\App\Util::to_float($rec[$i]->getValorOutros()) - \Zage\App\Util::to_float($rec[$i]->getValorDesconto());
	$descricao		= $rec[$i]->getCodContaRec()->getDescricao();
	$documento		= $rec[$i]->getDocumento();
	$parcela		= $rec[$i]->getCodContaRec()->getParcela() . '/' . $rec[$i]->getCodContaRec()->getNumParcelas();
	$oData			= $rec[$i]->getDataRecebimento();
	$data			= $oData->format($system->config["data"]["dateFormat"]);
	$dataIndex		= (int) $oData->format('Ymd');
	$pessoa			= ($rec[$i]->getCodContaRec()->getCodPessoa())	? $rec[$i]->getCodContaRec()->getCodPessoa()->getNome() : null;
	$formaPag		= ($rec[$i]->getCodFormaPagamento()) 			? $rec[$i]->getCodFormaPagamento()->getCodigo() : null;
	
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
	$aMov[$dataIndex][$n]["pessoa"]		= $pessoa;
	$aMov[$dataIndex][$n]["formaPag"]	= $formaPag;
	$aMov[$dataIndex][$n]["tipo"]		= "C";

}

#################################################################################
## Ordenar os dados do relatório pela data
#################################################################################
ksort($aMov);

#################################################################################
## Liberar memória
#################################################################################
//unset($rec);
//unset($pag);


$classeSaldo	= ($saldoInicial >= 0) ? "text-success" : "text-danger";
$table			= '<table style="width: 100%;" class="table table-condensed">';
$table	.= '<thead><tr style="background-color:#EFEFEF">
				<th style="text-align: left;" colspan="4">Período: '.$dataIni.' a '.$dataFim.'</th>
				<th style="text-align: right;" colspan="4">Data/Hora: '.date('d/m/Y').' às '.date('H:i').'h</th>
			</tr>';
$table	.= '<tr style="background-color:#FDF5E6">
				<th style="text-align: center; width: 7%;">Data</th>
				<th style="text-align: center; width: 40%;">Histórico</th>
				<th style="text-align: center; width: 16%;">Pessoa</th>
				<th style="text-align: center; width: 7%;">Pag</th>
				<th style="text-align: center; width: 10%;">Valor</th>
				<th style="text-align: center; width: 10%;">Valor Líquido</th>
				<th style="text-align: center; width: 10%;">Saldo</th>
			</tr></thead>';
$table	.= '<tbody><tr>
				<td style="text-align: center; width: 7%;"><strong>'.$dataBase.'</strong></td>
				<td style="text-align: left; width: 40%;"><strong>Saldo inicial</strong></td>
				<td colspan="4">&nbsp;</td>
				<td style="text-align: center; width: 10%;" class="'.$classeSaldo.'"><strong>'.\Zage\App\Util::to_money($saldoInicial).'</strong></td>
			</tr>';

$_saldo			= $saldoInicial;
$valorTotal		= 0;
$valorLiqTotal	= 0;
if (sizeof($aMov) > 0) {
	
	foreach ($aMov as $dataIndex => $_dados) {
		if (sizeof($_dados) > 0) {
			foreach ($_dados as $index => $mov) {
			
				$classeValor	= ($mov["tipo"] == "C") ? "text-success" : "text-danger";
				$valor			= ($mov["tipo"] == "C") ? $mov["valor"] 	: $mov["valor"]		* -1;
				$valorLiq		= ($mov["tipo"] == "C") ? $mov["valorLiq"]	: $mov["valorLiq"]	* -1;
				$_saldo			+= $valorLiq;
				$classeSaldo	= ($_saldo >= 0) ? "text-success" : "text-danger";
				$data			= $mov["data"];
				$descricao		= $mov["descricao"] . "&nbsp;(" . $mov["parcela"].")";
				$valorTotal		+= $valor;
				$valorLiqTotal	+= $valorLiq;
				
				$table	.= '<tr>
								<td style="text-align: center; width: 7%;">'.$data.'</td>
								<td style="text-align: left; width: 40%;">'.$descricao.'</td>
								<td style="text-align: left; width: 16%;">'.$mov["pessoa"].'</td>
								<td style="text-align: center; width: 7%;">'.$mov["formaPag"].'</td>
								<td style="text-align: center; width: 10%;" class="'.$classeValor.'">'.\Zage\App\Util::to_money($valor).'</td>
								<td style="text-align: center; width: 10%;" class="'.$classeValor.'">'.\Zage\App\Util::to_money($valorLiq).'</td>
								<td style="text-align: center; width: 10%;" class="'.$classeSaldo.'">'.\Zage\App\Util::to_money($_saldo).'</td>
							</tr>';
			}
		}
	}	
}

$classeSaldoFinal	= ($_saldo >= 0) 		? "text-success" : "text-danger";
$classeSaldoAtual	= ($saldoAtual >= 0) 	? "text-success" : "text-danger";
$classeValTot		= ($valorTotal >= 0) 	? "text-success" : "text-danger";
$classeValLiqTot	= ($valorLiqTotal>= 0) 	? "text-success" : "text-danger";
$table	.= '</tbody><tfoot><tr>
				<td style="text-align: center; width: 7%;"><strong>'.$dataFim.'</strong></td>
				<td style="text-align: left; width: 40%;"><strong>Total do período</strong></td>
				<td colspan="2">&nbsp;</td>
				<td style="text-align: center; width: 10%;" class="'.$classeValTot.'"><strong>'.\Zage\App\Util::to_money($valorTotal).'</strong></td>
				<td style="text-align: center; width: 10%;" class="'.$classeValLiqTot.'"><strong>'.\Zage\App\Util::to_money($valorLiqTotal).'</strong></td>
				<td style="text-align: center; width: 10%;" class="'.$classeSaldoFinal.'"><strong>'.\Zage\App\Util::to_money($_saldo).'</strong></td>
			</tr>';
$table	.= '<tr>
				<td style="text-align: center; width: 7%;"><strong>'.date("d/m/Y").'</strong></td>
				<td style="text-align: left; width: 40%;"><strong>Saldo atual</strong></td>
				<td colspan="4">&nbsp;</td>
				<td style="text-align: center; width: 10%;" class="'.$classeSaldoAtual.'"><strong>'.\Zage\App\Util::to_money($saldoAtual).'</strong></td>
			</tr>';
$table	.= '</tfoot></table>';


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
	$html	.= '
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



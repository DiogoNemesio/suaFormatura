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

#################################################################################
## Calcular o mês inicial a partir do mês de referência
#################################################################################
$_mesIni			= date("m",mktime(0, 0, 0, $mes - 11, 1 , $ano));
$_anoIni			= date("Y",mktime(0, 0, 0, $mes - 11, 1 , $ano));
$mesIni				= date("m/Y",mktime(0, 0, 0, $_mesIni, 1 , $_anoIni));

$_dtVencIni			= \DateTime::createFromFormat("m/Y", $mesIni);
$_dtVencFim			= \DateTime::createFromFormat("m/Y", $mesRef);

$dtVencIni			= $_dtVencIni->format("Ym");
$dtVencFim			= $_dtVencFim->format("Ym");

#################################################################################
## Montar o nome do Mês que será exibido
#################################################################################
$texto				= $mesRef . " (".ucfirst(strftime("%B",mktime(0,0,0,$mes,1,null))).")";

#################################################################################
## Url desse script
#################################################################################
$urlForm			= ROOT_URL . '/Fmt/relMensalidadeResumoPagamento.php'; 

#################################################################################
## Resgata a categoria de mensalidades
#################################################################################
$catMen				= \Zage\Adm\Parametro::getValorSistema('APP_COD_CAT_MENSALIDADE');


#################################################################################
## Resgata os dados do relatório
#################################################################################
try {

	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$rsm->addEntityResult('Entidades\ZgfinPessoa'	, 'p');
	$rsm->addScalarResult('COD_PESSOA'				, 'COD_PESSOA');
	$rsm->addScalarResult('NOME_PESSOA'				, 'NOME_PESSOA');
	$rsm->addScalarResult('MES_REF'					, 'MES_REF');
	$rsm->addScalarResult('VALOR'					, 'VALOR');
	$rsm->addScalarResult('VALOR_PAGO'				, 'VALOR_PAGO');
	
	$query 	= $em->createNativeQuery("
		SELECT  P.CODIGO AS COD_PESSOA,P.NOME AS NOME_PESSOA,DATE_FORMAT(R.DATA_VENCIMENTO,'%Y%m') AS MES_REF,SUM(IFNULL(R.VALOR,0) + IFNULL(R.VALOR_JUROS,0) - IFNULL(R.VALOR_DESCONTO_JUROS,0) + IFNULL(R.VALOR_MORA,0) - IFNULL(R.VALOR_DESCONTO_MORA,0) + IFNULL(R.VALOR_OUTROS,0) - IFNULL(R.VALOR_DESCONTO,0) - IFNULL(R.VALOR_CANCELADO,0)) AS VALOR, SUM(IFNULL(H.VALOR_RECEBIDO,0) + IFNULL(H.VALOR_JUROS,0) - IFNULL(H.VALOR_DESCONTO_JUROS,0) + IFNULL(H.VALOR_MORA,0) - IFNULL(H.VALOR_DESCONTO_MORA,0) + IFNULL(H.VALOR_OUTROS,0) - IFNULL(H.VALOR_DESCONTO,0)) VALOR_PAGO
		FROM	ZGFIN_CONTA_RECEBER 		R
		LEFT OUTER JOIN ZGFIN_HISTORICO_REC	H	ON (R.CODIGO		= H.COD_CONTA_REC)
		LEFT JOIN ZGFIN_PESSOA 				P	ON (R.COD_PESSOA	= P.CODIGO)
        LEFT JOIN ZGFIN_CONTA_STATUS_TIPO	ST	ON (R.COD_STATUS	= ST.CODIGO)
		WHERE	R.COD_ORGANIZACAO			= :codOrg
		AND		R.COD_STATUS				IN ('A','P','L')
        AND		EXISTS (
            	SELECT 1
            	FROM	ZGFIN_CONTA_RECEBER_RATEIO 	RR
            	WHERE	RR.COD_CONTA_REC			= R.CODIGO
         		AND		RR.COD_CATEGORIA			= :codCat   
        )
        
		GROUP	BY P.CODIGO,P.NOME,DATE_FORMAT(R.DATA_VENCIMENTO,'%Y%m')
		ORDER	BY 1,2
	", $rsm);
	$query->setParameter('codOrg'	, $system->getCodOrganizacao());
	$query->setParameter('codCat'	, $catMen);
	
	$contas = $query->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Formatar os dados do relatório
#################################################################################
$valTotal		= 0;
$valTotAPag		= 0;
$dadosRel		= array();
for ($i = 0; $i < sizeof($contas); $i++) {
	
	#################################################################################
	## Verificar se o mês está dentro do período do relatório
	#################################################################################
	$_valor		= $contas[$i]["VALOR"];
	
	#################################################################################
	## Verificar se o mês está dentro do período do relatório
	#################################################################################
	if ($contas[$i]["MES_REF"] < $dtVencIni) {
		$_mesRef		= "ANTERIOR";
	}elseif ($contas[$i]["MES_REF"] > $dtVencFim) {
		$_mesRef		= "POSTERIOR";
	}else{
		$_mesRef		= $contas[$i]["MES_REF"];
	}
	
	if (!isset($dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef])) {
		$dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef]["VALOR"]			= 0;
		$dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef]["VALOR_PAGO"]	= 0;
		$dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef]["VALOR_APAGAR"]	= 0;
	}
	
	if (!isset($dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_TOTAL"])) {
		$dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_TOTAL"] = 0;
	}
	
	
	if ($contas[$i]["VALOR"] > $contas[$i]["VALOR_PAGO"]) {
		$dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_APAGAR"]			+= ($contas[$i]["VALOR"] - $contas[$i]["VALOR_PAGO"]);
	}

	$dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef]["VALOR"]			+= $contas[$i]["VALOR"];
	$dadosRel[$contas[$i]["COD_PESSOA"]][$_mesRef]["VALOR_PAGO"]	+= $contas[$i]["VALOR_PAGO"];
	$dadosRel[$contas[$i]["COD_PESSOA"]]["NOME_PESSOA"]				= $contas[$i]["NOME_PESSOA"];
	$dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_TOTAL"]				+= $contas[$i]["VALOR_PAGO"];
	$valTotal														+= $contas[$i]["VALOR_PAGO"];

}

#################################################################################
## Montar o array de meses
#################################################################################
$_mes			=	$_mesIni;
$_ano			=	$_anoIni;
$aMeses			= array();
$aHtml			= array();
$numFormandos	= sizeof($dadosRel);	
for ($i = 0; $i < 12; $i++) {
	$_mesAtual			= (int) date("m",mktime(0, 0, 0, $_mes + $i, 1 , $_ano));
	$_anoAtual			= (int) date("Y",mktime(0, 0, 0, $_mes + $i, 1 , $_ano));
	$mesDesc			= gmstrftime("%b-%y",mktime(0, 0, 0, $_mesAtual, 1 , $_anoAtual));
	$mesIndex			= gmstrftime("%Y%m",mktime(0, 0, 0, $_mesAtual, 1 , $_anoAtual));
	$aMeses[]			= $mesDesc;
	$aHtml[$mesIndex]	= '<td style="text-align: right;">%MES_'.$mesIndex.'%</td>';
}


if (sizeof($dadosRel) > 0) {
	
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


if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
}else{
	$html	= '
<form id="zgFormRelResumoPagamentoID" class="form-horizontal" method="GET" target="_blank" action="'.$urlForm.'" >
<input type="hidden" name="mesRef" 	id="mesRefID" 	value="'.$mesRef.'">
<input type="hidden" name="geraPdf" id="geraPdfID">
<input type="hidden" name="id" value="'.$id.'">
<div class="row">
	<div class="col-sm-12 center">
		<div class="btn-group btn-corner center">
			<button type="button" class="btn btn-white btn-sm" title="Voltar" id="btnRelResumoPagamentoVoltarID" onclick="zgRelResumoPagamentoVoltar();">
				<i class="fa fa-angle-double-left "></i>
			</button>
			<span id="relPagamentoPopoverID" style="width: 250px;" class="btn btn-white btn-sm" data-rel="popover" data-placement="bottom" >'.$texto.'</span>
			<button type="button" class="btn btn-white btn-sm tooltip-info" onclick="zgRelResumoPagamentoImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
				<i class="fa fa-file-pdf-o red"></i>
			</button>
					
			<button type="button" class="btn btn-white btn-sm" title="Avançar" id="btnRelResumoPagamentoAvancarID" onclick="zgRelResumoPagamentoAvancar();">
				<i class="fa fa-angle-double-right"></i>
			</button>
		</div>
	</div>
</div>

<div id="divPopoverRelPagamentoContentID" class="hide">
	<div class="col-sm-12" id="divRelPagamentoContentMensalID">
		<label class="col-sm-3 control-label">Mês:</label>
		<div class="col-xs-12 col-sm-9">
    		<input class="form-control datepicker col-md-12" readonly id="tempMesFiltroID" onchange="copiaValoresFormRelPagamento();" value="'.$mesRef.'" type="text" maxlength="7" autocomplete="off">
		</div>
	</div>
	<div class="form-group col-sm-12 center">
	&nbsp;
	</div>
	<label class="col-sm-3 control-label">&nbsp;</label>
	<div class="btn-group btn-corner col-sm-9 center">
		<button type="button" class="btn btn-success btn-sm" onclick="relPagamentoFilter();">OK</button>
		<button type="button" class="btn btn-danger btn-sm" onclick="fecharPopoverRelPagamento();">Fechar</button>
	</div>
</div>
					
<div id="divPopoverRelPagamentoTitleID" class="hide">
	<div class="btn-group btn-corner" style="width: 310px;">
		<p>Selecione o mês de referência</p>
	</div>
</div>
</form>
<br>
<script>
	function zgRelResumoPagamentoImprimir() {
    	$("#geraPdfID").val(1);
		$("#zgFormRelResumoPagamentoID").submit();
	}

	function zgRelResumoPagamentoAvancar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef='.$mesRef.'&avancar=1";
		zgLoadUrl(vUrl);
	}

	function zgRelResumoPagamentoVoltar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef='.$mesRef.'&voltar=1";
		zgLoadUrl(vUrl);
	}

	function mostraMensalCPLisFiltro() {
		$("#divCPLisFiltroContentMensalID").removeClass("hide");
	}
	
	function copiaValoresFormRelPagamento() {
		var $mes;
		$mes		= $("#tempMesFiltroID").val();
		/** Copiar valores para o outro form **/
		$("#mesRefID").val( $mes );
	
	}
				
	function relPagamentoFilter() {
		copiaValoresFormRelPagamento();
		var vMes	= $("#mesRefID").val();
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&mesRef="+vMes;
		zgLoadUrl(vUrl);
	}

	function fecharPopoverRelPagamento() {
		$("#relPagamentoPopoverID").popover("hide");
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
			return $("#divPopoverRelPagamentoContentID").html();
		},
		title: function() {
			return $("#divPopoverRelPagamentoTitleID").html();
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
$relName	= "Resumo_Pagamento_".$mes.".pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}



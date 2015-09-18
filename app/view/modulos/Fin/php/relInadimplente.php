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
if (isset($_GET['dataRef'])) 		$dataRef		= \Zage\App\Util::antiInjection($_GET['dataRef']);
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
if (!isset($dataRef) || !$dataRef) $dataRef = date('d/m/Y');
list ($dia, $mes, $ano) = split ('[/.-]', $dataRef);

#################################################################################
## Ajustar a data de referência com base no offset
#################################################################################
$dataRef				= date('d/m/Y', mktime (0,0,0,$mes,($dia + $offset),$ano));
list ($dia, $mes, $ano) = split ('[/.-]', $dataRef);

$_dtVenc				= \DateTime::createFromFormat("d/m/Y", $dataRef);
$dtVenc					= $_dtVenc->format("Y-m-d");

#################################################################################
## Montar o nome do Doa que será exibido
#################################################################################
$texto					= $dataRef;

#################################################################################
## Url desse script
#################################################################################
$urlForm				= ROOT_URL . '/Fin/relInadimplente.php'; 

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
	$rsm->addScalarResult('COD_CONTA'				, 'COD_CONTA');
	$rsm->addScalarResult('NOME_PESSOA'				, 'NOME_PESSOA');
	$rsm->addScalarResult('VENCIMENTO'				, 'VENCIMENTO');
	$rsm->addScalarResult('DESCRICAO'				, 'DESCRICAO');
	$rsm->addScalarResult('VALOR'					, 'VALOR');
	$rsm->addScalarResult('VALOR_PAGO'				, 'VALOR_PAGO');
	
	$query 	= $em->createNativeQuery("
		SELECT  P.CODIGO AS COD_PESSOA,P.NOME AS NOME_PESSOA,R.CODIGO COD_CONTA,DATE_FORMAT(R.DATA_VENCIMENTO,'%d/%m/%Y') AS VENCIMENTO,(IFNULL(R.VALOR,0) + IFNULL(R.VALOR_JUROS,0) + IFNULL(R.VALOR_MORA,0) + IFNULL(R.VALOR_OUTROS,0) - IFNULL(R.VALOR_DESCONTO,0) - IFNULL(R.VALOR_CANCELADO,0)) AS VALOR, (IFNULL(H.VALOR_RECEBIDO,0) + IFNULL(H.VALOR_JUROS,0) + IFNULL(H.VALOR_MORA,0) + IFNULL(H.VALOR_OUTROS,0) - IFNULL(H.VALOR_DESCONTO,0)) VALOR_PAGO,R.DESCRICAO AS DESCRICAO
		FROM	ZGFIN_CONTA_RECEBER 		R
		LEFT OUTER JOIN ZGFIN_HISTORICO_REC	H	ON (R.CODIGO		= H.COD_CONTA_REC)
		LEFT JOIN ZGFIN_PESSOA 				P	ON (R.COD_PESSOA	= P.CODIGO)
        LEFT JOIN ZGFIN_CONTA_STATUS_TIPO	ST	ON (R.COD_STATUS	= ST.CODIGO)
		WHERE	R.COD_ORGANIZACAO			= :codOrg
		AND		R.COD_STATUS				IN ('A','P','L')
		AND		R.DATA_VENCIMENTO			< :dataVenc
		AND		( (R.DATA_LIQUIDACAO IS NULL) OR (R.DATA_LIQUIDACAO > R.DATA_VENCIMENTO) )
		AND		(H.CODIGO					IS NULL OR 	H.DATA_RECEBIMENTO			>  R.DATA_VENCIMENTO)
		AND		EXISTS (
            	SELECT 1
            	FROM	ZGFIN_CONTA_RECEBER_RATEIO 	RR
            	WHERE	RR.COD_CONTA_REC			= R.CODIGO
         		AND		RR.COD_CATEGORIA			= :codCat   
        )
		ORDER	BY 1,2,3
	", $rsm);
	$query->setParameter('codOrg'	, $system->getCodOrganizacao());
	$query->setParameter('codCat'	, $catMen);
	$query->setParameter('dataVenc'	, $dtVenc);
	
	
	$contas = $query->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Formatar os dados do relatório
#################################################################################
$valTotal		= 0;
$dadosRel		= array();
for ($i = 0; $i < sizeof($contas); $i++) {
	
	if ($contas[$i]["VALOR_PAGO"] < $contas[$i]["VALOR"]) {
		if (!isset($dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_APAGAR"])) {
			$dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_APAGAR"]	= 0;
			$dadosRel[$contas[$i]["COD_PESSOA"]]["CONTAS"]			= array();
			$dadosRel[$contas[$i]["COD_PESSOA"]]["NOME_PESSOA"]		= $contas[$i]["NOME_PESSOA"];
		}
		
		$dadosRel[$contas[$i]["COD_PESSOA"]]["CONTAS"][$contas[$i]["COD_CONTA"]]["DESCRICAO"]	= $contas[$i]["DESCRICAO"];
		$dadosRel[$contas[$i]["COD_PESSOA"]]["CONTAS"][$contas[$i]["COD_CONTA"]]["VENCIMENTO"]	= $contas[$i]["VENCIMENTO"];
		$dadosRel[$contas[$i]["COD_PESSOA"]]["CONTAS"][$contas[$i]["COD_CONTA"]]["VALOR"]		= $contas[$i]["VALOR"];
		$dadosRel[$contas[$i]["COD_PESSOA"]]["CONTAS"][$contas[$i]["COD_CONTA"]]["VALOR_PAGO"]	= $contas[$i]["VALOR_PAGO"];
		$dadosRel[$contas[$i]["COD_PESSOA"]]["VALOR_APAGAR"]									+= ($contas[$i]["VALOR"] - $contas[$i]["VALOR_PAGO"]);
		$valTotal																				+= ($contas[$i]["VALOR"] - $contas[$i]["VALOR_PAGO"]);
	}
}

if (sizeof($dadosRel) > 0) {
	
	#################################################################################
	## Não colocar os tamanhos do campo caso não seja para gerar o PDF
	#################################################################################
	if ($geraPdf	== 1) {
		$w1			= "width: 24%;";
		$w2			= "width: 50%;";
		$w3			= "width: 12%;";
		$w4			= "width: 12%;";
	}else{
		$w1			= "";
		$w2			= "";
		$w3			= "";
		$w4			= "";
	}
	
	
	$table	= '<table class="table table-condensed">';
	$table .= '<thead>
				<tr><th style="text-align: center;" colspan="4"><h4>TURMA: '.$oOrg->getNome().' - '.$oOrgFmt->getDataConclusao()->format('Y').' INADIMPLENTES NA DATA: '.$texto.'</h4></th></tr>
				</thead><tbody>';
				

	foreach ($dadosRel as $info) {
		$table .= '<tr style="background-color:#EEEEEE">
					<th style="text-align: left; '.$w1.'"><strong>FORMANDO:&nbsp;'.$info["NOME_PESSOA"].'</strong></th>
					<th style="text-align: left; '.$w2.'"><strong>DESCRIÇÃO</strong></th>
					<th style="text-align: center; '.$w3.'"><strong>VENCIMENTO</strong></th>
					<th style="text-align: right; '.$w4.'"><strong>VALOR</strong></th>
					</tr>
					';
		

		foreach ($info["CONTAS"] as $codConta => $parcela) {
			
			$table .= '<tr>
					<th style="text-align: left; '.$w1.'">&nbsp;</th>
					<th style="text-align: left; '.$w2.'">'.$parcela["DESCRICAO"].'</th>
					<th style="text-align: center; '.$w3.'">'.$parcela["VENCIMENTO"].'</th>
					<th style="text-align: right; '.$w4.'">'.\Zage\App\Util::to_money(($parcela["VALOR"] - $parcela["VALOR_PAGO"])).'</th>
					</tr>
					';
				
		}

		$table .= '<tr style="background-color:#EEEEEE">
					<th style="text-align: right;" colspan="3"><strong>TOTAL DO FORMANDO :&nbsp;'.$info["NOME_PESSOA"].'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($info["VALOR_APAGAR"]).'</strong></th>
					</tr>
					';
		
	}
	$table .= '<tr style="background-color:#CCCCCC">
					<th style="text-align: right;" colspan="3"><strong>TOTAL GERAL:&nbsp;</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valTotal).'</strong></th>
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
<form id="zgFormRelInadimplenteID" class="form-horizontal" method="GET" target="_blank" action="'.$urlForm.'" >
<input type="hidden" name="dataRef" 	id="dataRefID" 	value="'.$dataRef.'">
<input type="hidden" name="geraPdf" id="geraPdfID">
<input type="hidden" name="id" value="'.$id.'">
<div class="row">
	<div class="col-sm-12 center">
		<div class="btn-group btn-corner center">
			<button type="button" class="btn btn-white btn-sm" title="Voltar" id="btnRelInadimplenteVoltarID" onclick="zgRelInadimplenteVoltar();">
				<i class="fa fa-angle-double-left "></i>
			</button>
			<span id="relPagamentoPopoverID" style="width: 250px;" class="btn btn-white btn-sm" data-rel="popover" data-placement="bottom" >'.$texto.'</span>
			<button type="button" class="btn btn-white btn-sm tooltip-info" onclick="zgRelInadimplenteImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
				<i class="fa fa-file-pdf-o red"></i>
			</button>
					
			<button type="button" class="btn btn-white btn-sm" title="Avançar" id="btnRelInadimplenteAvancarID" onclick="zgRelInadimplenteAvancar();">
				<i class="fa fa-angle-double-right"></i>
			</button>
		</div>
	</div>
</div>

<div id="divPopoverRelPagamentoContentID" class="hide">
	<div class="col-sm-12" id="divRelPagamentoContentMensalID">
		<label class="col-sm-3 control-label">Mês:</label>
		<div class="col-xs-12 col-sm-9">
    		<input class="form-control datepicker col-md-12" readonly id="tempDataFiltroID" onchange="copiaValoresFormRelPagamento();" value="'.$dataRef.'" type="text" maxlength="7" autocomplete="off">
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
		<p>Selecione a data de referência</p>
	</div>
</div>
</form>
<br>
<script>
	function zgRelInadimplenteImprimir() {
    	$("#geraPdfID").val(1);
		$("#zgFormRelInadimplenteID").submit();
	}

	function zgRelInadimplenteAvancar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&dataRef='.$dataRef.'&avancar=1";
		zgLoadUrl(vUrl);
	}

	function zgRelInadimplenteVoltar() {
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&dataRef='.$dataRef.'&voltar=1";
		zgLoadUrl(vUrl);
	}

	function mostraMensalCPLisFiltro() {
		$("#divCPLisFiltroContentMensalID").removeClass("hide");
	}
	
	function copiaValoresFormRelPagamento() {
		var $mes;
		$mes		= $("#tempDataFiltroID").val();
		/** Copiar valores para o outro form **/
		$("#dataRefID").val( $mes );
	
	}
				
	function relPagamentoFilter() {
		copiaValoresFormRelPagamento();
		var vMes	= $("#dataRefID").val();
		var vUrl	= "'.$urlForm.'?id='.$id.'&geraPdf=0&dataRef="+vMes;
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
			$("#tempDataFiltroID").datepicker({autoclose: true,format: "dd/mm/yyyy"});
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
$relName	= "Inadimplentes_".str_replace("/", "_", $dataRef).".pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}



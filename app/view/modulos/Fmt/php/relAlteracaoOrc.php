<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
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
if (isset($_GET['dataRef'])) 		$dataRef		= \Zage\App\Util::antiInjection($_GET['dataRef']);
if (isset($_GET['geraPdf'])) 		$geraPdf		= \Zage\App\Util::antiInjection($_GET['geraPdf']);
if (isset($_GET['avancar'])) 		$avancar		= \Zage\App\Util::antiInjection($_GET['avancar']);
if (isset($_GET['voltar'])) 		$voltar			= \Zage\App\Util::antiInjection($_GET['voltar']);

#################################################################################
## Resgata as informações do Relatório
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
//if (!$oOrgFmt)	\Zage\App\Erro::halt("Organização não é uma formatura");

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio(''	,'A4-L',20,'',15,15,16,16,9,9,'P');

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

//$data 				= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], date($system->config["data"]["dateFormat"]." 00:00:00"));

$_dtVencIni			= \DateTime::createFromFormat("m/Y", $mesIni);
$_dtVencFim			= \DateTime::createFromFormat("m/Y", $mesRef);

// Configurar data
$_dtCadIni 	= "01/".$mesRef." 00:00:00";
$dtCadIni	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $_dtCadIni);

$ultimoDia 	= date("t", mktime(0,0,0,$mes,'01',$ano));
$_dtCadFim 	= $ultimoDia."/".$mesRef." 23:59:59";
$dtCadFim 	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $_dtCadFim);

#################################################################################
## Montar o nome do Doa que será exibido
#################################################################################
$texto				= $mesRef . " (".ucfirst(strftime("%B",mktime(0,0,0,$mes,1,null))).")";

#################################################################################
## Url desse script
#################################################################################
$urlForm				= ROOT_URL . '/Fmt/relAlteracaoOrc.php';

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

try {
	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$rsm->addScalarResult('VERSAO'				, 'VERSAO');
	$rsm->addScalarResult('DATA_ULTIMO_ACEITE'	, 'DATA_ULTIMO_ACEITE');
	$rsm->addScalarResult('QTDE_FORMANDOS'		, 'QTDE_FORMANDOS');
	$rsm->addScalarResult('QTDE_CONVIDADOS'		, 'QTDE_CONVIDADOS');
	$rsm->addScalarResult('VALOR_TOTAL'			, 'VALOR_TOTAL');
	$rsm->addScalarResult('DATA_ACEITE'			, 'DATA_ACEITE');
	$rsm->addScalarResult('NOME_USUARIO'		, 'NOME_USUARIO');
	$rsm->addScalarResult('COD_FORMATURA'		, 'COD_FORMATURA');
	$rsm->addScalarResult('NOME_FORMATURA'		, 'NOME_FORMATURA');
	$rsm->addScalarResult('COD_ACEITE'			, 'COD_ACEITE');
	
	
	$query 	= $em->createNativeQuery("
		SELECT O.VERSAO AS VERSAO,DATE_FORMAT(O.DATA_ACEITE,'%d/%m/%Y %T') AS DATA_ULTIMO_ACEITE,QTDE_FORMANDOS AS QTDE_FORMANDOS, 
				QTDE_CONVIDADOS AS QTDE_CONVIDADOS, HA.VALOR_TOTAL AS VALOR_TOTAL, DATE_FORMAT(HA.DATA_CADASTRO,'%d/%m/%Y %T') AS DATA_ACEITE,
				U.NOME AS NOME_USUARIO, ORG.CODIGO AS COD_FORMATURA, ORG.FANTASIA AS NOME_FORMATURA, HA.CODIGO AS COD_ACEITE
			
			FROM `ZGFMT_ORCAMENTO_HISTORICO_ACEITE` HA
			LEFT OUTER JOIN `ZGFMT_ORCAMENTO` O ON (O.CODIGO = HA.COD_ORCAMENTO)
			LEFT OUTER JOIN `ZGADM_ORGANIZACAO` ORG ON (ORG.CODIGO = O.COD_ORGANIZACAO)
			LEFT OUTER JOIN `ZGADM_ORGANIZACAO_ADM` OA ON (ORG.CODIGO = OA.COD_ORGANIZACAO)
			LEFT OUTER JOIN `ZGSEG_USUARIO` U ON (HA.COD_USUARIO = U.CODIGO)
			
			WHERE 	OA.COD_ORGANIZACAO_PAI = :codOrganizacao 
			AND		HA.DATA_CADASTRO BETWEEN :dataCadIni 
			AND 	:dataCadFim
			
			ORDER	BY ORG.FANTASIA
			", $rsm);
	$query->setParameter('codOrganizacao'		, $system->getCodOrganizacao());
	$query->setParameter('codTipo'				, "FMT");
	$query->setParameter('dataCadIni'			, $dtCadIni);
	$query->setParameter('dataCadFim'			, $dtCadFim);
	
	$resConsulta = $query->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$dadosRes		= array();

for ($i = 0; $i < sizeof($resConsulta); $i++) {

	if (!isset($dadosRes[$resConsulta[$i]["COD_FORMATURA"]])) {
		$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"]				= array();
		$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["NOME_FORMATURA"]		= $resConsulta[$i]["NOME_FORMATURA"];
		$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["COD_FORMATURA"]		= $resConsulta[$i]["COD_FORMATURA"];
		$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["DATA_ULTIMO_ACEITE"]	= $resConsulta[$i]["DATA_ULTIMO_ACEITE"];
	}

	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["VERSAO"]			= $resConsulta[$i]["VERSAO"];
	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["QTDE_FORMANDOS"]	= $resConsulta[$i]["QTDE_FORMANDOS"];
	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["QTDE_CONVIDADOS"]	= $resConsulta[$i]["QTDE_CONVIDADOS"];
	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["VALOR_TOTAL"]		= $resConsulta[$i]["VALOR_TOTAL"];
	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["DATA_ACEITE"]		= $resConsulta[$i]["DATA_ACEITE"];
	$dadosRes[$resConsulta[$i]["COD_FORMATURA"]]["ACEITE"][$resConsulta[$i]["COD_ACEITE"]]["NOME_USUARIO"]		= $resConsulta[$i]["NOME_USUARIO"];
	
}

$table	= '<table class="table table-striped table-bordered table-hover">';
$table .= '<thead>
					<tr style="background-color:#4682B4">
						<th style="text-align: center;" colspan="2"><font color="#FFFFFF"><strong>FORMATURA</strong></font></th>
						<th style="text-align: center;" colspan="2"><font color="#FFFFFF"><strong>ÚLTIMA ALTERAÇÃO</strong></font></th>
						<th style="text-align: center;" colspan="2"><strong></strong></th>
					</tr>
				</thead><tbody>';

if (!empty($dadosRes)){
	foreach ($dadosRes as $dados) {
	
		$table .= '<tr style="background-color:#B0C4DE">
					<td style="text-align: left;" colspan="2">&nbsp; '.$dados["NOME_FORMATURA"].'</td>
					<td style="text-align: center;" colspan="2">&nbsp;'.$dados["DATA_ULTIMO_ACEITE"].'</td>
					<td style="text-align: center;" colspan="2"><a id="detalhe_'.$dados["COD_FORMATURA"].'_ID" style="cursor: pointer;" onclick="orcHabilitaDetalhe(\''.$dados["COD_FORMATURA"].'\');">Exibir detalhes</a></td>
			   </tr>';
	
		//Tabela de eventos
	
		//$table .= '<table class="table table-condensed">';
		//$table .= '<thead>';
	
		$table .= '<tr class="hidden" id="trDetalhe_'.$dados["COD_FORMATURA"].'_ID" style="background-color:#E8E8E8">
					
					<th style="text-align: center;" colspan="1">VERSÃO</th>
					<th style="text-align: center;" colspan="1">USUÁRIO</th>
					<th style="text-align: center;" colspan="1">DATA ACEITE</th>
					<th style="text-align: center;" colspan="1">Nº FORMANDOS</th>
					<th style="text-align: center;" colspan="1">Nº CONVIDADOS</th>
					<th style="text-align: center;" colspan="1">VALOR TOTAL</th>

				</tr>';
		$table .= '';
	
		foreach ($dados["ACEITE"] as $info) {
		
			//Criar a tabela
			$table .= '<tr style="display:none;" class="trDetalheItem_'.$dados["COD_FORMATURA"].'_ID">
					
					<td style="text-align: center;" colspan="1">'.$info["VERSAO"].'</td>
					<td style="text-align: center;" colspan="1">'.$info["NOME_USUARIO"].'</td>
					<td style="text-align: center;" colspan="1">'.$info["DATA_ACEITE"].'</td>
					<td style="text-align: center;" colspan="1">'.$info["QTDE_FORMANDOS"].'</td>
					<td style="text-align: center;" colspan="1">'.$info["QTDE_CONVIDADOS"].'</td>
					<td style="text-align: center;" colspan="1">'.\Zage\App\Util::to_money($info["VALOR_TOTAL"]).'</td>
					
					
					</tr>';
		}
	
		//Fechar tabele de eventos
		//$table .= '</tbody></table>';
		//$table .= '</th></tr>';
	}
}else{
	$table .= '<tr style="background-color:#B0C4DE">
					<td style="text-align: left; width: 30%;" colspan="5">Nenhum resultado encontrado</td>
			   </tr>';
}
$table .= '</tbody></table>';

//Formulário
if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
	$html	.= '<h4 align="center"><strong>RESUMO DE VENDAS DE CONVITE EXTRA</strong></h4>';
	$html	.= '<h4 align="center">'.$oOrg->getNome()	.'</h4>';
	$html	.= '<br>';
}else{
	$html	= '

	<div class="page-header">
		<h1><i class="fa fa-line-chart">&nbsp;</i>Alteração de Orçamento&nbsp;&nbsp;
		</h1>
	</div><!-- /.page-header -->
			
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
	<br><br>
<script>
   	
  	function orcHabilitaDetalhe($codigo) {
		var $oTr		= $("#trDetalhe_"+$codigo+"_ID");
    				
		if ($oTr.hasClass("hidden")) {
			$oTr.removeClass("hidden");
    		$("#detalhe_"+$codigo+"_ID").html("Ocultar detalhes");
    		
    		var elems = document.getElementsByClassName("trDetalheItem_"+$codigo+"_ID");
    		for (var i=0; i<elems.length; i++) {
			    elems[i].style.display = "";
  			}
		}else{
			$oTr.addClass("hidden");
    		$("#detalhe_"+$codigo+"_ID").html("Exibir detalhes");
    						
    		var elems = document.getElementsByClassName("trDetalheItem_"+$codigo+"_ID");
    		for (var i=0; i<elems.length; i++) {
			    elems[i].style.display = "none";
  			}
		}
	}
    				
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
$relName	= "Pagamentos_".str_replace("/", "_", $dataRef).".pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}

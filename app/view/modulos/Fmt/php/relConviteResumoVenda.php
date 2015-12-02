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
if (isset($_GET['dataRef'])) 		$dataRef		= \Zage\App\Util::antiInjection($_GET['dataRef']);
if (isset($_GET['geraPdf'])) 		$geraPdf		= \Zage\App\Util::antiInjection($_GET['geraPdf']);
if (isset($_GET['avancar'])) 		$avancar		= \Zage\App\Util::antiInjection($_GET['avancar']);
if (isset($_GET['voltar'])) 		$voltar			= \Zage\App\Util::antiInjection($_GET['voltar']);

#################################################################################
## Resgata as informações do Relatório
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
if (!$oOrgFmt)	\Zage\App\Erro::halt("Organização não é uma formatura");

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
$urlForm				= ROOT_URL . '/Fmt/relConviteResumoVenda.php';

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

try {
	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$rsm->addScalarResult('COD_FORMANDO'		, 'COD_FORMANDO');
	$rsm->addScalarResult('NOME'				, 'NOME');
	$rsm->addScalarResult('VALOR_TOTAL'			, 'VALOR_TOTAL');
	$rsm->addScalarResult('COD_TIPO_EVENTO'		, 'COD_TIPO_EVENTO');
	$rsm->addScalarResult('QTDE'				, 'QTDE');
	$rsm->addScalarResult('VALOR_UNITARIO'		, 'VALOR_UNITARIO');
	$rsm->addScalarResult('DESCRICAO'			, 'DESCRICAO');
	$rsm->addScalarResult('TAXA_CONVENIENCIA'	, 'TAXA_CONVENIENCIA');
	
	$query 	= $em->createNativeQuery("
		SELECT V.COD_FORMANDO, P.NOME, SUM(V.VALOR_TOTAL) VALOR_TOTAL, E.COD_TIPO_EVENTO, T.DESCRICAO, SUM(V.TAXA_CONVENIENCIA) TAXA_CONVENIENCIA, SUM(I.QUANTIDADE) QTDE, I.VALOR_UNITARIO 
			FROM `ZGFMT_CONVITE_EXTRA_VENDA` V
			LEFT OUTER JOIN `ZGFIN_PESSOA` P ON (V.COD_FORMANDO = P.CODIGO) 
			LEFT OUTER JOIN `ZGFIN_CONTA_RECEBER` C ON (V.COD_TRANSACAO = C.COD_TRANSACAO) 
			LEFT OUTER JOIN `ZGFMT_CONVITE_EXTRA_VENDA_ITEM` I ON (V.CODIGO = I.COD_VENDA)
			LEFT OUTER JOIN `ZGFMT_EVENTO` E ON (I.COD_EVENTO = E.CODIGO) 
			LEFT OUTER JOIN `ZGFMT_EVENTO_TIPO` T ON (E.COD_TIPO_EVENTO = T.CODIGO) 
			WHERE 	C.COD_STATUS = :codStatus 
			AND  	C.COD_ORGANIZACAO = :codOrg
			GROUP 	BY E.COD_TIPO_EVENTO, V.COD_FORMANDO
			ORDER	BY P.NOME
			", $rsm);
	$query->setParameter('codOrg'		, $system->getCodOrganizacao());
	$query->setParameter('codStatus'	, "L");
	
	$vendas = $query->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$dadosRes		= array();
for ($i = 0; $i < sizeof($vendas); $i++) {

	if (!isset($dadosRes[$vendas[$i]["COD_FORMANDO"]])) {
		$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"]				= array();
		$dadosRes[$vendas[$i]["COD_FORMANDO"]]["NOME"]					= $vendas[$i]["NOME"];
		$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VALOR_TOTAL"]			= $vendas[$i]["VALOR_TOTAL"];
	}

	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["DESCRICAO"]		= $vendas[$i]["DESCRICAO"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["QUANTIDADE"]		= $vendas[$i]["QUANTIDADE"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["VALOR_UNITARIO"]	= $vendas[$i]["VALOR_UNITARIO"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["QTDE"]			= $vendas[$i]["QTDE"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["TAXA"]			= $vendas[$i]["TAXA_CONVENIENCIA"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["VALOR_TOTAL"]		= $vendas[$i]["VALOR_TOTAL"];
	//$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VALOR_APAGAR"]										+= ($vendas[$i]["VALOR"] - $vendas[$i]["VALOR_PAGO"]);
	//$valTotal																							+= $vendas[$i]["VALOR_TOTAL"];

}


$table	= '<table class="table table-condensed">';
//$table .= '<thead>
//			<tr><th style="text-align: center;" colspan="4"><h4>TURMA: '.$oOrg->getNome().' - '.$oOrgFmt->getDataConclusao()->format('Y').' INADIMPLENTES NA DATA: '.$texto.'</h4></th></tr>
//			</thead><tbody>';

foreach ($dadosRes as $dados) {
	$table .= '<thead><tr style="background-color:#EEEEEE">
					<th style="text-align: left;" colspan="5"><strong>&nbsp;'.$dados["NOME"].'</strong></th>
			   </tr></thead>';
	
	//Tabela de eventos
	//$table .= '<tr><th style="text-align: center;" colspan="5">';
	//$table .= '<table class="table table-condensed">';
	//$table .= '<thead>';
	$table .= '<tr style="background-color:#FDF5E6">
					<th style="text-align: center; width: 30%;"><strong>EVENTO</strong></th>
					<th style="text-align: center; width: 10%;"><strong>QUANTIDADE</strong></th>
					<th style="text-align: center; width: 20%;"><strong>VALOR TOTAL</strong></th>
					<th style="text-align: center; width: 20%;"><strong>DATA ENTREGA</strong></th>
					<th style="text-align: center; width: 20%;"><strong>ASSINATURA</strong></th>
				</tr>';
	$table .= '</thead><tbody>';
	
	foreach ($dados["VENDAS"] as $info) {
		
		$table .= '<tr>
					<td style="text-align: center; width: 30%;">'.$info["DESCRICAO"].'</td>
					<td style="text-align: center; width: 10%;">'.$info["QTDE"].'</td>
					<td style="text-align: center; width: 20%;">'.\Zage\App\Util::to_money($info["VALOR_TOTAL"] + $info["TAXA"]).'</td>
					<td style="text-align: center; width: 20%;">&nbsp;</td>
					<td style="text-align: center; width: 20%;">&nbsp;</td>
					</tr>';
	}
	
	//Fechar tabele de eventos
	//$table .= '</tbody></table>';
	//$table .= '</th></tr>';
}
$table .= '</table>';

//Formulário
if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
	$html	.= '<h4 align="center"><strong>RESUMO DE VENDAS DE CONVITE EXTRA</strong></h4>';
	$html	.= '<h4 align="center">'.$oOrg->getNome()	.'</h4>';
	$html	.= '<br>';
}else{
	$html	= '

	<div class="page-header">
		<h1><i class="fa fa-envelope">&nbsp;</i>Convite Extra&nbsp;&nbsp;
			<button class="btn btn-white btn-default btn-round" onclick="zgRelConviteResumoVendaImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
				<i class="ace-icon fa fa-file-pdf-o red2"></i>
				PDF	
			</button>
		</h1>
	</div><!-- /.page-header -->
			
	<form id="zgFormID" class="form-horizontal" method="GET" target="_blank" action="'.$urlForm.'" >
	<input type="hidden" name="dataRef" 	id="dataRefID" 	value="'.$dataRef.'">
	<input type="hidden" name="geraPdf" id="geraPdfID">
	<input type="hidden" name="id" value="'.$id.'">

	</form>
	<br>
	<script>
		function zgRelConviteResumoVendaImprimir() {
	    	$("#geraPdfID").val(1);
			$("#zgFormID").submit();
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
$relName	= "Pagamentos_".str_replace("/", "_", $dataRef).".pdf";

$log->info($html);

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}

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
global $system,$em,$log,$tr;

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
if (isset($_GET['geraPdf'])) 		$geraPdf		= \Zage\App\Util::antiInjection($_GET['geraPdf']);

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
## Url desse script
#################################################################################
$urlForm			= ROOT_URL . '/Fin/relResumoFinanceiro.php'; 

#################################################################################
## Array de valores Provisionados
#################################################################################
$aValoresProvisionados		= \Zage\Fmt\Financeiro::getValorProvisionadoPorFormando($system->getCodOrganizacao());

#################################################################################
## Array de valores pagos
#################################################################################
$aValoresPagos				= \Zage\Fmt\Financeiro::getValorPagoPorFormando($system->getCodOrganizacao());

#################################################################################
## Array de valores Devidos (Inadimplência)
#################################################################################
$aValoresDevidos			= \Zage\Fmt\Financeiro::getValorInadimplenciaPorFormando($system->getCodOrganizacao());

/*echo "Array Provisionado: <BR><BR>";
print_r($aValoresProvisionados);
echo "Array Pago: <BR><BR>";
print_r($aValoresPagos);
echo "Array Devido: <BR><BR>";
print_r($aValoresDevidos);
echo "<BR><BR>";
*/

#################################################################################
## Formatar o array de informações
#################################################################################
$aValores				= array();
for ($i = 0; $i < sizeof($aValoresProvisionados); $i++) {
	$cpf		= $aValoresProvisionados[$i][0]->getCgc();
	$aValores[$cpf]["provisionado"]		= \Zage\App\Util::to_float($aValoresProvisionados[$i]["sistema"]) + \Zage\App\Util::to_float($aValoresProvisionados[$i]["mensalidade"]);
}
if (sizeof($aValoresPagos) > 0) {
	foreach ($aValoresPagos as $cpf => $dados) {
		$aValores[$cpf]["mensalidade"]		= \Zage\App\Util::to_float($dados["mensalidade"]);
		$aValores[$cpf]["sistema"]			= \Zage\App\Util::to_float($dados["sistema"]);
		$aValores[$cpf]["rifas"]			= \Zage\App\Util::to_float($dados["rifas"]);
		$aValores[$cpf]["convites"]			= \Zage\App\Util::to_float($dados["convites"]);
		$aValores[$cpf]["outros"]			= \Zage\App\Util::to_float($dados["outros"]);
		$aValores[$cpf]["pago"]				= \Zage\App\Util::to_float($dados["sistema"]) + \Zage\App\Util::to_float($dados["mensalidade"]);
		$aValores[$cpf]["totalPago"]		= \Zage\App\Util::to_float($dados["sistema"]) + \Zage\App\Util::to_float($dados["mensalidade"]) + \Zage\App\Util::to_float($dados["rifas"]) + \Zage\App\Util::to_float($dados["convites"]) + \Zage\App\Util::to_float($dados["outros"]);
	}
}

for ($i = 0; $i < sizeof($aValoresDevidos); $i++) {
	$cpf		= $aValoresDevidos[$i][0]->getCgc();
	$aValores[$cpf]["valDevido"]		= \Zage\App\Util::to_float($aValoresDevidos[$i]["valor"]) - \Zage\App\Util::to_float($aValoresDevidos[$i]["valor_pago"]);
}

ksort($aValores);
//print_r($aValores);

if (sizeof($aValores) > 0) {
	
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
	
	foreach ($aValores as $cpf => $dados) {
		
		#################################################################################
		## Resgatar as informações do Formando
		#################################################################################
		$oFormando	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => $cpf));
		if (!$oFormando)	die ('Formando ('.$cpf.') não encontrado');
		
		#################################################################################
		## Resgatar o status da associação com a Formatura
		#################################################################################
		$oStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oFormando->getCodigo(),'codOrganizacao' => $system->getCodOrganizacao()));
		$status		= ($oStatus->getCodStatus()) ? $oStatus->getCodStatus()->getDescricao() : null;
		if (!$status) die($tr->trans('Status inválido para o Formando ('.$cpf.')'));
		
		$table .= '<thead><tr style="background-color:#EEEEEE">
					<th style="text-align: left;" colspan="7"><strong>&nbsp;'.$oFormando->getNome().'&nbsp; ('.$status.')'.'</strong></th>
			   </tr></thead>';
		
		$table .= '<tr style="background-color:#FDF5E6">
						<th style="text-align: center; width: 15%;"><strong>Provisionado de Mensalidades</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Pago em Mensalidades</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Total em aberto</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Saldo a Pagar</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Pago em Rifas</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Pago em Convites Extras</strong></th>
						<th style="text-align: center; width: 15%;"><strong>Total Pago</strong></th>
				</tr>';
		$table .= '</thead><tbody>';
		$table .= '<tr>
						<td style="text-align: center; width: 15%;">'.\Zage\App\Util::to_money($dados["provisionado"]).'</td>
						<td style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($dados["pago"]).'</td>
						<td style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($dados["valDevido"]).'</td>
						<td style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money(($dados["provisionado"] - $dados["pago"])).'</td>
						<td style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($dados["rifas"]).'</td>
						<td style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($dados["convites"]).'</td>
						<td style="text-align: center; width: 15%;">'.\Zage\App\Util::to_money($dados["totalPago"]).'</td>
					</tr>';
		$table .= '</tbody>';
	}
	
	$table .= '</table>';
}else{
	$table	= "<center>nenhuma informação encontrada !!!</center>";
}


if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
	$html	.= '<h4 align="center"><strong>RESUMO FINANCEIRO</strong></h4>';
	$html	.= '<h4 align="center">'.$oOrg->getNome().'</h4>';
	$html	.= '<br>';
}else{
	$html	= '

	<div class="page-header">
		<h1><i class="fa fa-dollar">&nbsp;</i>Resumo Financeiro&nbsp;&nbsp;
			<button class="btn btn-white btn-default btn-round" onclick="zgRelResumoFinanceiroImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF">
				<i class="ace-icon fa fa-file-pdf-o red2"></i>
				PDF	
			</button>
		</h1>
	</div><!-- /.page-header -->
			
	<form id="zgFormID" class="form-horizontal" method="GET" target="_blank" action="'.$urlForm.'" >
	<input type="hidden" name="geraPdf" id="geraPdfID">
	<input type="hidden" name="id" value="'.$id.'">

	</form>
	<br>
	<script>
		function zgRelResumoFinanceiroImprimir() {
	    	$("#geraPdfID").val(1);
			$("#zgFormID").submit();
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
$relName	= "Resumo_Financeiro.pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}



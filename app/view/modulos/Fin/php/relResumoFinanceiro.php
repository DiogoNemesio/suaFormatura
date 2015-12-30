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

#################################################################################
## Array de valores Devolvidos
#################################################################################
$aValoresDevolvidos			= \Zage\Fmt\Financeiro::getValorDevolvidoPorFormando($system->getCodOrganizacao());

/*echo "Array Devolvidos: <BR><BR>";
print_r($aValoresDevolvidos);
echo "Array Provisionado: <BR><BR>";
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
		$aValores[$cpf]["juros"]			= \Zage\App\Util::to_float($dados["juros"]);
		$aValores[$cpf]["mora"]				= \Zage\App\Util::to_float($dados["mora"]);
		$aValores[$cpf]["sistema"]			= \Zage\App\Util::to_float($dados["sistema"]);
		$aValores[$cpf]["rifas"]			= \Zage\App\Util::to_float($dados["rifas"]);
		$aValores[$cpf]["convites"]			= \Zage\App\Util::to_float($dados["convites"]);
		$aValores[$cpf]["outros"]			= \Zage\App\Util::to_float($dados["outros"]);
		$aValores[$cpf]["pago"]				= \Zage\App\Util::to_float($dados["sistema"]) + \Zage\App\Util::to_float($dados["mensalidade"]);
		$aValores[$cpf]["totalPago"]		= \Zage\App\Util::to_float($dados["sistema"]) + \Zage\App\Util::to_float($dados["mensalidade"]) + \Zage\App\Util::to_float($dados["rifas"]) + \Zage\App\Util::to_float($dados["convites"]) + \Zage\App\Util::to_float($dados["outros"]) + \Zage\App\Util::to_float($dados["juros"]) + \Zage\App\Util::to_float($dados["mora"]);
	}
}

for ($i = 0; $i < sizeof($aValoresDevolvidos); $i++) {
	$cpf		= $aValoresDevolvidos[$i][0]->getCgc();
	$aValores[$cpf]["devMensalidade"]		= \Zage\App\Util::to_float($aValoresDevolvidos[$i]["mensalidade"]);
	$aValores[$cpf]["devSistema"]			= \Zage\App\Util::to_float($aValoresDevolvidos[$i]["sistema"]);
	$aValores[$cpf]["devOutras"]			= \Zage\App\Util::to_float($aValoresDevolvidos[$i]["outras"]);
	$aValores[$cpf]["totalDevolvido"]		= \Zage\App\Util::to_float($aValoresDevolvidos[$i]["sistema"]) + \Zage\App\Util::to_float($aValoresDevolvidos[$i]["mensalidade"]) + \Zage\App\Util::to_float($aValoresDevolvidos[$i]["outras"]);
}

for ($i = 0; $i < sizeof($aValoresDevidos); $i++) {
	$cpf		= $aValoresDevidos[$i][0]->getCgc();
	$aValores[$cpf]["valDevido"]		= \Zage\App\Util::to_float($aValoresDevidos[$i]["valor"]) - \Zage\App\Util::to_float($aValoresDevidos[$i]["valor_pago"]);
}

if (sizeof($aValores) > 0) {
	

	#################################################################################
	## Criar um array ordenado por nome, e calcular o total geral da Formatura
	#################################################################################
	$aDados			= array();
	$totalPago		= 0;
	$totalDevolvido	= 0;
	$totalDevido	= 0;
	
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
		
		$nome		= $oFormando->getNome().'&nbsp; ('.\Zage\App\Util::formatCPF($cpf).')&nbsp;&nbsp;STATUS: '.$status; 

		$aDados[$nome]["provisionado"]		= $aValores[$cpf]["provisionado"];
		$aDados[$nome]["mensalidade"]		= $aValores[$cpf]["mensalidade"];
		$aDados[$nome]["juros"]				= $aValores[$cpf]["juros"];
		$aDados[$nome]["mora"]				= $aValores[$cpf]["mora"];
		$aDados[$nome]["sistema"]			= $aValores[$cpf]["sistema"];
		$aDados[$nome]["rifas"]				= $aValores[$cpf]["rifas"];
		$aDados[$nome]["convites"]			= $aValores[$cpf]["convites"];
		$aDados[$nome]["outros"]			= $aValores[$cpf]["outros"];
		$aDados[$nome]["pago"]				= $aValores[$cpf]["pago"];
		$aDados[$nome]["totalPago"]			= $aValores[$cpf]["totalPago"];
		$aDados[$nome]["valDevido"]			= $aValores[$cpf]["valDevido"];
		$aDados[$nome]["devMensalidade"]	= $aValores[$cpf]["devMensalidade"];
		$aDados[$nome]["devSistema"]		= $aValores[$cpf]["devSistema"];
		$aDados[$nome]["devOutras"]			= $aValores[$cpf]["devOutras"];
		$aDados[$nome]["totalDevolvido"]	= $aValores[$cpf]["totalDevolvido"];
		
		$totalPago							+= \Zage\App\Util::to_float($dados["totalPago"]);
		$totalDevolvido						+= \Zage\App\Util::to_float($dados["totalDevolvido"]);
		$totalDevido						+= \Zage\App\Util::to_float($dados["valDevido"]);
		
	}
	ksort($aDados);
	unset($aValores);
	
	if ($geraPdf == 1) {
		$tabStyle		= "width: 100%;";
		$tabClass		= "";
	}else{
		$tabStyle		= "width: 100%;";
		$tabClass		= "table-condensed";
	}
	
	#################################################################################
	## Criar a tabela de totalizador geral
	#################################################################################
	$tableTotal	= '<table style="'.$tabStyle.' width:400px;" align="right" class="table '.$tabClass.'">';
	$tableTotal .= '<tr style="background-color:#EFEFEF">
						<th style="text-align: center; border-left: 1px solid #000000; border-right: 1px solid #000000; border-top: 1px solid #000000;" colspan="3"><strong>Resumo da Formatura</strong></th>
					</tr>';
	$tableTotal .= '<tr style="background-color:#FDF5E6">
						<th style="text-align: center; width: 10%; border-left: 1px solid #000000;"><strong>Total pago</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Total devolvido</strong></th>
						<th style="text-align: center; width: 10%; border-right: 1px solid #000000;"><strong>Total atrasado</strong></th>
					</tr>';
	$tableTotal .= '<tr>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000; border-left: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($totalPago).'</strong></td>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($totalDevolvido).'</strong></td>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000; border-right: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($totalDevido).'</strong></td>
					</tr>';
	$tableTotal	.= '</table>';
	
	
	$table	= '<table style="'.$tabStyle.'" class="table '.$tabClass.'">';
	
	foreach ($aDados as $nome => $dados) {
		
		
		$table .= '<tr style="background-color:#EFEFEF">
					<th style="text-align: left; border: 1px solid #000000;" colspan="12" ><strong>&nbsp;'.$nome.'</strong></th>
			   	</tr><tbody>';

		$table .= '<tr style="background-color:#FDF5E6">
						<th style="text-align: center; border-left: 1px solid #000000; border-right: 1px solid #000000;" colspan="6"><strong>Pagamentos realizados</strong></th>
						<th style="text-align: center; border-left: 1px solid #000000; border-right: 1px solid #000000;" colspan="3"><strong>Devoluções</strong></th>
						<th style="text-align: center; border-left: 1px solid #000000; border-right: 1px solid #000000;" colspan="3"><strong>Resumo (Totalizadores)</strong></th>
				</tr>';
		$table .= '<tr style="background-color:#FDF5E6;">
						<th style="text-align: center; width: 10%; border-left: 1px solid #000000;"><strong>Mensalidades</strong></th>
						<th style="text-align: center; width: 7%;"><strong>Sistema</strong></th>
						<th style="text-align: center; width: 7%;"><strong>Juros/Mora</strong></th>
						<th style="text-align: center; width: 7%;"><strong>Rifas</strong></th>
						<th style="text-align: center; width: 7%;"><strong>Conv. extras</strong></th>
						<th style="text-align: center; width: 7%; border-right: 1px solid #000000;"><strong>Outros</strong></th>
						<th style="text-align: center; width: 10%; border-left: 1px solid #000000;"><strong>Mensalidades</strong></th>
						<th style="text-align: center; width: 7%;"><strong>Sistema</strong></th>
						<th style="text-align: center; width: 7%; border-right: 1px solid #000000;"><strong>Outros</strong></th>
						<th style="text-align: center; width: 10%; border-left: 1px solid #000000;"><strong>Total pago</strong></th>
						<th style="text-align: center; width: 10%;"><strong>Total devolvido</strong></th>
						<th style="text-align: center; width: 10%; border-right: 1px solid #000000;"><strong>Total atrasado</strong></th>
				</tr>';
		$table .= '';
		$table .= '<tr>
						<td style="text-align: center; width: 10%; border-left: 1px solid #000000; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["mensalidade"]).'</td>
						<td style="text-align: center; width: 7%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["sistema"]).'</td>
						<td style="text-align: center; width: 7%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money(($dados["juros"]+$dados["mora"])).'</td>
						<td style="text-align: center; width: 7%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["rifas"]).'</td>
						<td style="text-align: center; width: 7%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["convites"]).'</td>
						<td style="text-align: center; width: 7%; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["outros"]).'</td>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["devMensalidade"]).'</td>
						<td style="text-align: center; width: 7%; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["devSistema"]).'</td>
						<td style="text-align: center; width: 7%; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">'.\Zage\App\Util::to_money($dados["devOutras"]).'</td>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($dados["totalPago"]).'</strong></td>
						<td style="text-align: center; width: 10%; border-bottom: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($dados["totalDevolvido"]).'</strong></td>
						<td style="text-align: center; width: 10%; border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><strong>'.\Zage\App\Util::to_money($dados["valDevido"]).'</strong></td>
					</tr>';
		$table .= '</tbody>';
		
	}
	
	$table .= '</table>';
	
}else{
	$table	= "<center>nenhuma informação encontrada !!!</center>";
}


if ($geraPdf == 1) {
	$html	= '<body class="no-skin">';
	$html	.= '<table style="width: 100%;" class="table-condensed">
			<tr><td style="width: 70%;">
				<h4 align="center"><strong>Resumo Financeiro</strong></h4>
				</td>
				<td rowspan="2" align="right" style="width: 30%;">'.$tableTotal.'</td>
			</tr>
			<tr><td style="width: 70%; vertical-align:top;" valign="top"><h6 align="center">'.$oOrg->getNome().'</h6></td></tr>
			</table>
			';
	$html	.= '<br>';
}else{
	$html	= '<table style="width: 100%;" class="table-condensed">
			<tr><td style="width: 70%;">
				<h4 align="center"><strong>Resumo Financeiro</strong> - '.$oOrg->getNome().'</h4>
				</td>
				<td rowspan="2" align="right" style="width: 30%;">'.$tableTotal.'</td>
			</tr>
			<tr><td style="width: 70%; vertical-align:top;" valign="top"><h6 align="center"><button class="btn btn-white btn-default btn-round" onclick="zgRelResumoFinanceiroImprimir();" data-rel="tooltip" data-placement="top" title="Gerar PDF"><i class="ace-icon fa fa-file-pdf-o red2"></i>	PDF</button></h6></td></tr>
			</table>
			';
	$html	.= '<br>';
	$html	.= '
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
</div>';

if ($geraPdf == 1) {
	$htmlTable	.= '</body>';
}



$html		.= $htmlTable;
$relName	= "Resumo_Financeiro.pdf";

if ($geraPdf == 1) {
	$rel->WriteHTML($html);
	$rel->Output($relName,'D');
}else{
	echo $html;
}



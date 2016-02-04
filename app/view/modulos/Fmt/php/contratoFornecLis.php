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
global $em,$system;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
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
## Resgata as informações dos tipos de eventos
#################################################################################
$tipoEventos		= \Zage\Fmt\Orcamento::listaInfoContratoGrupoItemOrc($system->getCodOrganizacao());
$oQtdeContratada	= \Zage\Fmt\Orcamento::calculaQtdeGrupoItemOrc($system->getCodOrganizacao());
$oValorPago			= \Zage\Fmt\Orcamento::calculaValorPagoGrupoItemOrc($system->getCodOrganizacao());

#################################################################################
## Colocar as quantidades contradadas em um array, para facilitar os calculos
#################################################################################
$aQtdeContratada	= array();
for ($i = 0; $i < sizeof($oQtdeContratada); $i++) {
	$aQtdeContratada[$oQtdeContratada[$i][0]->getCodigo()]		= $oQtdeContratada[$i]["qtde"]; 
}

#################################################################################
## Colocar os valores pagos em um array, para facilitar os calculos
#################################################################################
$aValorPago	= array();
for ($i = 0; $i < sizeof($oValorPago); $i++) {
	$aValorPago[$oValorPago[$i][0]->getCodigo()]		= $oValorPago[$i]["valor"];
}


$teHtml				= "";
$corNada			= "#DA5430";
$corEmAndamento		= "#FEE074";
$corOK				= "#68BC31";

$pctConAcu			= 0;
$pctPagAcu			= 0;
$numTipoEventos		= sizeof($tipoEventos);

for ($i = 0; $i < $numTipoEventos; $i++) {
	$eid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codGrupoItem='.$tipoEventos[$i][0]->getCodigo());
	$linkItens		= "javascript:zgLoadUrl('".ROOT_URL . "/Fmt/contratoFornecItens.php?id=".$eid."');";
	
	$qtdeCon		= (int) $aQtdeContratada[$tipoEventos[$i][0]->getCodigo()];
	$valorPago		= (isset($aValorPago[$tipoEventos[$i][0]->getCodigo()])) ? $aValorPago[$tipoEventos[$i][0]->getCodigo()] : 0;
	$qtde			= ($tipoEventos[$i]["qtde"])	? $tipoEventos[$i]["qtde"] : 0;
	$valor			= ($tipoEventos[$i]["valor"])	? $tipoEventos[$i]["valor"] : 0;
	$pctCon			= ($qtde	> 0) ? round($qtdeCon * 100 / $tipoEventos[$i]["qtde"])		: 0;
	$pctPag			= ($valor	> 0) ? round($valorPago * 100 / $tipoEventos[$i]["valor"])	: 0;
	
	$pctConAcu		+= $pctCon;
	$pctPagAcu		+= $pctPag;
	
	if ($pctCon < 50) {
		$corCon			= $corNada;
	}elseif ($pctCon	== 100) {
		$corCon			= $corOK; 
	}else{
		$corCon			= $corEmAndamento;
	}

	if ($pctPag < 50) {
		$corPag			= $corNada;
	}elseif ($pctPag	== 100) {
		$corPag			= $corOK;
	}else{
		$corPag			= $corEmAndamento;
	}
	
	
	$pctConChart	= '<div class="easy-pie-chart percentage" data-size="50" data-color="'.$corCon.'" data-percent="'.$pctCon.'"><span class="percent">'.$pctCon.'</span>%</div>';
	$pctPagChart	= '<div class="easy-pie-chart percentage" data-size="50" data-color="'.$corPag.'" data-percent="'.$pctPag.'"><span class="percent">'.$pctPag.'</span>%</div>';
	
	$teHtml		.= '
		<div class="widget-box" style="width: 240px; display: inline-block; margin-left: 12px;">
			<div class="widget-header widget-header-small">
				<a href="'.$linkItens.'">
					<h5 class="widget-title">'.$tipoEventos[$i][0]->getDescricao().'</h5>
				</a>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<table style="width: 100%; border-collapse: separate; border-spacing:2px 0px;">
						<tr><th colspan="2" style="text-align: center; background-color:#EFEFEF;">Total do evento</th></tr>
						<tr><th colspan="2" style="text-align: center; background-color:#FDF5E6;">'.\Zage\App\Util::to_money($tipoEventos[$i]["valor"]).'</th></tr>
						<tr><th colspan="2">&nbsp;</th></tr>
						<tr style="background-color:#EFEFEF;">
							<td style="text-align: center; ">Contratado</td>
							<td style="text-align: center;">Pago</td>
						</tr>
						
						<tr>
							<td class="center" style="text-align: center; padding-top: 8px;">'.$pctConChart.'</td>
							<td class="center" style="text-align: center; padding-top: 8px;">'.$pctPagChart.'</td>
						</tr>
					</table>
				</div>
				<div>
					<a href="'.$linkItens.'" class="btn btn-block btn-light">
						<i class="ace-icon fa fa-arrow-circle-right bigger-110"></i>
						<span>Visualizar os itens</span>
					</a>
				</div>
			</div>
		</div>
	';
}

#################################################################################
## Calculo  do percentual total
#################################################################################
$pctConTot		= round($pctConAcu / $numTipoEventos);
$pctPagTot		= round($pctPagAcu / $numTipoEventos);

if ($pctConTot < 50) {
	$corConTot		= $corNada;
}elseif ($pctConTot	== 100) {
	$corConTot		= $corOK;
}else{
	$corConTot		= $corEmAndamento;
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('ID'						,$id);
$tpl->set('TIPOS_EVENTO'			,$teHtml);
$tpl->set('PCT_CON_TOT'				,$pctConTot);
$tpl->set('PCT_PAG_TOT'				,$pctPagTot);
$tpl->set('COR_CON_TOT'				,$corConTot);
$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'						,$_icone_);
$tpl->set('COD_MENU'				,$_codMenu_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

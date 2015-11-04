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
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros'));
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
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codPlanoOrc'])) 		$codPlanoOrc			= \Zage\App\Util::antiInjection($_GET['codPlanoOrc']);
if (isset($_GET['codVersao'])) 			$codVersao				= \Zage\App\Util::antiInjection($_GET['codVersao']);

if (!isset($codPlanoOrc)) exit;

#################################################################################
## Resgatar os dados
#################################################################################
$itens		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codPlano' => $codPlanoOrc,'indAtivo' => 1));

#################################################################################
## Verificar se o orçamento tem algum item
#################################################################################
if (sizeof($itens) == 0)	{
	die ("Configure o Plano orçamentário antes de usá-lo");
}


#################################################################################
## Carrega o orçamento salvo
#################################################################################
$orcItens		= $em->getRepository('Entidades\ZgfmtOrcamentoItem')->findBy(array('codOrcamento' => $codVersao));

#################################################################################
## Monta um array com os itens salvos
#################################################################################
$aOrcItens		= array();
for ($i = 0; $i < sizeof($orcItens); $i++) {
	$item		= $orcItens[$i]->getCodItem();
	$codTipo	= $item->getCodGrupoItem()->getCodigo();
	$codigo		= $item->getCodigo();
	$aOrcItens[$codigo]["QTDE"]		= $orcItens[$i]->getQuantidade();
	$aOrcItens[$codigo]["VALOR"]	= \Zage\App\Util::formataDinheiro($orcItens[$i]->getValorUnitario());
	$aOrcItens[$codigo]["OBS"]		= $orcItens[$i]->getObservacao();
	$aOrcItens[$codigo]["TOTAL"]	= \Zage\App\Util::to_float($orcItens[$i]->getQuantidade() * \Zage\App\Util::to_float($orcItens[$i]->getValorUnitario()));
}


#################################################################################
## Montar o array com as informações do Plano
#################################################################################
$aItens		= array(); 
for ($i = 0; $i < sizeof($itens); $i++) {
	$codTipo		= $itens[$i]->getCodGrupoItem()->getCodigo();
	$codigo			= $itens[$i]->getCodigo();

	$aItens[$codTipo]["DESCRICAO"]						= $itens[$i]->getCodGrupoItem()->getDescricao();
	$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 		= $itens[$i]->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 		= $itens[$i]->getCodTipoItem()->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 		= $itens[$i]->getItem();
	
	if (isset($aOrcItens[$codigo])) {
		$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 	= $aOrcItens[$codigo]["QTDE"];
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 	= $aOrcItens[$codigo]["VALOR"];
		$aItens[$codTipo]["ITENS"][$codigo]["OBS"] 		= $aOrcItens[$codigo]["OBS"];
		$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"]	= $aOrcItens[$codigo]["TOTAL"];
	}else{
		$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 	= null;
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 	= null;
		$aItens[$codTipo]["ITENS"][$codigo]["OBS"] 		= null;
		$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"] 	= null;
	}
}

//print_r($aItens);
//exit;

#################################################################################
## Cria o html dinâmico 
#################################################################################
$tabIndex	= 101;
$htmlForm	= '';
$htmlForm	.= '<h4 align="center"><b>Detalhes dos eventos</b></h4>';
$htmlForm	.= '<br>';
$htmlForm	.= '<center>';
$htmlForm	.= '<div id="itensOrcamentoID" class="panel-group accordion-style1" style="width: 98%;">';

foreach ($aItens as $codTipo => $aItem)	{
	$htmlForm	.= '<div class="panel panel-default">';
	$htmlForm	.= '<div class="panel-heading">';
	$htmlForm	.= '<a href="#itemTipo_'.$codTipo.'_ID" data-parent_old="#itensOrcamentoID" data-toggle="collapse" aria-expanded="true" aria-controls="collapseThree" class="accordion-toggle collapsed">';
	//$htmlForm	.= '<i class="ace-icon fa fa-chevron-left pull-right" data-icon-hide="ace-icon fa fa-chevron-down" data-icon-show="ace-icon fa fa-chevron-left"></i>';
	$htmlForm	.= '<i class="ace-icon fa fa-chevron-right pull-left" data-icon-hide="ace-icon fa fa-chevron-down" data-icon-show="ace-icon fa fa-chevron-right"></i>';
	$htmlForm	.= '&nbsp;<label class="pull-left" id="lbEvento_'.$codTipo.'_ID">'.$aItem["DESCRICAO"].'</label>';
	$htmlForm	.= '</a>';
	$htmlForm	.= '</div>';
	$htmlForm	.= '<div class="panel-collapse collapse in" id="itemTipo_'.$codTipo.'_ID">';
	$htmlForm	.= '<div class="panel-body">';

	
	
	#################################################################################
	## Montar a tabela de itens
	#################################################################################
	$tipoItens	= $aItem["ITENS"];
	if (sizeof($tipoItens) > 0) {
		$htmlForm	.= '<div class="col-sm-10" align="center">';
		$htmlForm	.= '<table id="tabItem_'.$codItem.'_ID" zg-table-orc="1" class="table table-hover table-condensed">';
		$totalTipo	= 0;
		
		foreach ($tipoItens as $codItem => $item) {
			
			if ($item["TIPO"] == "UN") {
				$ro		= null;
				$qtde	= $item["QTDE"];
			}else{
				$ro		= "readonly";
				$qtde	= 1;
			}
			
			if (isset($item["VALOR"]) && $item["VALOR"]) {
				$checked	= 'checked="checked"';
			}else{
				$checked	= null;
			}
			
			$htmlForm	.= '<tr>';
			$htmlForm	.= '<td class="col-sm-1 center"><label class="position-relative"><input type="checkbox" '.$checked.' name="codItemSel['.$item["CODIGO"].']" zg-name="selItem" class="ace" value="'.$item["CODIGO"].'" onchange="orcAlteraSel(\''.$item["CODIGO"].'\');" /><span class="lbl"></span></label></td>';
			$htmlForm	.= '<td class="col-sm-2">'.$item["ITEM"].'</td>';
			$htmlForm	.= '<td class="col-sm-1 right"><span>Qtde:&nbsp;</span> <input class="input-mini" id="qtde_'.$item["CODIGO"].'_ID" name="aQtde['.$item["CODIGO"].']" type="text" '.$ro.' zg-tipo="'.$item["TIPO"].'" zg-evento="'.$codTipo.'" zg-codigo="'.$item["CODIGO"].'" zg-name="qtde" maxlength="5" value="'.$qtde.'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero" onchange="orcAlteraQuantidade(\''.$item["CODIGO"].'\');"></td>';
			$htmlForm	.= '<td class="col-sm-1 center"><i class="fa fa-close"></i></td>';
			$htmlForm	.= '<td class="col-sm-2 left"><span>Valor unitário:&nbsp;</span><input class="input-small" id="valor_'.$item["CODIGO"].'_ID" type="text" name="aValor['.$item["CODIGO"].']" value="'.$item["VALOR"].'" zg-codigo="'.$item["CODIGO"].'" zg-evento="'.$codTipo.'" zg-name="valor" autocomplete="off" tabindex="'.$tabIndex.'" zg-data-toggle="mask" zg-data-mask="dinheiro" onchange="orcAlteraValor(\''.$item["CODIGO"].'\');"></td>';
			$htmlForm	.= '<td class="col-sm-2"><span>Total:&nbsp;</span><span zg-total-item="1" id="total_'.$item["CODIGO"].'_ID">'.\Zage\App\Util::to_money($item["TOTAL"]).'</span></td>';
			$htmlForm	.= '<td class="col-sm-1"><span style="cursor: pointer;" onclick="orcHabilitaObs(\''.$item["CODIGO"].'\');"><i class="fa fa-commenting-o"><i></span></td>';
			$htmlForm	.= '</tr>';
			$htmlForm	.= '<tr class="hidden" id="trOrcObs_'.$item["CODIGO"].'_ID">';
			$htmlForm	.= '<td colspan="7"><textarea rows="3" class="col-sm-6 pull-right" name="aObs['.$item["CODIGO"].']" onchange="orcAlteraObs(\''.$item["CODIGO"].'\');">'.$item["OBS"].'</textarea></td>';
			$htmlForm	.= '</tr>';
				
			$tabIndex++;
			$totalTipo	+= $item["TOTAL"];
			
		}
		$htmlForm	.= '</table>';
		$htmlForm	.= '</div>';
	}
	
	$htmlForm	.= '</div>';
	
	
	$htmlForm	.= '<div class="panel-footer">';
	$htmlForm	.= '<span>Total do item '.$aItem["DESCRICAO"].': </span>&nbsp;<span id="totalEvento_'.$codTipo.'_ID" zg-total-evento="'.\Zage\App\Util::formataFloatJquery($totalTipo).'" >'.\Zage\App\Util::to_money($totalTipo).'</span>';
	$htmlForm	.= '</div>';

	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
}

$htmlForm	.= '</div>';
$htmlForm	.= '</center>';
$htmlForm	.= '<script>';
$htmlForm	.= "$('[zg-data-toggle=\"mask\"]').each(function( index ) {
	zgMask($( this ), $( this ).attr('zg-data-mask'));
});
";
$htmlForm	.= '</script>';

echo $htmlForm;
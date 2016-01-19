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
global $em,$tr,$system,$log;

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
if (isset($_GET['numFormando'])) 		$numFormando			= \Zage\App\Util::antiInjection($_GET['numFormando']);
if (isset($_GET['numConvidado'])) 		$numConvidado			= \Zage\App\Util::antiInjection($_GET['numConvidado']);

if (!isset($codPlanoOrc)) exit;

#################################################################################
## Resgatar os dados
#################################################################################
$itens		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codPlano' => $codPlanoOrc,'indAtivo' => 1),array('ordem' => 'ASC'));

#################################################################################
## Verificar se o orçamento tem algum item
#################################################################################
if (sizeof($itens) == 0)	{
	die ("Configure o Plano orçamentário antes de usá-lo");
}

#################################################################################
## Resgata as informações da organização
#################################################################################
$oOrg 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Verifica se as configurações do cerimonial estão OK
## e Verifica o perfil do usuário para fazer as limitações do vendedor
#################################################################################
$souVendedor			= \Zage\Seg\Usuario::ehVendedor($system->getCodOrganizacao(), $system->getCodUsuario());
$indVenDesPad			= 1;
$indVenDarCor			= 1;
$indVenAltObs			= 1;

if ($oOrg->getCodTipo()->getCodigo() == "FMT") {
	$oFmtAdm	= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
	if ($oFmtAdm) {
		$orgCer				= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $oFmtAdm->getCodigo()));
		if ($orgCer && $souVendedor == true) {
			$indVenDesPad		= ($orgCer->getIndVendedorDesmarcarPadrao()	=== 0) ? 0 : 1;
			$indVenDarCor		= ($orgCer->getIndVendedorDarCortesia()		=== 0) ? 0 : 1;
			$indVenAltObs		= ($orgCer->getIndVendedorAlterarObs()		=== 0) ? 0 : 1;
		}
	}
}


#################################################################################
## Carrega o orçamento salvo
#################################################################################
$orcItens		= $em->getRepository('Entidades\ZgfmtOrcamentoItem')->findBy(array('codOrcamento' => $codVersao));

#################################################################################
## Monta um array com os itens salvos
#################################################################################
$aOrcItens		= array();
$orcSalvo		= (sizeof($orcItens) > 0) ? 1 : 0;
for ($i = 0; $i < sizeof($orcItens); $i++) {
	$item		= $orcItens[$i]->getCodItem();
	$codTipo	= $item->getCodGrupoItem()->getCodigo();
	$codigo		= $item->getCodigo();
	$aOrcItens[$codigo]["QTDE"]			= $orcItens[$i]->getQuantidade();
	$aOrcItens[$codigo]["VALOR"]		= \Zage\App\Util::formataDinheiro($orcItens[$i]->getValorUnitario());
	$aOrcItens[$codigo]["DESCRITIVO"]	= ($orcItens[$i]->getTextoDescritivo()) ? $orcItens[$i]->getTextoDescritivo() : $item->getTextoDescritivo() ;
	$aOrcItens[$codigo]["TOTAL"]		= \Zage\App\Util::to_float($orcItens[$i]->getQuantidade() * \Zage\App\Util::to_float($orcItens[$i]->getValorUnitario()));
	$aOrcItens[$codigo]["COD_CORT"]		= ($orcItens[$i]->getCodTipoCortesia()) ? $orcItens[$i]->getCodTipoCortesia()->getCodigo() : null ;
}


#################################################################################
## Montar o array com as informações do Plano
#################################################################################
$aItens		= array(); 
for ($i = 0; $i < sizeof($itens); $i++) {
	$codTipo		= $itens[$i]->getCodGrupoItem()->getCodigo();
	$codigo			= $itens[$i]->getCodigo();
	$valorPadrao	= ($itens[$i]->getValorPadrao()) ? \Zage\App\Util::formataDinheiro($itens[$i]->getValorPadrao()) 	: null;
	$pctMaxDesconto	= ($itens[$i]->getPctMaxDescontoVendedor() !== null) ? \Zage\App\Util::to_float($itens[$i]->getPctMaxDescontoVendedor()) : null;
	
	
	$aItens[$codTipo]["DESCRICAO"]						= $itens[$i]->getCodGrupoItem()->getDescricao();
	$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 		= $itens[$i]->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 		= $itens[$i]->getCodTipoItem()->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 		= $itens[$i]->getItem();
	$aItens[$codTipo]["ITENS"][$codigo]["VALOR_PADRAO"]	= $valorPadrao;
	$aItens[$codTipo]["ITENS"][$codigo]["PADRAO"] 		= $itens[$i]->getIndPadrao();
	
	if ($valorPadrao && ($pctMaxDesconto !== null) && ($souVendedor == true)) {
		$pctMin		= (100 - $pctMaxDesconto);
		if ($pctMin < 0) $pctMin = 0;
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR_MINIMO"]		= \Zage\App\Util::to_float(round($valorPadrao * $pctMin/100,2));
	}else{
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR_MINIMO"]		= null;
	}
	
	if (isset($aOrcItens[$codigo])) {
		$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= $aOrcItens[$codigo]["QTDE"];
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 		= $aOrcItens[$codigo]["VALOR"];
		$aItens[$codTipo]["ITENS"][$codigo]["DESCRITIVO"] 	= $aOrcItens[$codigo]["DESCRITIVO"];
		$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"]		= $aOrcItens[$codigo]["TOTAL"];
		$aItens[$codTipo]["ITENS"][$codigo]["COD_CORT"]		= $aOrcItens[$codigo]["COD_CORT"];
		$aItens[$codTipo]["ITENS"][$codigo]["SALVO"]		= 1;
		//$aItens[$codTipo]["ITENS"][$codigo]["PADRAO"] 		= null;
	}else{

		
		if ($aItens[$codTipo]["ITENS"][$codigo]["TIPO"] == "F") {
			$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= ($numFormando) ? $numFormando : 1;
		}elseif ($aItens[$codTipo]["ITENS"][$codigo]["TIPO"] == "C") {
			$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= ($numFormando && $numConvidado) ? ($numFormando * $numConvidado) : 1;
		}else{
			$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= ($valorPadrao) ? 1 : null;
		}
		
		$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 		= ($orcSalvo == 0) ? $valorPadrao : null;
		$aItens[$codTipo]["ITENS"][$codigo]["DESCRITIVO"] 	= $itens[$i]->getTextoDescritivo();
		$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"] 		= ($itens[$i]->getIndPadrao() && $valorPadrao) ? ($aItens[$codTipo]["ITENS"][$codigo]["QTDE"] * $valorPadrao) : 0;
		$aItens[$codTipo]["ITENS"][$codigo]["COD_CORT"]		= null;
		$aItens[$codTipo]["ITENS"][$codigo]["SALVO"]		= 0;
		//$aItens[$codTipo]["ITENS"][$codigo]["PADRAO"] 		= $itens[$i]->getIndPadrao();
	}
}


//print_r($aItens);
//exit;

#################################################################################
## Resgata as informações de Cortesia do Orçamento
#################################################################################
try {
	$aCortesia	= $em->getRepository('Entidades\ZgfmtOrcamentoCortesiaTipo')->findAll();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

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
		$htmlForm	.= '<div class="col-sm-12" align="center">';
		$htmlForm	.= '<table id="tabItem_'.$codItem.'_ID" zg-table-orc="1" class="table table-hover table-condensed">';
		$totalTipo	= 0;
		
		foreach ($tipoItens as $codItem => $item) {
			
			if ($item["TIPO"] == "UN") {
				$ro		= null;
				$qtde	= ($item["QTDE"]) ? $item["QTDE"] : null;
				$qTab	= $tabIndex;
				$tabIndex++;
			}else{
				$ro		= "readonly";
				$qtde	= ($item["QTDE"]) ? $item["QTDE"] : 1;
				$qTab	= null;
			}
			
			if (isset($item["VALOR"]) && $item["VALOR"] && $item["SALVO"] == 1) {
				$checked	= 'checked="checked"';
			//}elseif (isset($item["VALOR"]) && $item["VALOR"] && $item["SALVO"] == 0 && $item["PADRAO"]) {
//				$checked	= 'checked="checked"';
			}else{
				$checked	= ($item["PADRAO"] && $orcSalvo == 0) ? 'checked="checked"' : null;
				$hidObs		= "";
			}
			
			if ((isset($item["COD_CORT"]) && $item["COD_CORT"] && ($item["VALOR"] == 0)) || (($item["VALOR"] == 0) && ($item["PADRAO"]))) {
				$hidObs		= "";
			}else{
				$hidObs		= "hidden";
			}
			
			if ($item["DESCRITIVO"]) {
				$iconDesc		= "blue";
				$btnDesc		= "btn-white";
			}else{
				$iconDesc		= "grey";
				$btnDesc		= "btn-light";
			}
			
			
			if ($indVenAltObs 	== 0) {
				$roObs			= "readonly";
			}else{
				$roObs			= "";
			}
			
			$log->info("Item: ".$item["ITEM"]." Valor Padrão: ".$item["VALOR_PADRAO"]);
			
			if (($item["PADRAO"] == 1) && ($indVenDesPad	== 0)) {
				$disChkClass	= "readonly disabled";
				$disChk			= 'readonly="readonly"';
				$disChkStyle	= 'style="color: grey; background-color: #F0F0F0;"';
			}else{
				$disChk			= "";
				$disChkClass	= "";
				$disChkStyle	= "";
			}
			
			if ($item["VALOR_MINIMO"] !== null) {
				$tagValMin			= 'zg-val-minimo="'.\Zage\App\Util::formataDinheiro($item["VALOR_MINIMO"]).'"';
				if (($indVenDarCor === 0) && ($souVendedor == true)) {
					$tagDarCor			= 'zg-pode-dar-cortesia="0"';
				}else if ($souVendedor 	== true) {
					$tagDarCor			= 'zg-pode-dar-cortesia="1"';
				}else{
					$tagDarCor			= '';
				}
				 
			}else{
				$tagValMin			= "";
				$tagDarCor			= "";
			}
			
			$log->info("Padrão: ".$item["PADRAO"]." ValorPadrão: ".$item["VALOR_PADRAO"]." Valor Mínimo: ".$item["VALOR_MINIMO"]." IndVenDarCor: ".$indVenDarCor);
			if ($item["PADRAO"] && $item["VALOR_PADRAO"] && ($item["VALOR_MINIMO"] == $item["VALOR_PADRAO"]) && $indVenDarCor == 0) {
				$roValor	= "readonly";
			}else{
				$roValor	= "";
			}
			
			
			#################################################################################
			## Resgata as informações de Cortesia do Orçamento
			#################################################################################
			try {
				$oCortesia	= $system->geraHtmlCombo($aCortesia,'CODIGO', 'DESCRICAO', $item["COD_CORT"], null);
			} catch (\Exception $e) {
				\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
			}

			$htmlForm	.= '<tr id="trorc_'.$item["CODIGO"].'_ID">';
			$htmlForm	.= '<td class="col-sm-1 center"><label class="position-relative"><input type="checkbox" '.$checked.' name="codItemSel['.$item["CODIGO"].']" '.$disChkStyle.' zg-name="selItem" class="ace '.$disChkClass.'" '.$disChk.' value="'.$item["CODIGO"].'" onchange="orcAlteraSel(\''.$item["CODIGO"].'\');" /><span class="lbl"></span></label></td>';
			$htmlForm	.= '<td class="col-sm-2">'.$item["ITEM"].'</td>';
			$htmlForm	.= '<td class="col-sm-2 right"><span>Qtde:&nbsp;</span> <input class="input-mini" id="qtde_'.$item["CODIGO"].'_ID" name="aQtde['.$item["CODIGO"].']" type="text" '.$ro.' zg-tipo="'.$item["TIPO"].'" zg-evento="'.$codTipo.'" zg-codigo="'.$item["CODIGO"].'" zg-name="qtde" maxlength="5" tabindex="'.$qTab.'" value="'.$qtde.'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero" onchange="orcAlteraQuantidade(\''.$item["CODIGO"].'\');"></td>';
			$htmlForm	.= '<td class="col-sm-1 center"><i class="fa fa-close"></i></td>';
			$htmlForm	.= '<td class="col-sm-2 left"><span>Valor unitário:&nbsp;</span><input class="input-small" id="valor_'.$item["CODIGO"].'_ID" type="text" name="aValor['.$item["CODIGO"].']" '.$roValor.' value="'.$item["VALOR"].'" zg-valor-padrao="'.$item["VALOR_PADRAO"].'" '.$tagDarCor.' '.$tagValMin.' zg-codigo="'.$item["CODIGO"].'" zg-evento="'.$codTipo.'" zg-name="valor" autocomplete="off" tabindex="'.$tabIndex.'" zg-data-toggle="mask" zg-data-mask="dinheiro" onchange="orcAlteraValor(\''.$item["CODIGO"].'\',true);"></td>';
			$htmlForm	.= '<td class="col-sm-2">
								<div data-toggle="buttons" class="btn-group btn-overlap">
									<span class="btn btn-sm '.$btnDesc.' btn-info center pull-left" id="span-desc-'.$item["CODIGO"].'_ID" onclick="orcHabilitaObs(\''.$item["CODIGO"].'\');"><i id="icon-desc-'.$item["CODIGO"].'_ID"  class="fa fa-commenting-o bigger-150 '.$iconDesc.'"></i></span>
									&nbsp;
									<select id="selCortesia_'.$item["CODIGO"].'_ID" class="select2 '.$hidObs.'" name="codTipoCortesia['.$item["CODIGO"].']" data-rel="select2" onchange="orcAlteraCortesia($(this));">'.$oCortesia.'</select>
								</div>
							</td>';
			$htmlForm	.= '<td class="col-sm-2"><span>Total:&nbsp;</span><span zg-total-item="1" id="total_'.$item["CODIGO"].'_ID">'.\Zage\App\Util::to_money($item["TOTAL"]).'</span></td>';
			$htmlForm	.= '</tr>';
			$htmlForm	.= '<tr class="hidden" id="trOrcObs_'.$item["CODIGO"].'_ID">';
			$htmlForm	.= '<td colspan="8"><textarea maxlength="1000" rows="3" class="col-sm-6 pull-right '.$roObs.'" '.$roObs.' zg-name="aObs" name="aObs['.$item["CODIGO"].']" onchange="orcAlteraObs(\''.$item["CODIGO"].'\');">'.$item["DESCRITIVO"].'</textarea></td>';
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
$('[name*=\"codTipoCortesia\"').select2({allowClear:false,width: 'resolve'});
		
$('input[type=\"checkbox\"][readonly=\"readonly\"]').click(function(e){
    e.preventDefault();
});
		
";
$htmlForm	.= '</script>';

echo $htmlForm;
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
if (isset($_GET['codVersaoOrc'])) 		$codVersaoOrc			= \Zage\App\Util::antiInjection($_GET['codVersaoOrc']);

if (!isset($codVersaoOrc)) exit;

#################################################################################
## Resgatar os dados
#################################################################################
$itens		= $em->getRepository('Entidades\ZgfmtPlanoOrcItem')->findBy(array('codVersao' => $codVersaoOrc,'indAtivo' => 1));

#################################################################################
## Verificar se o orçamento tem algum item
#################################################################################
if (sizeof($itens) == 0)	{
	die ("Configure o Plano orçamentário antes de usá-lo");
}

#################################################################################
## Montar o array com as informações do Plano
#################################################################################
$aItens		= array(); 
for ($i = 0; $i < sizeof($itens); $i++) {
	$codTipo		= $itens[$i]->getCodTipoEvento()->getCodigo();
	$codigo		= $itens[$i]->getCodigo();
	$aItens[$codTipo]["DESCRICAO"]			= $itens[$i]->getCodTipoEvento()->getDescricao();
	$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 	= $itens[$i]->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 	= $itens[$i]->getCodTipoItem()->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 	= $itens[$i]->getItem();
}

//print_r($aItens);
//exit;

#################################################################################
## Cria o html dinâmico 
#################################################################################
$tabIndex	= 101;
$htmlForm	= '';
$htmlForm	.= '<h4 align="center"><b>Detalhes do evento</b></h4>';
$htmlForm	.= '<br>';
$htmlForm	.= '<center>';
$htmlForm	.= '<div id="itensOrcamentoID" class="panel-group accordion-style1" style="width: 98%;">';

foreach ($aItens as $codTipo => $aItem)	{
	$htmlForm	.= '<div class="panel panel-default">';
	$htmlForm	.= '<div class="panel-heading">';
	$htmlForm	.= '<a href="#itemTipo_'.$codTipo.'_ID" data-parent_old="#itensOrcamentoID" data-toggle="collapse" aria-expanded="true" aria-controls="collapseThree" class="accordion-toggle collapsed">';
	//$htmlForm	.= '<i class="ace-icon fa fa-chevron-left pull-right" data-icon-hide="ace-icon fa fa-chevron-down" data-icon-show="ace-icon fa fa-chevron-left"></i>';
	$htmlForm	.= '<i class="ace-icon fa fa-chevron-right pull-left" data-icon-hide="ace-icon fa fa-chevron-down" data-icon-show="ace-icon fa fa-chevron-right"></i>';
	$htmlForm	.= '&nbsp;<label style="text-align: center;">'.$aItem["DESCRICAO"].'</label>';
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
		$htmlForm	.= '<table id="tabItem_'.$codItem.'_ID" class="table table-hover table-condensed">';
		
		foreach ($tipoItens as $codItem => $item) {
			
			if ($item["TIPO"] == "UN") {
				$ro		= null;
				$valor	= null;
			}else{
				$ro		= "readonly";
				$valor	= 1;
			}
			
			$htmlForm	.= '<tr>';
			$htmlForm	.= '<td class="col-sm-2">'.$item["ITEM"].'</td>';
			$htmlForm	.= '<td class="col-sm-1 right"><span>Qtde:&nbsp;</span> <input class="input-mini" id="qtde_'.$item["CODIGO"].'_ID" type="text"'.$ro.' zg-tipo="'.$item["TIPO"].'" zg-evento="'.$codTipo.'" zg-codigo="'.$item["CODIGO"].'" zg-name="qtde" maxlength="5" value="'.$valor.'" autocomplete="off" zg-data-toggle="mask" zg-data-mask="numero" onchange="orcAtualizaTotalItem(\''.$item["CODIGO"].'\');"></td>';
			$htmlForm	.= '<td class="col-sm-1 center"><i class="fa fa-close"></i></td>';
			$htmlForm	.= '<td class="col-sm-2 left"><span>Valor unitário:&nbsp;</span><input class="input-small" id="valor_'.$item["CODIGO"].'_ID" type="text" zg-codigo="'.$item["CODIGO"].'" zg-evento="'.$codTipo.'" zg-name="valor" autocomplete="off" tabindex="'.$tabIndex.'" zg-data-toggle="mask" zg-data-mask="dinheiro" onchange="orcAtualizaTotalItem(\''.$item["CODIGO"].'\');"></td>';
			$htmlForm	.= '<td class="col-sm-2"><span>Total:&nbsp;</span><span zg-total-item="1" id="total_'.$item["CODIGO"].'_ID">R$ 0,00</span></td>';
			$htmlForm	.= '</tr>';
			$tabIndex++;
			
		}
		$htmlForm	.= '</table>';
		$htmlForm	.= '</div>';
	}
	
	$htmlForm	.= '</div>';
	
	$htmlForm	.= '<div class="panel-footer">';
	$htmlForm	.= '<span>Total do item '.$aItem["DESCRICAO"].': </span>&nbsp;<span id="totalEvento_'.$codTipo.'_ID" zg-total-evento="" >R$ 0,00</span>';
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
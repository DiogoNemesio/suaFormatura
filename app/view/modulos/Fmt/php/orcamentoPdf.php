<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

use \H2P\Converter\PhantomJS;
use \H2P\TempFile;
use \H2P\Request;
use \H2P\Request\Cookie;

#################################################################################
## Variáveis globais
#################################################################################
global $system,$log,$_user,$em,$tr;

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
if (isset($_GET['codVersaoOrc'])) 		$codVersaoOrc		= \Zage\App\Util::antiInjection($_GET['codVersaoOrc']);

#################################################################################
## Valida os parâmetros
#################################################################################
if (!isset($codVersaoOrc) || (!$codVersaoOrc)) 	\Zage\App\Erro::halt('Parâmetro incorreto');

#################################################################################
## Resgata as informações do orçamento
#################################################################################
$orcamento			= $em->getRepository('Entidades\ZgfmtOrcamento')->findOneBy(array('codigo' => $codVersaoOrc));
$codPlanoOrc		= $orcamento->getCodPlanoOrc()->getCodigo();
$numFormandos		= $orcamento->getQtdeFormandos();
$numConvidados		= $orcamento->getQtdeConvidados();
$indAceite			= $orcamento->getIndAceite();


#################################################################################
## Resgata as informações da Turma
#################################################################################
try {
	$oOrgFmt	= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$ident			= $oOrg->getIdentificacao();
$nome			= $oOrg->getNome();
//$instituicao	= $oOrgFmt->getCodInstituicao()->getCodigo();
//$curso			= $oOrgFmt->getCodCurso()->getCodigo();
//$cidade			= $oOrgFmt->getCodCidade()->getCodigo();
$numMeses		= $orcamento->getNumMeses();
$dataConclusao	= ($oOrgFmt->getDataConclusao() != null) ? $oOrgFmt->getDataConclusao()->format($system->config["data"]["dateFormat"]) : null;


#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio();

#################################################################################
## Criação do cabeçalho
#################################################################################
//$rel->adicionaCabecalho("Orçamento");

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Monta os dados iniciais
#################################################################################
$html		= '<h3 align="center"><b>'.$nome.'</b></h3>';
$html		.= '<table align="center" class="table table-condensed" style="width: 70%; align: center;">';
$html		.= '<thead>';
$html		.= '<tr>
					<th style="text-align: center;"><strong>Número de Formandos</strong></th>
					<th style="text-align: center;"><strong>Convites por formando</strong></th>
					<th style="text-align: center;"><strong>Número de Pessoas</strong></th>
				</tr>';
$html		.= '</thead><tbody>';
$html		.= '<tr>
					<th style="text-align: center;">'.$numFormandos.'</th>
					<th style="text-align: center;">'.$numConvidados.'</th>
					<th style="text-align: center;">'.($numFormandos * $numConvidados).'</th>
				</tr>
				</tbody></table>';

/*$html		.= '<tr>
					<th style="text-align: left;"><strong>Número de Formandos:</strong></th>
					<th style="text-align: left;">'.$numFormandos.'</th>
				</tr>
				<tr>
					<th style="text-align: left;"><strong>Número de Convites por formando:</strong></th>
					<th style="text-align: left;">'.$numConvidados.'</th>
				</tr>
				<tr>
					<th style="text-align: left;"><strong>Número de Pessoas:</strong></th>
					<th style="text-align: left;">'.($numFormandos * $numConvidados).'</th>
				</tr>
				<tr>
					<th style="text-align: left;"><strong>Data de Conclusão:</strong></th>
					<th style="text-align: left;">'.$dataConclusao.' ('.$numMeses.' meses previstos)</th>
				</tr>
				</table>
				<br>
				';
*/				

#################################################################################
## Carrega o orçamento salvo
#################################################################################
$orcItens		= $em->getRepository('Entidades\ZgfmtOrcamentoItem')->findBy(array('codOrcamento' => $codVersaoOrc));

#################################################################################
## Monta um array com os itens salvos
#################################################################################
$aItens			= array();
for ($i = 0; $i < sizeof($orcItens); $i++) {
	$item		= $orcItens[$i]->getCodItem();
	$codTipo	= $item->getCodGrupoItem()->getCodigo();
	$codigo		= $item->getCodigo();
	$aItens[$codTipo]["DESCRICAO"]						= $item->getCodGrupoItem()->getDescricao();
	$aItens[$codTipo]["ITENS"][$codigo]["CODIGO"] 		= $item->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["TIPO"] 		= $item->getCodTipoItem()->getCodigo();
	$aItens[$codTipo]["ITENS"][$codigo]["ITEM"] 		= $item->getItem();
	$aItens[$codTipo]["ITENS"][$codigo]["QTDE"] 		= $orcItens[$i]->getQuantidade();
	$aItens[$codTipo]["ITENS"][$codigo]["VALOR"] 		= \Zage\App\Util::formataDinheiro($orcItens[$i]->getValorUnitario());
	$aItens[$codTipo]["ITENS"][$codigo]["OBS"] 			= $orcItens[$i]->getObservacao();
	$aItens[$codTipo]["ITENS"][$codigo]["TOTAL"]		= \Zage\App\Util::to_float($orcItens[$i]->getQuantidade() * \Zage\App\Util::to_float($orcItens[$i]->getValorUnitario()));
	
}


//print_r($aItens);
//exit;

#################################################################################
## Cria o html dinâmico
#################################################################################
$htmlForm	= '';
$htmlForm	.= '<h5 align="center"><b>Detalhes dos eventos</b></h5>';
$htmlForm	.= '<center>';
//$htmlForm	.= '<div id="itensOrcamentoID" style="width: 98%;">';

$w1			= "width: 30%;";
$w2			= "width: 20%;";
$w3			= "width: 20%;";
$w4			= "width: 20%;";

$aTotal		= array();
$valorTotal	= 0;

foreach ($aItens as $codTipo => $aItem)	{
	$htmlForm	.= '<h5 align="left"><b>'.$aItem["DESCRICAO"].'</b></h5>';

	#################################################################################
	## Montar a tabela de itens
	#################################################################################
	$tipoItens	= $aItem["ITENS"];
	if (sizeof($tipoItens) > 0) {
		$htmlForm	.= '<div align="center">';
		$htmlForm	.= '<table class="table table-bordered1"><thead>';
		$htmlForm	.= '<tr>';
		$htmlForm	.= '<th style="text-align: left; '.$w1.' border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">ITEM</th>';
		$htmlForm	.= '<th style="text-align: center; '.$w2.' border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Quantidade</th>';
		$htmlForm	.= '<th style="text-align: right; '.$w3.' border-bottom: 1px solid #000000; border-top: 1px solid #000000;">VALOR</th>';
		$htmlForm	.= '<th style="text-align: right; '.$w4.' border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">TOTAL</th>';
		$htmlForm	.= '</tr>';
		$htmlForm	.= '</thead><tbody>';
		
		$totalTipo	= 0;

		foreach ($tipoItens as $codItem => $item) {
			
			if ($item["OBS"]) {
				$bdBottom	= null;
			}else{
				$bdBottom	= "border-bottom: 1px solid #000000;";
			}
			
			$htmlForm	.= '<tr>';
			$htmlForm	.= '<td style="text-align: left; '.$w1.' border-left: 1px solid #000000; '.$bdBottom.' border-top: 1px solid #000000;">'.$item["ITEM"].'</td>';
			$htmlForm	.= '<td style="text-align: center; '.$w2.' '.$bdBottom.' border-top: 1px solid #000000;">'.$item["QTDE"].' </td>';
			$htmlForm	.= '<td style="text-align: right; '.$w3.' '.$bdBottom.' border-top: 1px solid #000000;">'.\Zage\App\Util::to_money($item["VALOR"]).'</td>';
			$htmlForm	.= '<td style="text-align: right; '.$w4.' border-right: 1px solid #000000; '.$bdBottom.' border-top: 1px solid #000000;">'.\Zage\App\Util::to_money($item["TOTAL"]).'</td>';
			$htmlForm	.= '</tr>';
			
			if ($item["OBS"]) {
				$htmlForm	.= '<tr>';
				$htmlForm	.= '<td style="border-bottom: 1px solid #000000; border-right: 1px solid #000000; border-left: 1px solid #000000;" colspan="4">'.$item["OBS"].'</td>';
				$htmlForm	.= '</tr>';
			}
			$totalTipo	+= $item["TOTAL"];
			$valorTotal	+= $item["TOTAL"];
		}
		$htmlForm	.= '</tbody>';
		//$htmlForm	.= '<tfoot>';
		$htmlForm	.= '<tr><th style="text-align: right;" colspan="3">Total: </th>';
		$htmlForm	.= '<th style="text-align: right; '.$w4.'">'.\Zage\App\Util::to_money($totalTipo).'</th></tr>';
		//$htmlForm	.= '</tfoot>';
		$htmlForm	.= '</tbody>';
		$htmlForm	.= '</table></div>';
		$aTotal[$aItem["DESCRICAO"]]["VALOR"]	= $totalTipo;
		$aTotal[$aItem["DESCRICAO"]]["EVENTO"]	= $aItem["DESCRICAO"];
	}

	//$htmlForm	.= '</div>';
}

$htmlForm	.= '<br>';
$htmlForm	.= '<table class="table table-bordered1" style="width: 50%;"><thead>';
$htmlForm	.= '<tr><th style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Evento</th><th style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">Valor do Evento</th></tr>';
$htmlForm	.= '</thead>';
$htmlForm	.= '<tbody>';
foreach ($aTotal as $evento) {
	$htmlForm	.= '<tr><td style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">'.$evento["EVENTO"].'</td><td style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">'.\Zage\App\Util::to_money($evento["VALOR"]).'</td></tr>';	
}
$htmlForm	.= '</tbody>';
$htmlForm	.= '<tfoot>';
$htmlForm	.= '<tr><th style="text-align: left; border-left: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Valor Total</th><th style="text-align: right; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">'.\Zage\App\Util::to_money($valorTotal).'</th></tr>';	
$htmlForm	.= '</tfoot>';
$htmlForm	.= '</table>';

$html		.= $htmlForm;

$rel->WriteHTML($html);
$rel->Output("Orcamento.pdf",'D');

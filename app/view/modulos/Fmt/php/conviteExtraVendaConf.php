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
//$system->checaPermissao($_codMenu_);

if (isset($_GET['codVenda'])){
	$codVenda		= \Zage\App\Util::antiInjection($_GET['codVenda']);
}

#################################################################################
## Resgatar informações da venda
#################################################################################
$infoVenda = $em->getRepository('Entidades\ZgfmtConviteExtraVendaItem')->findBy(array('codVenda' => $codVenda));

// Cálculo do valor total
$valorTotal = $infoVenda[0]->getCodVenda()->getValorTotal() + $infoVenda[0]->getCodVenda()->getTaxaConveniencia();

#################################################################################
## Resgatar informações da conta
#################################################################################
$infoContaRec = $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codTransacao' => $infoVenda[0]->getCodVenda()->getCodTransacao()));

$total = 0;
for ($i = 0; $i < sizeof($infoVenda); $i++) {
	//$total = $infoVenda[$i]->getCodRifa()->getValorUnitario() + $total;
	$linha = $i + 1;
	
	$html .= '<tr>';
	$html .= '<td class="center">'.$linha.'</td>';
	$html .= '<td class="center">'.$infoVenda[$i]->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>';
	$html .= '<td class="center">'.$infoVenda[$i]->getQuantidade().'</td>';
	$html .= '<td class="center">'.\Zage\App\Util::to_money($infoVenda[$i]->getValorUnitario()).'</td>';
	$html .= '</tr>';
}

$html 	.= "<tr><td colspan='3' align=\"right\">TAXA DE CONVENIÊNCIA</td><td class=\"center\"><div id='valorConvenienciaID'>".\Zage\App\Util::to_money($infoVenda[0]->getCodVenda()->getTaxaConveniencia())."</div></td>";
$html 	.= "<tr><td colspan='3' align=\"right\"><strong>TOTAL</strong></td><td class=\"center\"><div id='valorTotalID' name='valorTotal'><strong>".\Zage\App\Util::to_money($infoVenda[0]->getCodVenda()->getValorTotal())."</strong></div></td></table>";

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('IC'					,$_icone_);

$tpl->set('DATA_VENCIMENTO'		,$infoContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]));
$tpl->set('FORMA_PAGAMENTO'		,$infoVenda[0]->getCodVenda()->getCodFormaPagamento()->getDescricao());
$tpl->set('CONTA_RECEBIMENTO'	,$infoVenda[0]->getCodVenda()->getCodContaRecebimento()->getNome());
$tpl->set('COD_VENDA'			,$codVenda);
$tpl->set('TOTAL'				,\Zage\App\Util::to_money($valorTotal));
$tpl->set('DATA_VENDA'			,$infoVenda[0]->getCodVenda()->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]));

$tpl->set('NOME'				,$infoVenda[0]->getCodVenda()->getCodFormando()->getNome());
$tpl->set('EMAIL'				,$infoVenda[0]->getCodVenda()->getCodFormando()->getEmail());
$tpl->set('HTML_TABLE'			,$html);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


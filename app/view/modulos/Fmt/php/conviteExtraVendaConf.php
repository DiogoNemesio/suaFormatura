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
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgatar informações da venda
#################################################################################
if (isset($_GET['codVenda'])){
	$codVenda		= \Zage\App\Util::antiInjection($_GET['codVenda']);
}

$infoVenda	 	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findOneBy(array('codigo' => $codVenda));
$infoItemVenda 	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaItem')->findBy(array('codVenda' => $codVenda));

// Cálculo do valor total
$valorTotal = $infoVenda->getValorTotal() + $infoVenda->getTaxaConveniencia();

#################################################################################
## Resgatar informações da conta
#################################################################################
$infoContaRec = $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codTransacao' => $infoItemVenda[0]->getCodVenda()->getCodTransacao()));

#################################################################################
## Verificar se pode imprimir boleto
#################################################################################
if($infoContaRec && \Zage\Fin\ContaReceber::podeEmitirBoleto($infoContaRec) == true){
	//URL da geração de boleto
	$eid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$infoContaRec->getCodigo().'&tipoMidia=EMAIL'.'&email='.$infoItemVenda[0]->getCodVenda()->getCodFormando()->getEmail().'&instrucao=');
	$pid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$infoContaRec->getCodigo().'&tipoMidia=PDF'.'&instrucao=');
		
	$urlEmail 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $eid;
	$urlPdf 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $pid;
		
	$htmlBol = '<div data-toggle="buttons" class="btn-group btn-corner pull-right col-sm-2">
					<span class="btn btn-white btn-info center" id="btnOrcPrintID" onclick="javascript:zgDownloadUrl(\''.$urlPdf.'\');" data-rel="tooltip" data-placement="top" data-original-title="Salve o Boleto para poder imprimir" title="">
						<i class="fa fa-file-pdf-o bigger-120" id="icOrcPrintID"></i>
					</span>
					<span class="btn btn-white btn-info center" id="enviaEmailID" onclick="javascript:enviaEmail(\''.$urlEmail.'\');" data-rel="tooltip" data-placement="top" data-original-title="Enviar boleto por e-mail" title="">
						<i class="fa fa-envelope bigger-120" id="icOrcMailID"></i>
					</span>
				</div>';
	
	$htmlBol .= ' <script>
				function enviaEmail($urlEmail){
					$(\'#enviaEmailID\').html(\'<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i>\');
					$(\'#enviaEmailID\').attr("disabled","disabled");
					$.ajax({
						type:	"GET",
						url:	$urlEmail,
						data:	$(\'zgFormMeuPagID\').serialize(),
						}).done(function( data, textStatus, jqXHR) {
							$.gritter.add({
								title: "Email enviado com sucesso",
								text: "Enviado para o mesmo email utilizado para acessar o portal",
								class_name: "gritter-info gritter-info",
								time: "5000"
							});
	
							$(\'#enviaEmailID\').html(\'<i class="fa fa-envelope bigger-120"></i>\');
							$(\'#enviaEmailID\').attr("disabled",false);
	
						}).fail(function( jqXHR, textStatus, errorThrown) {
							alert("errado");
						});
					}
				</script>';
}

$total = 0;
for ($i = 0; $i < sizeof($infoItemVenda); $i++) {
	//$total = $infoItemVenda[$i]->getCodRifa()->getValorUnitario() + $total;
	$linha = $i + 1;
	
	$html .= '<tr>';
	$html .= '<td class="center">'.$linha.'</td>';
	$html .= '<td class="center">'.$infoItemVenda[$i]->getCodEvento()->getCodTipoEvento()->getDescricao().'</td>';
	$html .= '<td class="center">'.$infoItemVenda[$i]->getQuantidade().'</td>';
	$html .= '<td class="center">'.\Zage\App\Util::to_money($infoItemVenda[$i]->getValorUnitario()).'</td>';
	$html .= '<td class="center">'.\Zage\App\Util::to_money($infoItemVenda[$i]->getQuantidade() * $infoItemVenda[$i]->getValorUnitario()).'</td>';
	$html .= '</tr>';
}

$html 	.= "<tr><td colspan='4' align=\"right\">TAXA DE CONVENIÊNCIA</td><td class=\"center\"><div id='valorConvenienciaID'>".\Zage\App\Util::to_money($infoVenda->getTaxaConveniencia())."</div></td>";

#################################################################################
## Verifica se está vencida
#################################################################################
$vencimento			= $infoContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]);
$numDiasAtraso		= \Zage\Fin\Data::numDiasAtraso($vencimento);
if ($numDiasAtraso > 0 && $infoContaRec->getCodStatus()->getCodigo() == 'A') {
	$vencida 	= 1;
	$statusDesc	= '<span class="label label-danger">
								<i class="ace-icon fa fa-exclamation-triangle bigger-120"></i>
								'.$infoContaRec->getCodStatus()->getDescricao().'
							</span>';
}else{
	$statusDesc	= '<span class="label label-'.$infoContaRec->getCodStatus()->getEstiloNormal().'">
								'.$infoContaRec->getCodStatus()->getDescricao().'
							</span>';
}

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

$tpl->set('STATUS'				,$statusDesc);
$tpl->set('DATA_VENCIMENTO'		,$infoContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]));
$tpl->set('FORMA_PAGAMENTO'		,$infoVenda->getCodFormaPagamento()->getDescricao());
$tpl->set('CONTA_RECEBIMENTO'	,$infoVenda->getCodContaRecebimento()->getNome());
$tpl->set('COD_VENDA'			,$codVenda);
$tpl->set('TOTAL'				,\Zage\App\Util::to_money($valorTotal));
$tpl->set('DATA_VENDA'			,$infoItemVenda[0]->getCodVenda()->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]));

$tpl->set('NOME'				,$infoVenda->getCodFormando()->getNome());
$tpl->set('EMAIL'				,$infoVenda->getCodFormando()->getEmail());
$tpl->set('HTML_TABLE'			,$html);
$tpl->set('HTML_BOL'			,$htmlBol);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


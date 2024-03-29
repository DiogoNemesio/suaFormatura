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
global $system,$em,$tr,$_user;

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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata usuário logado
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$pagamentosAtr	= \Zage\Fmt\Formando::listaPagamentosAtrasados($system->getCodOrganizacao(), $_user->getCpf());
	$pagamentosFut	= \Zage\Fmt\Formando::listaPagamentosAVencer($system->getCodOrganizacao(), $_user->getCpf());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Verifica se precisa mostrar a tabela de pagamentos em atraso
#################################################################################
if (sizeof($pagamentosAtr) == 0) {
	$hidAtr		= 'hidden';
	$tabAtr		= '<tr>
						<td style="text-align: center;" colspan="6"> Nenhum registro encontrado </td>
					</tr>
				';
}else{
	$hidAtr		= '';
	$tabAtr		= '';
}

#################################################################################
## Verifica se precisa mostrar a tabela de pagamentos futuros
#################################################################################
if (sizeof($pagamentosFut) == 0) {
	$tabFut		= '<tr>
						<td style="text-align: center;" colspan="6"> Nenhuma conta disponível </td>
					</tr>
				';
}else{
	$tabFut		= '';
}

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();

#################################################################################
## Popula a tabela de pagamentos em atraso
#################################################################################
$totalAtr	= 0;
for ($i = 0; $i < sizeof($pagamentosAtr); $i++) {
	
	#################################################################################
	## Formatar campos da conta
	#################################################################################
	$codFormaPag		= ($pagamentosAtr[$i]->getCodFormaPagamento() 	!= null) ? $pagamentosAtr[$i]->getCodFormaPagamento()->getCodigo() : null;
	$codContaRec		= ($pagamentosAtr[$i]->getCodConta() 			!= null) ? $pagamentosAtr[$i]->getCodConta() 						: null;
	$vencimento			= ($pagamentosAtr[$i]->getDataVencimento() 		!= null) ? $pagamentosAtr[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
	
	#################################################################################
	## Verificar se a conta está atrasada
	#################################################################################
	$vencBol			= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
	$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento,$vencBol);
	$htmlAtraso			= "<i class='fa fa-check-circle green bigger-120'></i>";
	
	#################################################################################
	## Calcular o valor
	#################################################################################
	if (!$contaRec->getValorJaRecebido($pagamentosAtr[$i]->getCodigo())) {
		$valor				= ($pagamentosAtr[$i]->getValor() + $pagamentosAtr[$i]->getValorJuros() + $pagamentosAtr[$i]->getValorMora() + $pagamentosAtr[$i]->getValorOutros() - $pagamentosAtr[$i]->getValorDesconto() - $pagamentosAtr[$i]->getValorCancelado());
	}else{
		$valor				= \Zage\App\Util::to_float($contaRec->getSaldoAReceber($pagamentosAtr[$i]->getCodigo()));
	}
	
	#################################################################################
	## Calcular o Juros e Mora
	#################################################################################
	$_juros				= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	$_mora				= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($pagamentosAtr[$i]->getCodigo(), date($system->config["data"]["dateFormat"]));
	
	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $pagamentosAtr[$i]->getParcela() . " de ".$pagamentosAtr[$i]->getNumParcelas();
	$juros				= ($_juros + $_mora);
	$totalAtr			+= $valor + $juros;
	$instrucao			= "";
	$email				= ($pagamentosAtr[$i]->getCodPessoa()) ? $pagamentosAtr[$i]->getCodPessoa()->getEmail() : null;
	
	#################################################################################
	## Verificar se pode imprimir boleto
	#################################################################################
	if($pagamentosAtr[$i] && \Zage\Fin\ContaReceber::podeEmitirBoleto($pagamentosAtr[$i]) == true){
		//URL da geração de boleto
		$eid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$pagamentosAtr[$i]->getCodigo().'&tipoMidia=EMAIL'.'&email='.$oUsuario->getUsuario().'&instrucao=');
		$pid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$pagamentosAtr[$i]->getCodigo().'&tipoMidia=PDF'.'&instrucao=');
			
		$urlEmailAtr 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $eid;
		$urlPdfAtr 		= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $pid;
			
		$htmlBol			= '
			<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
				<span class="btn btn-sm btn-white btn-info center" onclick="javascript:zgDownloadUrl(\''.$urlPdfAtr.'\');"><i class="fa fa-file-pdf-o bigger-120"></i></span>
				<span id="enviaEmailAtrID_'.$i.'" class="btn btn-sm btn-white btn-info center" onclick="javascript:enviaEmailAtr('.$i.',\''.$urlEmailAtr.'\');"><i class="fa fa-envelope bigger-120"></i></span>
			</div>
			';
	}else{
		$htmlBol		= '<i class="icon-only ace-icon fa fa-minus bigger-110"></i>';
	}

	#################################################################################
	## Contar tabela
	#################################################################################
	$tabAtr	.= '<tr>
			<td>'.$pagamentosAtr[$i]->getDescricao().'</td>
			<td class="hidden-480" style="text-align: center;">'.$parcela.'</td>
			<td style="text-align: center;">'.$vencimento.'</td>
			<td style="text-align: center;">'.\Zage\App\Util::to_money($valor).'</td>
			<td style="text-align: center;">'.\Zage\App\Util::to_money($juros).'</td>
			<td style="text-align: center;">'.$htmlBol.'</td>
			</tr>
	';
}

$tabAtr .= ' <script>
				function enviaEmailAtr(i,$urlEmailAtr){
					$(\'#enviaEmailAtrID_\'+i).html(\'<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i>\');
					$(\'#enviaEmailAtrID_\'+i).attr("disabled","disabled");
					$.ajax({
						type:	"GET",
						url:	$urlEmailAtr,
						data:	$(\'zgFormMeuPagID\').serialize(),
						}).done(function( data, textStatus, jqXHR) {
							$.gritter.add({
								title: "Email enviado com sucesso",
								text: "Enviado para o mesmo email utilizado para acessar o portal",
								class_name: "gritter-info gritter-info",
								time: "5000"
							});

							$(\'#enviaEmailAtrID_\'+i).html(\'<i class="fa fa-envelope bigger-120"></i>\');
							$(\'#enviaEmailAtrID_\'+i).attr("disabled",false);

						}).fail(function( jqXHR, textStatus, errorThrown) {
							alert("errado");
						});


					}
				</script>
			';

#################################################################################
## Popula a tabela de pagamentos futuros
#################################################################################
$numFut		= sizeof($pagamentosFut);
if ($numFut > 1) $numFut = 1;
for ($i = 0; $i < $numFut; $i++) {

	################################################################################
	## Verificar se pode imprimir boleto
	#################################################################################
	if($pagamentosFut[$i] && \Zage\Fin\ContaReceber::podeEmitirBoleto($pagamentosFut[$i]) == true){
		//URL da geração de boleto
		$eid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$pagamentosFut[$i]->getCodigo().'&tipoMidia=EMAIL'.'&email='.$oUsuario->getUsuario().'&instrucao=');
		$pid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$pagamentosFut[$i]->getCodigo().'&tipoMidia=PDF'.'&instrucao=');
			
		$urlEmailFut 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $eid;
		$urlPdfFut 		= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $pid;
			
		$htmlBol			= '
			<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
				<span class="btn btn-sm btn-white btn-info center" onclick="javascript:zgDownloadUrl(\''.$urlPdfFut.'\');"><i class="fa fa-file-pdf-o bigger-120"></i></span>
				<span id="enviaEmailFutID_'.$i.'" class="btn btn-sm btn-white btn-info center" onclick="javascript:enviaEmailFut('.$i.',\''.$urlEmailFut.'\');"><i class="fa fa-envelope bigger-120"></i></span>
			</div>
			';
	}else{
		$htmlBol		= '<i class="icon-only ace-icon fa fa-minus bigger-110"></i>';
	}
	
	#################################################################################
	## Formatar os campos
	#################################################################################
	$parcela			= $pagamentosFut[$i]->getParcela() . " de ".$pagamentosFut[$i]->getNumParcelas();
	$juros				= 0;
	$vencimento			= $pagamentosFut[$i]->getDataVencimento()->format($system->config["data"]["dateFormat"]);
	$valor				= $pagamentosFut[$i]->getValor();
	
	$tabFut	.= '<tr>
			<td>'.$pagamentosFut[$i]->getDescricao().'</td>
			<td class="hidden-480" style="text-align: center;">'.$parcela.'</td>
			<td style="text-align: center;">'.$vencimento.'</td>
			<td style="text-align: center;">'.\Zage\App\Util::to_money($valor).'</td>
			
			<td style="text-align: center;">'.$htmlBol.'</td>
			</tr>
	';
}

$tabFut .= ' <script>
				function enviaEmailFut(i,$urlEmailFut){
					$(\'#enviaEmailFutID_\'+i).html(\'<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i>\');
					$(\'#enviaEmailFutID_\'+i).attr("disabled","disabled");
					$.ajax({
						type:	"GET",
						url:	$urlEmailFut,
						data:	$(\'zgFormMeuPagID\').serialize(),
						}).done(function( data, textStatus, jqXHR) {
							$.gritter.add({
								title: "Email enviado com sucesso",
								text: "Enviado para o mesmo email utilizado para acessar o portal",
								class_name: "gritter-info gritter-info",
								time: "5000"
							});

							$(\'#enviaEmailFutID_\'+i).html(\'<i class="fa fa-envelope bigger-120"></i>\');
							$(\'#enviaEmailFutID_\'+i).attr("disabled",false);

						}).fail(function( jqXHR, textStatus, errorThrown) {
							alert("errado");
						});


					}
				</script>
			';


#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlHist				= ROOT_URL."/Fin/meuPagamentoHis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('URL_HIST'		,$urlHist);
$tpl->set('HID_ATR'			,$hidAtr);
$tpl->set('HID_FUT'			,$hidFut);
$tpl->set('NUM_FUT'			,$numFut);
$tpl->set('TAB_ATR'			,$tabAtr);
$tpl->set('TAB_FUT'			,$tabFut);
$tpl->set('TOT_ATR'			,\Zage\App\Util::to_money($totalAtr));
$tpl->set('DP'				,ROOT_URL . "/Fin/geraBoletoMidia.php");

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

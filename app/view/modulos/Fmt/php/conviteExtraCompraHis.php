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
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Buscar o usuário para conseguir o email
#################################################################################
$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario())); 

#################################################################################
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();

#################################################################################
## Resgata os dados do grid
#################################################################################
$hidden = null;
$msnCom = null;

try {
	$oCompras	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codFormando' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa($system->getCodUsuario()), 'codOrganizacao' => $system->getCodOrganizacao() ), array('dataCadastro' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if(!$oCompras) {
	$hidden = "hidden";
	$msnCom .= '<div align="center" class="alert alert-info">';
	$msnCom .= '<i class="fa fa-exclamation-triangle bigger-125"></i> Nenhuma compra realizada!';
	$msnCom .= '</div>';
}else{
	for ($i = 0; $i < sizeof($oCompras); $i++) {
		
		#################################################################################
		## Resgata informação da conta de recebimento
		#################################################################################
		$oContaRec	= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codTransacao' => $oCompras[$i]->getCodTransacao()));

		#################################################################################
		## Verificar se pode imprimir boleto
		#################################################################################
		if($oContaRec && \Zage\Fin\ContaReceber::podeEmitirBoleto($oContaRec) == true){
			//URL da geração de boleto
			$eid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$oContaRec->getCodigo().'&tipoMidia=EMAIL'.'&email='.$oUsuario->getUsuario().'&instrucao=');
			$pid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ .'&codConta='.$oContaRec->getCodigo().'&tipoMidia=PDF'.'&instrucao=');
			
			$urlEmail 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $eid;
			$urlPdf 	= ROOT_URL . "/Fin/geraBoletoConta.php?id=" . $pid;
			
			$htmlBol			= '
			<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
				<span class="btn btn-sm btn-white btn-info center" onclick="javascript:zgDownloadUrl(\''.$urlPdf.'\');"><i class="fa fa-file-pdf-o bigger-120"></i></span>
				<span id="enviaEmailID_'.$i.'" class="btn btn-sm btn-white btn-info center" onclick="javascript:enviaEmail('.$i.',\''.$urlEmail.'\');"><i class="fa fa-envelope bigger-120"></i></span>
			</div>
			';
		}else{
			$htmlBol	= '';
 		}
		
		#################################################################################
		## Verifica se está vencida
		#################################################################################
		$vencimento			= $oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]);
		$numDiasAtraso		= \Zage\Fin\Data::numDiasAtraso($vencimento);
		if ($numDiasAtraso > 0 && $oContaRec->getCodStatus()->getCodigo() == 'A') {
			$vencida 	= 1;
			$statusDesc	= '<span class="label label-danger">
								<i class="ace-icon fa fa-exclamation-triangle bigger-120"></i>
								'.$oContaRec->getCodStatus()->getDescricao().'
							</span>';
		}else{
			$statusDesc	= '<span class="label label-'.$oContaRec->getCodStatus()->getEstiloNormal().'">
								'.$oContaRec->getCodStatus()->getDescricao().'
							</span>';
		}
			
		$tabCompra	.= '<tr>
			<td style="text-align: center;">'.$statusDesc.'</td>
			<td class="hidden-480" style="text-align: center;">'.$oContaRec->getCodFormaPagamento()->getDescricao().'</td>
			<td style="text-align: center;">'.$oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]).'</td>
			<td style="text-align: center;">R$ '.\Zage\App\Util::formataDinheiro($oCompras[$i]->getValorTotal()+$oCompras[$i]->getTaxaConveniencia()).'</td>
			<td class="hidden-480" style="text-align: center;">'.$oCompras[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]).'</td>
			<td style="text-align: center;">'.$htmlBol.'</td>
			</tr>';
	}
}

$tabCompra .= ' <script>
				function enviaEmail(i,$urlEmail){
					$(\'#enviaEmailID_\'+i).html(\'<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i>\');
					$(\'#enviaEmailID_\'+i).attr("disabled","disabled");
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
								
							$(\'#enviaEmailID_\'+i).html(\'<i class="fa fa-envelope bigger-120"></i>\');
							$(\'#enviaEmailID_\'+i).attr("disabled",false);
								
						}).fail(function( jqXHR, textStatus, errorThrown) {
							alert("errado");
						});
								
						
					}
				</script>				
			';

if ($vencida == 1){
	$msnVenc = '<div class="well well-sm"> Caso já tenha realizado o pagamento da conta sinalizada como vencida, desconsidere e aguarde a compensação. </div>';
}else{
	$msnVenc = '';
}

#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraCompra.php?id=".$id;

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
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('HIDDEN'			,$hidden);
$tpl->set('TAB_COMPRA'		,$tabCompra);
$tpl->set('MSG_VENCIDO'		,$msnVenc);
$tpl->set('MSG_COM'			,$msnCom);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

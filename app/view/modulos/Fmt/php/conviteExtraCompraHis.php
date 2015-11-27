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
## Instancia o objeto do contas a receber
#################################################################################
$contaRec	= new \Zage\Fin\ContaReceber();

#################################################################################
## Resgata os dados do grid
#################################################################################
$hidden = null;
$msnCom = null;

try {
	$oCompras	= $em->getRepository('Entidades\ZgfmtConviteExtraVenda')->findBy(array('codFormando' => \Zage\Fmt\Convite::getCodigoUsuarioPessoa(), 'codOrganizacao' => $system->getCodOrganizacao() ), array('dataCadastro' => 'ASC'));
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
		## Formatar campos da conta
		#################################################################################
		$podeBol			= true;
		$codFormaPag		= ($oContaRec->getCodFormaPagamento() 	!= null) ? $oContaRec->getCodFormaPagamento()->getCodigo() : null;
		$codContaRec		= ($oContaRec->getCodConta() 			!= null) ? $oContaRec->getCodConta() 						: null;
		$vencimento			= ($oContaRec->getDataVencimento() 		!= null) ? $oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]) : null;
		
		if (!$vencimento) 										$podeBol	= false;
		if ($codFormaPag	!== "BOL")							$podeBol	= false;
		if (!$codContaRec) 										$podeBol	= false;
		if ($codContaRec->getCodTipo()->getCodigo() !== "CC")	$podeBol	= false;
		if (!$codContaRec->getCodCarteira()) 					$podeBol	= false;
		
		
		#################################################################################
		## Verificar se a conta está atrasada
		#################################################################################
		$vencBol			= \Zage\Fin\Data::proximoDiaUtil(date($system->config["data"]["dateFormat"]));
		$numDias			= \Zage\Fin\Data::numDiasAtraso($vencimento,$vencBol);
		$htmlAtraso			= "<i class='fa fa-check-circle green bigger-120'></i>";
		
		#################################################################################
		## Calcular o valor
		#################################################################################
		if (!$contaRec->getValorJaRecebido($oContaRec->getCodigo())) {
			$valor				= ($oContaRec->getValor() + $oContaRec->getValorJuros() + $oContaRec->getValorMora() + $oContaRec->getValorOutros() - $oContaRec->getValorDesconto() - $oContaRec->getValorCancelado());
		}else{
			$valor				= \Zage\App\Util::to_float($contaRec->getSaldoAReceber($oContaRec->getCodigo()));
		}
		
		#################################################################################
		## Calcular o Juros e Mora
		#################################################################################
		$_juros				= \Zage\Fin\ContaReceber::calculaJurosPorAtraso($oContaRec->getCodigo(), date($system->config["data"]["dateFormat"]));
		$_mora				= \Zage\Fin\ContaReceber::calculaMoraPorAtraso($oContaRec->getCodigo(), date($system->config["data"]["dateFormat"]));
		
		$urlDown			= "meuPagBoleto('".$oContaRec->getCodigo()."','".$vencBol."','".$valor."','".$_juros."','".$_mora."','0','0','PDF','".$instrucao."','');";
		$urlMail			= "meuPagBoleto('".$oContaRec->getCodigo()."','".$vencBol."','".$valor."','".$_juros."','".$_mora."','0','0','MAIL','".$instrucao."','".$email."');";
		$htmlBol			= '
		<div data-toggle="buttons" class="btn-group btn-overlap btn-corner">
			<span class="btn btn-sm btn-white btn-info center" onclick="'.$urlDown.'"><i class="fa fa-file-pdf-o bigger-120"></i></span>
			<span class="btn btn-sm btn-white btn-info center" onclick="'.$urlMail.'"><i class="fa fa-envelope bigger-120"></i></span>
		</div>
		';
		
		
		$tabCompra	.= '<tr>
			<td style="text-align: center;">'.$oCompras[$i]->getCodTransacao().'</td>
			<td style="text-align: center;">'.$oCompras[$i]->getCodVendaTipo()->getDescricao().'</td>
			<td style="text-align: center;">'.$oContaRec->getCodFormaPagamento()->getDescricao().'</td>
			<td style="text-align: center;">'.$oContaRec->getDataVencimento()->format($system->config["data"]["dateFormat"]).'</td>
			<td style="text-align: center;">R$ '.\Zage\App\Util::formataDinheiro($oCompras[$i]->getValorTotal()).'</td>
			<td class="hidden-480" style="text-align: center;">'.$oCompras[$i]->getDataCadastro()->format($system->config["data"]["datetimeSimplesFormat"]).'</td>
			<td style="text-align: center;">'.$htmlBol.'</td>
			</tr>';
	}
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

$tpl->set('MSG_COM'			,$msnCom);
#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

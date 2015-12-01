<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

################################################################################
# Verifica se o usuário tem permissão no menu
################################################################################
$system->checaPermissao ( $_codMenu_ );

################################################################################
# Resgatar os eventos aptos a compra
################################################################################
$msg 	  = null;
$hidden	  = null;
$disabled = null;
try {
	$eventoConfApto = \Zage\Fmt\Convite::listaConviteAptoVendaInternet();
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}

if ($eventoConfApto){
	$codFormando = \Zage\Fmt\Convite::getCodigoUsuarioPessoa();

	for ($i = 0; $i < sizeof($eventoConfApto); $i++) {
		$codEvento	 	= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodigo() : null;
		$eventoDesc		= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodTipoEvento()->getDescricao() : null;
		$valor			= ($eventoConfApto[$i]->getValor()) ? \Zage\App\Util::formataDinheiro($eventoConfApto[$i]->getValor()) : null;
		
		if (isset($codFormando) && !empty($codFormando)) {
			$qtdeDisponivel	= \Zage\Fmt\Convite::qtdeConviteDispFormando($codFormando, $eventoConfApto[$i]->getCodEvento());
			if(empty($qtdeDisponivel) || $qtdeDisponivel < 0) {
				$qtdeDisponivel = 0;
				$readonly = "readonly";
			}else{
				$readonly = "";
			}
		}
		$html .= "<tr class=\"center\"><td class=\"center\" style=\"width: 20px;\"><div class=\"inline\" zg-type=\"zg-div-msg\"></div></td>
				<td>".$eventoDesc."<input type='hidden' name='codEvento[]' value='".$codEvento."'></td>
				<td>R$ ".$valor."<input type='hidden' name='valor[]' value='".$valor."' ></td>
				<td>".$qtdeDisponivel."<input type='hidden' name='quantDisp[]' value='".$qtdeDisponivel."'></td>
				<td><input type='text' name='quantConv[]' id='quantConv' value='' ".$readonly." size='2' zg-data-toggle=\"mask\" zg-data-mask=\"numero\" onchange='zgCalcularTotal();zgValidaQuantDisp();'></td>
				<td><div name='total[".$i."]' zg-name=\"total\">R$ 0,00</div><input type='hidden' name='total[]' value='0'><input type='hidden' name='codConvExtra[]' value='".$eventoConfApto[$i]->getCodigo()."'></td></tr>";
	}
	$html 	.= "<tr><td colspan='5' align=\"right\">TAXA DE CONVENIÊNCIA</td><td class=\"center\"><div id='valorConvenienciaID'></div></td>";
	$html 	.= "<tr><td colspan='5' align=\"right\"><strong>TOTAL</strong></td><td class=\"center\"><div id='valorTotalID' name='valorTotal'><strong>R$ 0,00</strong></div></td></table>";
}else{
	$hidden = "hidden";
	$disabled = "disabled";
	$msg = '<div class="alert alert-warning">';
	$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i> A formatura ainda não disponibilizou nenhum evento para venda.';
	$msg .= '</div>';
}


################################################################################
# Select de Forma de Pagamento
################################################################################
try {
	$aFormaPag = $em->getRepository('Entidades\ZgfmtConviteExtraVendaForma')->findBy(array('codVendaTipo' => 'I', 'codOrganizacao' => $system->getCodOrganizacao()));

	if(!$aFormaPag){
		$hidden = "hidden";
		$disabled = "disabled";
		$msg = '<div class="alert alert-warning">';
		$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i> A formatura ainda não disponibilizou nenhum convite para venda.';
		$msg .= '</div>';
	}else{
		
		foreach ($aFormaPag as $info) {
			$oFormaPag .= "<option value=\"".$info->getCodFormaPagamento()->getCodigo()."\">".$info->getCodFormaPagamento()->getDescricao().'</option>';
		}
	}

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Resgatar as configurações de venda internet
################################################################################
try {
	$oConfFormaPag = $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codFormatura' => $system->getCodOrganizacao(), 'codVendaTipo' => 'I'));

	if ($oConfFormaPag){
		$taxaAdm 			= ($oConfFormaPag->getTaxaAdministracao()) ? $oConfFormaPag->getTaxaAdministracao() : 0;
		$indAddTaxaBoleto	= ($oConfFormaPag->getIndAdicionarTaxaBoleto()) ? $oConfFormaPag->getIndAdicionarTaxaBoleto() : 0;
		$codContaBoleto		= ($oConfFormaPag->getCodContaBoleto()) ? $oConfFormaPag->getCodContaBoleto()->getCodigo() : 0;
	}else{
		$hidden = "hidden";
		$disabled = "disabled";
		$msg = '<div class="alert alert-warning">';
		$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i> A formatura ainda não disponibilizou nenhum convite para venda.';
		$msg .= '</div>';
	}

} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}

################################################################################
# Resgatar as informações da conta (custo boleto)
################################################################################
try {
	$oConta = $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaBoleto));

	if ($oConta){
		$valorBoleto			= ($oConta->getValorBoleto()) ? $oConta->getValorBoleto() : 0;
	}else{
		$hidden = "hidden";
		$disabled = "disabled";
		$msg = '<div class="alert alert-warning">';
		$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i> A formatura ainda não disponibilizou nenhum convite para venda.';
		$msg .= '</div>';
	}

} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}


################################################################################
# Url Historico
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlHist = ROOT_URL . "/Fmt/conviteExtraCompraHis.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			   , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URL_HIST'		 	   , $urlHist );
$tpl->set ( 'IC'			 	   , $_icone_);
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_FORMANDO'	   	   , \Zage\Fmt\Convite::getCodigoUsuarioPessoa());
$tpl->set ( 'TABLE'				   , $html);
$tpl->set ( 'DISABLED'			   , $disabled);
$tpl->set ( 'HIDDEN'			   , $hidden);
$tpl->set ( 'MSG'			  	   , $msg);
$tpl->set ( 'COD_FORMA_PAG'		   , $oFormaPag);

$tpl->set ( 'VALOR_BOLETO'	   	   , $valorBoleto);
$tpl->set ( 'TAXA_ADM'	   		   , $taxaAdm);
$tpl->set ( 'IND_ADD_TAXA_BOLETO'  , $indAddTaxaBoleto);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


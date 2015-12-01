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
# Select de Formando
################################################################################
try {
	$aFormando = $em->getRepository('Entidades\ZgfinPessoa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codTipoPessoa' => 'O','indAtivo' => 1),array('nome' => 'ASC'));
	$oFormando = $system->geraHtmlCombo($aFormando, 'CODIGO', 'NOME', $codFormando, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Select de Forma de Pagamento
################################################################################
try {
	$aFormaPag = $em->getRepository('Entidades\ZgfmtConviteExtraVendaForma')->findBy(array('codVendaTipo' => 'P', 'codOrganizacao' => $system->getCodOrganizacao()),array());
	//$oFormaPag = $system->geraHtmlCombo($aFormaPag, 'CODIGO', 'DESCRICAO', $codFormaPag, '');
	
	if(!$aFormaPag){
		$aFormaPag = $em->getRepository('Entidades\ZgfinFormaPagamento')->findBy(array(),array('descricao' => 'ASC'));
		$oFormaPag = $system->geraHtmlCombo($aFormaPag, 'CODIGO', 'DESCRICAO', $codFormaPag, '');
	}else{
		$oFormaPag      = "<option value=\"\"></option>";
		foreach ($aFormaPag as $info) {
			$oFormaPag .= "<option value=\"".$info->getCodFormaPagamento()->getCodigo()."\">".$info->getCodFormaPagamento()->getDescricao().'</option>';
		}
	}
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Select de Conta Recebimento
################################################################################
	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));

	if ($aCntCer) {
		$oConta		= "<optgroup label='Contas do Cerimonial'>";
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$valBol		= ($aCntCer[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aCntCer[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
	}
	
	if ($aConta) {
		$oConta		.= ($aCntCer) ? "<optgroup label='Contas da Formatura'>" : '';
		for ($i = 0; $i < sizeof($aConta); $i++) {
			$valBol		= ($aConta[$i]->getCodTipo()->getCodigo() == "CC") ? \Zage\Fmt\Financeiro::getValorBoleto($aConta[$i]->getCodigo()) : 0;
			$oConta	.= "<option value='".$aConta[$i]->getCodigo()."' zg-val-boleto='".$valBol."'>".$aConta[$i]->getNome()."</option>";
		}
		$oConta		.= ($aCntCer) ? '</optgroup>' : '';
	}

################################################################################
# Resgatar as configurações de venda presencial
################################################################################
try {
	$oConfFormaPag = $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codFormatura' => $system->getCodOrganizacao(), 'codVendaTipo' => 'P'));

	if ($oConfFormaPag){
		$taxaAdm 			=($oConfFormaPag->getTaxaAdministracao()) ? $oConfFormaPag->getTaxaAdministracao() : 0;
		$indAddTaxaBoleto	=($oConfFormaPag->getIndAdicionarTaxaBoleto()) ? $oConfFormaPag->getIndAdicionarTaxaBoleto() : 0;
		$codContaBoleto		=($oConfFormaPag->getCodContaBoleto()) ? $oConfFormaPag->getCodContaBoleto()->getCodigo() : 0;
	}else{
		$taxaAdm = 0;
		$indAddTaxaBoleto = 0;
	}

} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/conviteExtraAlunosLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlNovo = ROOT_URL . "/Fmt/conviteExtraVenda.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ( 'URL_FORM'			   , $_SERVER ['SCRIPT_NAME'] );
$tpl->set ( 'URLVOLTAR'			   , $urlVoltar );
$tpl->set ( 'URLNOVO'		 	   , $urlNovo );
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_CONV_VENDA'	   , $codConvVenda);
$tpl->set ( 'COD_FORMANDO'	   	   , $oFormando);
$tpl->set ( 'COD_FORMA_PAG'	   	   , $oFormaPag);
$tpl->set ( 'COD_CONTA'	   		   , $oConta);

$tpl->set ( 'TAXA_ADM'	   		   , $taxaAdm);
$tpl->set ( 'IND_ADD_TAXA_BOLETO'  , $indAddTaxaBoleto);
$tpl->set ( 'COD_MENU'			   , $_codMenu_);

$tpl->set ( 'IC'				   , $_icone_);
$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


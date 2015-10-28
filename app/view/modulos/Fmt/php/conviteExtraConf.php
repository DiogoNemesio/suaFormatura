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
# Resgata as informações do banco
################################################################################
if ($codConviteExtraConf) {
	try {
		$info = $em->getRepository('Entidades\ZgfmtConviteExtraConf')->findOneBy(array('codigo' => $codConviteExtraConf));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ($e->getMessage());
	}
	
	$codTipoEvento			= ($info->getCodTipoEvento()->getCodigo()) ? $info->getCodTipoEvento()->getCodigo() : null;
	$tipoEventoDescricao	= ($info->getCodTipoEvento()->getDescricao()) ? $info->getCodTipoEvento()->getDescricao() : null;
	$valor 					= ($info->getValor()) ? \Zage\App\Util::formataDinheiro($info->getValor()) : null;
	$qtdeMax				= ($info->getQtdeMaxAluno()) ? $info->getQtdeMaxAluno() : null;
	$dataInicioInternet		= ($info->getDataInicioInternet() != null) ? $info->getDataInicioInternet()->format($system->config["data"]["dateFormat"]) : null;
	$dataFimInternet		= ($info->getDataFimInternet() != null) ? $info->getDataFimInternet()->format($system->config["data"]["dateFormat"]) : null;
	$codConta				= ($info->getContaRecebimentoInternet()) ? $info->getContaRecebimentoInternet() : null;
	$dataInicioPresencial	= ($info->getDataInicioPresencial() != null) ? $info->getDataInicioPresencial()->format($system->config["data"]["dateFormat"]) : null;
	$dataFimPresencial		= ($info->getDataFimPresencial() != null) ? $info->getDataFimPresencial()->format($system->config["data"]["dateFormat"]) : null;
	$custoBoleto			= ($info->getTaxaConveniencia() != null) ? \Zage\App\Util::formataDinheiro($info->getTaxaConveniencia()) : null;
	
	$oContaCorrente		= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codConta));
	$custoBoletoPadrao 	= ($oContaCorrente->getValorBoleto() != null) ? \Zage\App\Util::formataDinheiro($oContaCorrente->getValorBoleto()) : null;
	
} else {
	$infoTipoEvento = $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codTipoEvento));
	
	$tipoEventoDescricao = $infoTipoEvento->getDescricao();
	$valor 					= null;
	$qtdeMax 				= null;
	$dataInicioInternet 	= null;
	$dataFimInternet		= null;
	$codConta				= null;
	$dataInicioPresencial	= null;
	$dataFimPresencial		= null;
	$custoBoleto			= null;
}

#################################################################################
## Select da Conta de Crédito
#################################################################################
try {

	#################################################################################
	## Verifica se a formatura está sendo administrada por um Cerimonial, para resgatar as contas do cerimonial tb
	#################################################################################
	$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());

	if ($oFmtAdm)	{
		$aCntCer	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $oFmtAdm->getCodigo()),array('nome' => 'ASC'));
		
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			$arrayConta[$aCntCer[$i]->getCodigo()] = \Zage\App\Util::toPHPNumber($aCntCer[$i]->getValorBoleto());
		}
		
	}else{
		$aCntCer	= null;
	}

	$aConta		= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	
	for ($i = 0; $i < sizeof($aConta); $i++) {
		$arrayConta[$aConta[$i]->getCodigo()] = \Zage\App\Util::toPHPNumber($aConta[$i]->getValorBoleto());
	}

	if ($aCntCer) {
		$oConta		.= "<option value=''></option>";
		$oConta		.= "<optgroup label='Contas do Cerimonial'>";
		
		for ($i = 0; $i < sizeof($aCntCer); $i++) {
			if ($aCntCer[$i]->getCodigo() == $codConta){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			
			$oConta	.= "<option value='".$aCntCer[$i]->getCodigo()."'".$selected.">".$aCntCer[$i]->getNome()."</option>";
		}
		$oConta		.= '</optgroup>';
		if ($aConta) {
			$oConta		.= "<optgroup label='Contas da Formatura'>";
			for ($i = 0; $i < sizeof($aConta); $i++) {
				if ($aConta[$i]->getCodigo() == $codConta){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				
				$oConta	.= "<option value='".$aConta[$i]->getCodigo()."'>".$aConta[$i]->getNome()."</option>";
			}
			$oConta		.= '</optgroup>';
		}
	}else{
		$oConta		= $system->geraHtmlCombo($aConta,	'CODIGO', 'NOME', $codConta , '');
	}


} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/conviteExtraConfLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlNovo = ROOT_URL . "/Fmt/eventoAgendarLis.php?id=" . $uid;

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set ('URL_FORM'			   , $_SERVER ['SCRIPT_NAME']);
$tpl->set ('URLVOLTAR'			   , $urlVoltar);
$tpl->set ('URLNOVO'		 	   , $urlNovo);
$tpl->set ('ID'					   , $id);	
$tpl->set ('COD_TIPO_EVENTO'	   , $codTipoEvento);
$tpl->set ('COD_CONVITE_CONF'	   , $codConviteExtraConf);
$tpl->set ('CUSTO_BOLETO_PADRAO'   , $custoBoletoPadrao);
$tpl->set ('DESCRICAO_TIPO_EVENTO' , $tipoEventoDescricao);

$tpl->set ('CONTAS'					,$oConta);
$tpl->set ('VALOR'					,$valor);
$tpl->set ('QTDE_MAX'				,$qtdeMax);
$tpl->set ('DATA_INI_NET'			,$dataInicioInternet);
$tpl->set ('DATA_FIM_NET'			,$dataFimInternet);
$tpl->set ('CUSTO_BOLETO'			,$custoBoleto);
$tpl->set ('DATA_INI_PRE'			,$dataInicioPresencial);
$tpl->set ('DATA_FIM_PRE'			,$dataFimPresencial);

$tpl->set('ARRAY_CONTA'				,json_encode($arrayConta));

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


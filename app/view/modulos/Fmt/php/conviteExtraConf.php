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
if ($codConviteEventoConf) {
	try {
		$info = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codigo' => $codConviteEventoConf));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ($e->getMessage());
	}
	
	$codEvento				= ($info->getCodEvento()->getCodigo()) ? $info->getCodEvento()->getCodigo() : null;
	$dataEvento				= ($info->getCodEvento()->getData()) ? $info->getCodEvento()->getData()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$tipoEventoDescricao	= ($info->getCodEvento()->getCodTipoEvento()->getDescricao()) ? $info->getCodEvento()->getCodTipoEvento()->getDescricao() : null;
	$valor 					= ($info->getValor()) ? \Zage\App\Util::formataDinheiro($info->getValor()) : null;
	$qtdeMax				= ($info->getQtdeMaxAluno()) ? $info->getQtdeMaxAluno() : null;
	$dataInicioInternet		= ($info->getDataInicioInternet() != null) ? $info->getDataInicioInternet()->format($system->config["data"]["dateFormat"]) : null;
	$dataFimInternet		= ($info->getDataFimInternet() != null) ? $info->getDataFimInternet()->format($system->config["data"]["dateFormat"]) : null;
	$dataInicioPresencial	= ($info->getDataInicioPresencial() != null) ? $info->getDataInicioPresencial()->format($system->config["data"]["dateFormat"]) : null;
	$dataFimPresencial		= ($info->getDataFimPresencial() != null) ? $info->getDataFimPresencial()->format($system->config["data"]["dateFormat"]) : null;
	
	
} else {
	$infoEvento				= $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
	
	if ($infoEvento){
		$tipoEventoDescricao 	= $infoEvento->getCodTipoEvento()->getDescricao();
		$dataEvento				= ($infoEvento->getData()) ? $infoEvento->getData()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	}else{
		$tipoEventoDescricao	= null;
		$dataEvento				= null;
	}
	
	$valor 					= null;
	$qtdeMax 				= null;
	$dataInicioInternet 	= null;
	$dataFimInternet		= null;
	$dataInicioPresencial	= null;
	$dataFimPresencial		= null;
}

################################################################################
# Verificar se as configurações de internet estão ativas
################################################################################
$oVendaConf = $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codVendaTipo' => 'I' , 'codFormatura' => $system->getCodOrganizacao()));

if (!$oVendaConf){
	$autorizadoNet = 'disabled';
	
	$msgAutorizado .= '<div class="col-sm-12 alert alert-warning">';
	$msgAutorizado .= '<i class="fa fa-exclamation-triangle bigger-125"></i> Para inciar as vendas pela internet é necessário realizar as configurações de pagamento no menu: CONVIDADOS > CONFIGURAR PAGAMENTO';
	$msgAutorizado .= '</div>';
	
}else{
	$autorizadoNet = '';
	$msgAutorizado = '';
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
$tpl->set ('COD_EVENTO'	   		   , $codEvento);
$tpl->set ('COD_CONVITE_CONF'	   , $codConviteEventoConf);
$tpl->set ('DESCRICAO_TIPO_EVENTO' , $tipoEventoDescricao);
$tpl->set ('DATA_EVENTO' 		   , $dataEvento);

$tpl->set ('AUTORIZADO_NET'			,$autorizadoNet);
$tpl->set ('AUTORIZADO_MSG'			,$msgAutorizado);
$tpl->set ('VALOR'					,$valor);
$tpl->set ('QTDE_MAX'				,$qtdeMax);
$tpl->set ('DATA_INI_NET'			,$dataInicioInternet);
$tpl->set ('DATA_FIM_NET'			,$dataFimInternet);
$tpl->set ('DATA_INI_PRE'			,$dataInicioPresencial);
$tpl->set ('DATA_FIM_PRE'			,$dataFimPresencial);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();

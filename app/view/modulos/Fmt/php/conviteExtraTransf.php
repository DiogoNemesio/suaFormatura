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
$msg 	  = null;
$hidden	  = null;
$disabled = null;
try {
	$aFormando = \Zage\Fmt\Formando::ListaPessoaFormandoRetiraSelec($system->getCodUsuario());
	
	if(!empty($aFormando) && $aFormando !=0){
		$oFormando = $system->geraHtmlCombo($aFormando, 'CODIGO', 'NOME', $codFormando, '');
	}else{
		$hidden = "hidden";
		$disabled = "disabled";
		$msg .= '<div class="alert alert-warning">';
		$msg .= '<i class="fa fa-exclamation-triangle bigger-125"></i>Formatura não possui nenhum formando ativo!';
		$msg .= '</div>';
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Resgata as informações do banco
################################################################################
try {
	$eventoConfApto = \Zage\Fmt\Convite::listaConviteAptoVenda();
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage () );
}

$codFormando = \Zage\Fmt\Convite::getCodigoUsuarioPessoa();

$oEvento      = "<option value=\"\"></option>";
for ($i = 0; $i < sizeof($eventoConfApto); $i++) {
	$codEvento	 	= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodigo() : null;
	$eventoDesc		= ($eventoConfApto[$i]->getCodEvento()) ? $eventoConfApto[$i]->getCodEvento()->getCodTipoEvento()->getDescricao() : null;
	
	$oEventoConf = $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'codEvento' => $codEvento ));
	$qtdeMaxima	 = ($oEventoConf->getQtdeMaxAluno()) ? $oEventoConf->getQtdeMaxAluno() : null;
	
	if (isset($codFormando) && !empty($codFormando)) {
		$qtdeDisponivel	= \Zage\Fmt\Convite::qtdeConviteDispFormando($codFormando, $eventoConfApto[$i]->getCodEvento());

		if(empty($qtdeDisponivel) || $qtdeDisponivel < 0) {
			$qtdeDisponivel = 0;
			$readonly = "readonly";
		}else{
			$readonly = "";
		}
	}
	$oEvento 	.= "<option value='".$codEvento."' zg-val-quantidade='".$qtdeDisponivel."' zg-val-maxima='".$qtdeMaxima."'>".$eventoDesc."</option>";
}
################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/conviteExtraAlunosLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ );
$urlHist = ROOT_URL . "/Fmt/conviteExtraTransfLis.php?id=" . $uid;

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
$tpl->set ( 'URL_HIST'		 	   , $urlHist );
$tpl->set ( 'ID'				   , $id );
$tpl->set ( 'COD_FORMANDO'	   	   , $oFormando);
$tpl->set ( 'COD_EVENTO'	   	   , $oEvento);
$tpl->set ( 'TABLE_TRANS'	   	   , $html);
$tpl->set ( 'MSG'		 	  	   , $msg);
$tpl->set ( 'HIDDEN'		 	   , $hidden);
$tpl->set ( 'DISABLED'		 	   , $disabled);

$tpl->set ( 'IC'				   ,$_icone_);
$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


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
if ($codEvento) {
	try {
		$info = $em->getRepository ( 'Entidades\ZgfmtEvento' )->findOneBy (array ('codigo' => $codEvento));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$codTipo		 = ($info->getCodTipoEvento()) ? $info->getCodTipoEvento()->getCodigo() : null;
	$codLocal		 = ($info->getCodLocal()) ? $info->getCodLocal() : null;
	$dataEvento		 = ($info->getData() != null) ? $info->getData()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$nome			 = ($info->getNome()) ? $info->getNome() : null;
	$cep			 = ($info->getCep()) ? $info->getCep() : null;
	$codLogradouro	 = ($info->getCodLogradouro()) ? $info->getCodLogradouro()->getCodigo() : null;
	$endereco		 = ($info->getEndereco()) ? $info->getEndereco() : null;
	$bairro			 = ($info->getBairro()) ? $info->getBairro() : null;
	$complemento	 = ($info->getComplemento()) ? $info->getComplemento() : null;
	$numero			 = ($info->getNumero()) ? $info->getNumero() : null;
	$latitude		 = ($info->getLatitude()) ? $info->getLatitude() : null;
	$longitude		 = ($info->getLongitude()) ? $info->getLongitude() : null;
	
	if($codLogradouro != null){
	
		$infoLogradouro = $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	
		if($infoLogradouro->getDescricao() == $endereco){
			$endPadrao 	  = $infoLogradouro->getDescricao();
			$bairroPadrao = $infoLogradouro->getCodBairro()->getDescricao();
			$readonly 	  = 'readonly';
		}else{
			$endPadrao 	  = $endereco;
			$bairroPadrao = $bairro;
			$readonly 	  = '';
		}
	
		$cidade = $infoLogradouro->getCodBairro()->getCodLocalidade()->getDescricao();
		$estado = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodUF()->getNome();
	
	}else{
	
		$endPadrao 		= '';
		$bairroPadrao 	= '';
		$cidade	 		= '';
		$estado 		= '';
		$readonly 	  = 'readonly';
	
	}
} else {
	$codLocal		 = null;
	$dataEvento		 = null;
	$nome			 = null;
	$cep			 = null;
	$codLogradouro	 = null;
	$endereco		 = null;
	$bairro			 = null;
	$complemento 	 = null;
	$numero			 = null;
	$latitude		 = null;
	$longitude		 = null;
	
	$endPadrao 	 	= '';
	$bairroPadrao 	= '';
	$cidade 		= '';
	$estado 		= '';
	$readonly		= 'readonly';
}

################################################################################
# Select de Local
################################################################################
try {
	$aLocal = $em->getRepository('Entidades\ZgadmOrganizacao')->findBy(array(),array('nome' => 'ASC'));
	$oLocal = $system->geraHtmlCombo($aLocal, 'CODIGO', 'NOME', $codLocal, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Fmt/eventoAgendarLis.php?id=" . $id;

################################################################################
# Url Novo
################################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codEvento=' );
$urlNovo = ROOT_URL . "/Fmt/eventoAgendarAlt.php?id=" . $uid;

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
$tpl->set ( 'COD_EVENTO'		   , $codEvento);
$tpl->set ( 'COD_TIPO'		   	   , $codTipo);
$tpl->set ( 'COD_LOCAL'		   	   , $oLocal);
$tpl->set ( 'DATA_EVENTO'	   	   , $dataEvento);
$tpl->set ( 'NOME'			   	   , $nome);
$tpl->set ( 'CEP'  			 	   , $cep);
$tpl->set ( 'COD_LOGRADOURO'   	   , $codLogradouro);
$tpl->set ( 'COMPLEMENTO' 		   , $complemento);
$tpl->set ( 'NUMERO' 			   , $numero);
$tpl->set ( 'LATITUDE' 			   , $latitude);
$tpl->set ( 'LONGITUDE' 		   , $longitude);

$tpl->set ( 'LOGRADOURO'		   , $endPadrao);
$tpl->set ( 'BAIRRO'		       , $bairroPadrao);
$tpl->set ( 'CIDADE'			   , $cidade);
$tpl->set ( 'ESTADO'			   , $estado);
$tpl->set ( 'READONLY'			   , $readonly);

$tpl->set ( 'DP', \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


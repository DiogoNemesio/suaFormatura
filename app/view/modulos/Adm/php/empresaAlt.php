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
if ($codEmpresa) {
	try {
		$info = $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() ,'codigo' => $codEmpresa));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$codEmpresa 	= $info->getCodigo();
	$nome 			= $info->getNome();
	$fantasia 		= $info->getFantasia();
	$cnpj 			= $info->getCnpj();
	$inscEstadual 	= $info->getInscEstadual();
	$inscMunicipal 	= $info->getInscMunicipal();
	$email 			= $info->getEmail();
	
	$codLogradouro 	= ($info->getCodLogradouro() != null) ? $info->getCodLogradouro()->getCodigo() : null;
	$cep 			= $info->getCep();
	$endereco 		= $info->getEndereco();
	$bairro 		= $info->getBairro();
	$complemento 	= $info->getComplemento();
	$numero 		= $info->getNumero();
	
	$codStatus 		= $info->getCodStatus()->getCodigo ();
	$codMatriz	 	= ($info->getCodMatriz () != null) ? $info->getCodMatriz()->getCodigo() : null;
	$dataAbertura	= ($info->getDataAbertura() != null) ? $info->getDataAbertura()->format($system->config["data"]["dateFormat"]) : null;
	$logomarca 		= $info->getLogomarca();
	
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
}else {
	
	$codEmpresa 	= '';
	$nome 			= '';
	$fantasia 		= '';
	$cnpj 			= '';
	$inscEstadual 	= '';
	$inscMunicipal 	= '';
	$email 			= '';
	
	$codLogradouro 	= '';
	$endereco 		= '';
	$cep 			= '';
	$bairro 		= '';
	$complemento 	= '';
	$numero 		= '';
		
	$codStatus 		= '';
	$codMatriz 		= '';
	$dataAbertura	= '';
	$logomarca 		= '';
	
	$endPadrao 	 	= '';
	$bairroPadrao 	= '';
	$cidade 		= '';
	$estado 		= '';
	$readonly		= 'readonly';
}



################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Adm/empresaLis.php?id=" . $id;

###############################################################################
#Url Novo
###############################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codEmpresa=' );
$urlNovo = ROOT_URL . "/Adm/empresaAlt.php?id=" . $uid;

################################################################################
# Select de Matriz
################################################################################
try {
	$aMatriz = $em->getRepository ( 'Entidades\ZgadmEmpresa' )->findBy ( array ('codOrganizacao' => $system->getCodorganizacao(),'codMatriz' => null));
	$oMatriz = $system->geraHtmlCombo ( $aMatriz, 'CODIGO', 'FANTASIA', $codMatriz, '' );
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage (), __FILE__, __LINE__ );
}

###############################################################################
# Select de Status
###############################################################################
try {
	$aStatus = $em->getRepository ( 'Entidades\ZgadmEmpresaStatusTipo' )->findBy ( array (), array ('nome' => 'ASC'));
	$oStatus = $system->geraHtmlCombo ( $aStatus, 'CODIGO', 'NOME', $codStatus, null );
} catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage (), __FILE__, __LINE__ );
}

################################################################################
# Carregando o template html
################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ));

################################################################################
# Define os valores das variáveis
################################################################################
$tpl->set('URL_FORM'		,$_SERVER ['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLNOVO'			,$urlNovo);
$tpl->set('ID'				,$id);
$tpl->set('COD_EMPRESA'		,$codEmpresa);
$tpl->set('COD_LOGRADOURO'	,$codLogradouro);
$tpl->set('CNPJ'			,$cnpj);
$tpl->set('CEP'				,$cep);
$tpl->set('LOGRADOURO'		,$endPadrao);
$tpl->set('BAIRRO'			,$bairroPadrao);
$tpl->set('CIDADE'			,$cidade);
$tpl->set('ESTADO'			,$estado);
$tpl->set('NUMERO'			,$numero);
$tpl->set('COMPLEMENTO'		,$complemento);
$tpl->set('READONLY'		,$readonly);
$tpl->set('NOME'			,$nome);
$tpl->set('FANTASIA'		,$fantasia);
$tpl->set('INSCR_EST'		,$inscEstadual);
$tpl->set('INSCR_MUN'		,$inscMunicipal);
$tpl->set('COD_STATUS'		,$oStatus);
$tpl->set('MATRIZ'			,$oMatriz);
$tpl->set('DATA_ABERTURA'	,$dataAbertura);
$tpl->set('EMAIL'			,$email);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


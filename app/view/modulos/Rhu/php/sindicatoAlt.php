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
if ($codSindicato) {
	try {
		$info = $em->getRepository('Entidades\ZgrhuSindicato')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao() ,'codigo' => $codSindicato));
	} catch ( \Exception $e ) {
		\Zage\App\Erro::halt ( $e->getMessage () );
	}
	
	$codSindicato 	= ($info->getCodigo() != null) ? $info->getCodigo() : null;
	$cnpj 			= ($info->getCnpj() != null) ? $info->getCnpj() : null;
	$nome 			= ($info->getNome() != null) ? $info->getNome() : null;
	$fantasia 		= ($info->getFantasia() != null) ? $info->getFantasia() : null;
	$apelido 		= ($info->getApelido() != null) ? $info->getApelido() : null;
	$email 			= ($info->getEmail() != null) ? $info->getEmail() : null;
	
	/** Endereco **/
	$codLogradouro 	= ($info->getCodLogradouro() != null) ? $info->getCodLogradouro()->getCodigo() : null;
	$cep 			= $info->getCep();
	$endereco 		= $info->getEndereco();
	$bairro 		= $info->getBairro();
	$complemento 	= $info->getComplemento();
	$numero 		= $info->getNumero();
	
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
	
	$codSindicato 	= '';
	$nome 			= '';
	$fantasia 		= '';
	$cnpj 			= '';
	$email 			= '';
	$apelido		= '';
	
	$codLogradouro 	= '';
	$endereco 		= '';
	$cep 			= '';
	$bairro 		= '';
	$complemento 	= '';
	$numero 		= '';
		
	$endPadrao 	 	= '';
	$bairroPadrao 	= '';
	$cidade 		= '';
	$estado 		= '';
	$readonly		= 'readonly';
}

#################################################################################
## Select de Tipo de Telefone
#################################################################################
try {
	$aTipoTel		= $em->getRepository('Entidades\ZgappTelefoneTipo')->findAll();
	$oTipoTel		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	null, 	null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar os dados de contato
#################################################################################
$aTelefones		= $em->getRepository('Entidades\ZgrhuSindicatoTelefone')->findBy(array('codSindicato' => $codSindicato));
$tabTel			= "";
for ($i = 0; $i < sizeof($aTelefones); $i++) {

	#################################################################################
	## Monta a combo de Tipo
	#################################################################################
	$codTipoTel		= ($aTelefones[$i]->getCodTipoTelefone()) ? $aTelefones[$i]->getCodTipoTelefone()->getCodigo() : null;
	$oTipoInt		= $system->geraHtmlCombo($aTipoTel,	'CODIGO', 'DESCRICAO',	$codTipoTel, '');

	$tabTel			.= '<tr><td class="center" style="width: 20px;"><div class="inline" zg-type="zg-div-msg"></div></td><td><select class="select2" style="width:100%;" name="codTipoTel[]" data-rel="select2">'.$oTipoInt.'</select></td><td><input type="text" name="telefone[]" value="'.$aTelefones[$i]->getTelefone().'" maxlength="15" autocomplete="off" zg-data-toggle="mask" zg-data-mask="fone" zg-data-mask-retira="1"></td><td class="center"><span class="center" zgdelete onclick="delRowTelefoneSindicatoAlt($(this));"><i class="fa fa-trash bigger-150 red"></i></span><input type="hidden" name="codTelefone[]" value="'.$aTelefones[$i]->getCodigo().'"></td></tr>';
}

################################################################################
# Url Voltar
################################################################################
$urlVoltar = ROOT_URL . "/Rhu/sindicatoLis.php?id=" . $id;

###############################################################################
#Url Novo
###############################################################################
$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codSindicato=' );
$urlNovo = ROOT_URL . "/Rhu/sindicatoAlt.php?id=" . $uid;

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
$tpl->set('COD_SINDICATO'	,$codSindicato);
$tpl->set('NOME'			,$nome);
$tpl->set('FANTASIA'		,$fantasia);
$tpl->set('EMAIL'			,$email);
$tpl->set('CNPJ'			,$cnpj);
$tpl->set('APELIDO'			,$apelido);
$tpl->set('CEP'				,$cep);
$tpl->set('COD_LOGRADOURO'	,$codLogradouro);
$tpl->set('LOGRADOURO'		,$endPadrao);
$tpl->set('BAIRRO'			,$bairroPadrao);
$tpl->set('CIDADE'			,$cidade);
$tpl->set('ESTADO'			,$estado);
$tpl->set('NUMERO'			,$numero);
$tpl->set('COMPLEMENTO'		,$complemento);
$tpl->set('READONLY'		,$readonly);
$tpl->set('TAB_TELEFONE'	, $tabTel);
$tpl->set('TIPO_TEL'		, $oTipoTel);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_DP, \Zage\App\ZWS::CAMINHO_RELATIVO ) );

################################################################################
# Por fim exibir a página HTML
################################################################################
$tpl->show ();


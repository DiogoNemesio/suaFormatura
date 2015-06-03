<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($codUsuario)) {
	if ($codUsuario) 	$podeAlt	= 'readonly';
	else 				$podeAlt	= '';
}else{
	$codUsuario	= null;
	$podeAlt	= '';
}

#################################################################################
## Resgata as informações do banco
#################################################################################
if ($codUsuario) {
	try {
		$info			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	$usuario		= $info->getUsuario();
	$nome			= $info->getNome();
	$codStatus		= $info->getCodStatus()->getCodigo();	
	$sexo			= $info->getSexo()->getCodigo();
	$codPerfil		= $info->getCodStatus()->getCodigo();
	if ($info->getIndTrocarSenha() == 1) {
		$indTrocarSenha		= "checked";
	}else{
		$indTrocarSenha		= '';
	}
	
	
	/** Endereco **/
	$codLogradouro   = ($info->getCodLogradouro()) ? $info->getCodLogradouro()->getCodigo() : null;
	$cep 		     = ($info->getCep()) ? $info->getCep() : null;
	$complemento     = ($info->getComplemento()) ? $info->getComplemento() : null;
	$numero		     = ($info->getNumero()) ? $info->getNumero() : null;
	$endCorreto		 = ($info->getIndEndCorreto() == 1) ? "checked" : null;
	
	if($codLogradouro != null){
	
		$infoLogradouro = $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	
		if($info->getIndEndCorreto() == 0){
			if($infoLogradouro->getDescricao() == $info->getEndereco()){
				$logradouro	  = $infoLogradouro->getDescricao();
				$readOnlyEnd 	  = 'readonly';
			}else{
				$logradouro	  = $info->getEndereco();
				$readOnlyEnd 	  = '';
			}
				
			if($infoLogradouro->getCodBairro()->getDescricao() == $info->getBairro()){
				$bairro = $infoLogradouro->getCodBairro()->getDescricao();
				$readOnlyBairro 	  = 'readonly';
			}else{
				$bairro = $info->getBairro();
				$readOnlyBairro 	  = '';
			}
	
		}else{
			$logradouro 	= $infoLogradouro->getDescricao();
			$bairro 		= $infoLogradouro->getCodBairro()->getDescricao();
			$readOnlyBairro = 'readonly';
			$readOnlyEnd 	= 'readonly';
		}
	
		$cidade	  		 = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome();
		$estado    		 = $infoLogradouro->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUF()->getNome();
	}else{
		$readOnlyBairro = 'readonly';
		$readOnlyEnd 	= 'readonly';
	}
	

}else{
	$usuario		= '';
	$nome			= '';
	$codStatus		= '';
	$email			= '';
	$telefone		= '';
	$celular		= '';
	$sexo			= '';
	$indTrocarSenha	= '';
	$avatar			= '';
	$avatarLink		= '';
	$codPerfil		= '';
	
	$codLogradouro  = '';
	$logradouro		= '';
	$bairro			= '';
	$cidade			= '';
	$estado			= '';
	$complemento   	= '';
	$numero		    = '';
	$readOnlyBairro	= 'readonly';
	$readOnlyEnd	= 'readonly';
	$endCorreto  = '';
}

if (empty($avatarLink)) $avatarLink		= IMG_URL.'/avatars/usuarioGenerico.png';

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
$urlVoltar			= ROOT_URL . "/Seg/usuarioLis.php?id=".$uid;
$urlNovo			= ROOT_URL . "/Seg/usuarioAlt.php?id=".$uid;

#################################################################################
## Select de Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Select de Status
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findAll();
	$oStatus	= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$aStatus, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Select de Perfil
#################################################################################
try {
	$aPerfil	= $em->getRepository('Entidades\ZgsegPerfil')->findBy(array(),array('nome' => 'ASC'));
	$oPerfil	= $system->geraHtmlCombo($aPerfil,	'CODIGO', 'NOME', $codPerfil, null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Avatars
#################################################################################
$aAvatar		= $em->getRepository('Entidades\ZgsegAvatar')->findBy(array('sexo' => $sexo));
$hAvatar		= "";
if ($aAvatar) {
	foreach ($aAvatar as $av) {
		$hAvatar	.= '<li>';
		$hAvatar	.= '<a href="'.$av->getLink().'" title="'.$av->getNome().'" data-rel="colorbox">';
		$hAvatar	.= '<img height="50" width="50" src="'.$av->getLink().'" />';
		$hAvatar	.= '<div class="tags"></div>';
		$hAvatar	.= '</a>';
		$hAvatar	.= '<div class="tools tools-bottom">';
		$hAvatar	.= '<a href="javascript:zgAlteraAvatar(\''.$av->getCodigo().'\',\''.$av->getLink().'\');"><i class="fa fa-check"></i></a>';
		$hAvatar	.= '</div>';
		$hAvatar	.= '</li>';
	}
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'			,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'			,$urlVoltar);
$tpl->set('URLNOVO'				,$urlNovo);
$tpl->set('ID'					,$id);
$tpl->set('COD_USUARIO'			,$codUsuario);
$tpl->set('USUARIO'				,$usuario);
$tpl->set('NOME'				,$nome);
$tpl->set('COD_STATUS'			,$oStatus);
$tpl->set('TELEFONE'			,$telefone);
$tpl->set('CELULAR'				,$celular);
$tpl->set('SEXO'				,$oSexo);
$tpl->set('EMAIL'				,$email);
$tpl->set('PODEALTERAR'			,$podeAlt);
$tpl->set('COD_AVATAR'			,$avatar);
$tpl->set('PERFIL'				,$oPerfil);
$tpl->set('AVATAR_LINK'			,$avatarLink);
$tpl->set('AVATARS'				,$hAvatar);
$tpl->set('EMPRESA_HTML'		,$empresaHTML);

$tpl->set ( 'COD_LOGRADOURO' , $codLogradouro);
$tpl->set ( 'CEP' 			 , $cep);
$tpl->set ( 'LOGRADOURO'	 , $logradouro);
$tpl->set ( 'BAIRRO'		 , $bairro);
$tpl->set ( 'CIDADE'		 , $cidade);
$tpl->set ( 'ESTADO'		 , $estado);
$tpl->set ( 'COMPLEMENTO' 	 , $complemento);
$tpl->set ( 'NUMERO' 		 , $numero);
$tpl->set ( 'READONLY_BAIRRO', $readOnlyBairro);
$tpl->set ( 'READONLY_END' 	 , $readOnlyEnd);
$tpl->set ( 'IND_END_CORRETO', $endCorreto);

$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


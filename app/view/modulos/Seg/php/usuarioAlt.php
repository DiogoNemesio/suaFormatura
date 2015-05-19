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
		$info			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $codUsuario));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	$usuario		= $info->getUsuario();
	$nome			= $info->getNome();
	$codStatus		= $info->getCodStatus()->getCodigo();
	$email			= $info->getEmail();
	$telefone		= $info->getTelefone();
	$celular		= $info->getCelular();
	$sexo			= $info->getSexo()->getCodigo();
	$codPerfil		= $info->getCodStatus()->getCodigo();
	if ($info->getIndTrocarSenha() == 1) {
		$indTrocarSenha		= "checked";
	}else{
		$indTrocarSenha		= '';
	}
	
	$avatar			= ($info->getAvatar()) ? $info->getAvatar()->getCodigo() 	: null;
	$avatarLink		= ($info->getAvatar()) ? $info->getAvatar()->getLink()		: null;
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
}

if (empty($avatarLink)) $avatarLink		= IMG_URL.'/avatars/usuarioGenerico.png';

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
$urlVoltar			= ROOT_URL . "/Seg/usuarioLis.php?id=".$uid;
$urlNovo			= ROOT_URL . "/Seg/usuarioAlt.php?id=".$uid;

#################################################################################
## Select de Status / Sexo
#################################################################################
try {
	$aStatus	= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findAll(); 
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oStatus	= $system->geraHtmlCombo($aStatus,	'CODIGO', 'NOME', 		$codStatus, null);
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, 		null);
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
## Acesso para as empresas
#################################################################################
$aEmpresas		= $em->getRepository('Entidades\ZgadmEmpresa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
$empresaHTML	= "";
if ($aEmpresas) {
	foreach ($aEmpresas as $av) {
		$empresaHTML	.= '<label class="col-sm-7 control-label"><input name="'.$av->getCodigo().'" type="checkbox" class="ace" /><span class="lbl"> '.$av->getFantasia().'</span></label>';
	}
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
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


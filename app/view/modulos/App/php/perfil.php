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
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);


#################################################################################
## Resgata as informações do banco
#################################################################################
$codUsuario		= $_user->getCodigo();
$usuario		= $_user->getUsuario();
$nome			= $_user->getNome();
$codStatus		= $_user->getCodStatus()->getCodigo();
$email			= $_user->getEmail();
$telefone		= $_user->getTelefone();
$celular		= $_user->getCelular();
$sexo			= $_user->getSexo()->getCodigo();
$avatar			= $_user->getAvatar()->getCodigo();
$avatarLink		= $_user->getAvatar()->getLink();
if (empty($avatarLink)) $avatarLink		= IMG_URL.'/avatars/usuarioGenerico.png';

#################################################################################
## Select de Status / Sexo
#################################################################################
try {
	$aSexo		= $em->getRepository('Entidades\ZgsegSexoTipo')->findAll();
	$oSexo		= $system->geraHtmlCombo($aSexo,	'CODIGO', 'DESCRICAO',	$sexo, 		null);
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
$tpl->set('URL_FORM'		,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'		,null);
$tpl->set('ID'				,$id);
$tpl->set('COD_USUARIO'		,$codUsuario);
$tpl->set('USUARIO'			,$usuario);
$tpl->set('NOME'			,$nome);
$tpl->set('TELEFONE'		,$telefone);
$tpl->set('CELULAR'			,$celular);
$tpl->set('SEXO'			,$oSexo);
$tpl->set('EMAIL'			,$email);
$tpl->set('COD_STATUS'		,$codStatus);
$tpl->set('COD_AVATAR'		,$avatar);
$tpl->set('AVATAR_LINK'		,$avatarLink);
$tpl->set('AVATARS'			,$hAvatar);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

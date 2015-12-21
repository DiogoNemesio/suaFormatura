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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codPerfil)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codPerfil)) {
	try {
		$info = $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$descricao		= $info->getNome();
	
}else{
	$descricao		= "";
}

#################################################################################
## Lista de categorias
#################################################################################
$oTipoOrg		= $em->getRepository('Entidades\ZgadmOrganizacaoTipo')->findBy(array(),array('descricao' => ASC));
$perfilOrgTipo	= $em->getRepository('Entidades\ZgsegPerfilOrganizacaoTipo')->findBy(array('codPerfil' => $codPerfil));

$aPerfilOrg 	= array();

for ($i = 0; $i < sizeof($perfilOrgTipo); $i++) {
	$aPerfilOrg[$i] = $perfilOrgTipo[$i]->getCodTipoOrganizacao()->getCodigo();
}

$htmlLis		= "";

for ($i = 0; $i < sizeof($oTipoOrg); $i++) {

	if (in_array($oTipoOrg[$i]->getCodigo(), $aPerfilOrg)){
		$selected = 'selected';
	}else{
		$selected = '';
	}

	$htmlLis .= '<option value="'.$oTipoOrg[$i]->getCodigo().'" '.$selected.'>'.$oTipoOrg[$i]->getDescricao().'</option>';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Seg/perfilLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPerfil=');
$urlNovo			= ROOT_URL."/Seg/perfilAlt.php?id=".$uid;

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
$tpl->set('COD_PERFIL'			,$codPerfil);
$tpl->set('NOME'				,$nome);
$tpl->set('DUAL_LIST'			,$htmlLis);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

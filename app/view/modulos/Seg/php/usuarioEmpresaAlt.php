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
	\Zage\Erro::halt('Falta de Parâmetros');
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
if (!isset($codUsuario) || (!$codUsuario)) {
	\Zage\Erro::halt('Falta de Parâmetros 2');
}

#################################################################################
## Resgata as informações do usuário
#################################################################################
$info 		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codigo' => $codUsuario));

#################################################################################
## Resgata as empresas
#################################################################################
$aEmps		= $em->getRepository('Entidades\ZgadmEmpresa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
$uEmps		= \Zage\Seg\Usuario::listaPerfilAcesso($codUsuario);
$cEmps		= '';

#################################################################################
## Select do Perfil
#################################################################################
$aPerfil	= $em->getRepository('Entidades\ZgsegPerfil')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(),'indAtivo' => 1),array('nome' => 'ASC')); 

#################################################################################
## Monta check boxes
#################################################################################
for ($i = 0; $i < sizeof($aEmps); $i++) {
	$checked	= '';
	$codPerfil	= null;
	for ($j = 0; $j < sizeof($uEmps); $j++) {
		if ($aEmps[$i]->getCodigo() == $uEmps[$j]->getCodEmpresa()->getCodigo())	{
			$checked	= "checked";
			$codPerfil	= $uEmps[$j]->getCodPerfil()->getCodigo();
		}
	}
	$oPerfil	= $system->geraHtmlCombo($aPerfil,	'codigo', 'nome', $codPerfil, null);
	
	$cEmps		.= "<div class='row'><div class='col-sm-3'><div class='checkbox'><label><input name='".$aEmps[$i]->getCodigo()."' type='checkbox' class='ace' $checked /></span><span class='lbl'> ".$aEmps[$i]->getNome()." </span></label></div></div><div class='col-sm-3'><span class='lbl'>&nbsp;<select data-rel='select2' name='perfil[".$aEmps[$i]->getCodigo()."]'>".$oPerfil."</select></span></div></div>";
}

#################################################################################
## Urls
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
$urlVoltar			= ROOT_URL . "/Seg/usuarioLis.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('COD_USUARIO'		,$codUsuario);
$tpl->set('USUARIO'			,$info->getUsuario());
$tpl->set('EMPRESAS'		,$cEmps);
$tpl->set('ID'				,$id);
$tpl->set('DP'				,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

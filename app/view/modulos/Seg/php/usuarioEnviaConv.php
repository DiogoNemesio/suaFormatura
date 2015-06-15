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
if (isset($_GET['email'])){
	$email		= \Zage\App\Util::antiInjection($_GET['email']);
}

if (isset($_GET['cpf'])){
	$cpf		= \Zage\App\Util::antiInjection($_GET['cpf']);
	$cpf = \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->retiraMascara($cpf);
}

if (isset($_GET['codOrganizacao'])){
	$codOrganizacao		= \Zage\App\Util::antiInjection($_GET['codOrganizacao']);
}

if (!isset($email) || !isset($cpf)) \Zage\App\Erro::halt('Falta de Parâmetros : 2');
if (!isset($codOrganizacao)) \Zage\App\Erro::halt('Falta de Parâmetros : COD_ORGANIZACAO');

#################################################################################
## Resgata as informações do banco
#################################################################################
$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $email));

if ($oUsuario){
	$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $email));
	$campo 			= 'EMAIL';

}else{
	$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => $cpf));
	if ($oUsuario){
		$campo 		= 'CPF';
	}
}

if (!$oUsuario) {
	\Zage\App\Erro::halt('Usuário não encontrado');
	
}

$usuario	= $oUsuario->getUsuario();
$codUsuario	= $oUsuario->getCodigo();
$nome 		= $oUsuario->getNome();
$apelido	= $oUsuario->getApelido();
$cpf		= $oUsuario->getCpf();
$sexo		= $oUsuario->getSexo()->getDescricao();
$status		= $oUsuario->getCodStatus()->getDescricao();

$oUsuarioOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));
if (!$oUsuarioOrg){
	$perfil = null;
}else{
	$perfil = $oUsuarioOrg->getCodPerfil()->getCodigo();	
}

#################################################################################
## Select de perfil
#################################################################################

try {
	$aPerfil	= \Zage\Seg\Perfil::listaPerfilOrganizacao($codOrganizacao);
	$oPerfil	= $system->geraHtmlCombo($aPerfil, 'CODIGO', 'NOME', $perfil , null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Verificar Associação
#################################################################################
$oUsuarioOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));

if($oUsuarioOrg){
	if ($oUsuarioOrg->getCodStatus()->getCodigo() == A){
		$texto = 'Esté usuário já está associado a organizacao.';
		$podeEnviar = 'disabled';
	}elseif ($oUsuarioOrg->getCodStatus()->getCodigo() == P){
		$texto = 'Este usuário já está associado a organização, porém a confirmação do usuário ainda está pendente. Clique em associar para enviar um novo convite ao usuário.';
	}
}else{
	$texto = 'Indentificamos em nosso sistema um usuário cadastrado com este '.$campo.'. Certifique-se que esteja informando os dados corretamente e clique em associar para cadastrá-lo na organização.';
}

#################################################################################
## Urls
#################################################################################
$vid				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$codOrganizacao.'&url='.$url);
$urlVoltar			= ROOT_URL . "/Fmt/parceiroUsuarioLis.php?id=".$vid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Associar usuário');

$tpl->set('COD_ORGANIZACAO'		,$codOrganizacao);
$tpl->set('COD_USUARIO'			,$codUsuario);
$tpl->set('NOME'				,$nome);
$tpl->set('USUARIO'				,$usuario);
$tpl->set('APELIDO'				,$apelido);
$tpl->set('CPF'					,$cpf);
$tpl->set('SEXO'				,$sexo);
$tpl->set('STATUS'				,$status);
$tpl->set('CAMPO'				,$campo);
$tpl->set('PERFIL'				,$oPerfil);

$tpl->set('DISABLED'			,$podeEnviar);
$tpl->set('TEXTO'				,$texto);

$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


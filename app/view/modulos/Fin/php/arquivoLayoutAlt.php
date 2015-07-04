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
if (!isset($codLayout)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codLayout)) {
	try {
		$info = $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$codTipo		= ($info->getCodTipoLayout()) 	? $info->getCodTipoLayout()->getCodigo() 	: null; 
	$codBanco		= ($info->getCodBanco()) 		? $info->getCodBanco()->getCodigo() 		: null;
	$nome			= $info->getNome();
	
}else{
	$codTipo		= null; 
	$codBanco		= null;
	$nome			= null;
}

#################################################################################
## Select do Tipo de Layout
#################################################################################
try {
	$aTipo		= $em->getRepository('Entidades\ZgfinArquivoLayoutTipo')->findBy(array(),array('nome' => 'ASC'));
	$oTipo		= $system->geraHtmlCombo($aTipo,	'CODIGO', 'NOME',	$codTipo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/arquivoLayoutLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codLayout=');
$urlNovo			= ROOT_URL."/Fin/arquivoLayoutAlt.php?id=".$uid;

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
$tpl->set('COD_LAYOUT'			,$codLayout);
$tpl->set('COD_BANCO'			,$codBanco);
$tpl->set('NOME'				,$nome);
$tpl->set('TIPOS'				,$oTipo);
$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


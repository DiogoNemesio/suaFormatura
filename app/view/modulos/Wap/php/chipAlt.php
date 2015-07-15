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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

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
if (!isset($codChip)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codChip)) {
	try {
		$info = $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$identificacao	= $info->getIdentificacao();
	$numero			= $info->getDdd() . $info->getNumero();
	$status			= $info->getCodStatus()->getCodigo();
	

}else{
	$identificacao	= null;
	$numero			= null;
	$status			= null;
}


#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Wap/chipLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codChip=');
$urlNovo			= ROOT_URL."/Wap/chipAlt.php?id=".$uid;

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
$tpl->set('COD_CHIP'			,$codChip);
$tpl->set('IDENTIFICACAO'		,$identificacao);
$tpl->set('NUMERO'				,$numero);
$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValorSistema('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

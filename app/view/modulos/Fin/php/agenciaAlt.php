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
if (!isset($codAgencia)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codAgencia)) {
	try {
		$info = $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codAgencia));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$codBanco		= ($info->getCodBanco() != null) ? $info->getCodBanco()->getCodigo() : null;
	$agencia		= $info->getAgencia();
	$agenciaDV		= $info->getAgenciaDV();
	$nome			= $info->getNome();
	$banco			= ($info->getCodBanco() != null) ? $info->getCodBanco()->getCodBanco() . ' / '.$info->getCodBanco()->getNome() : null;
	
}else{
	$codBanco		= null;
	$agencia		= null;
	$agenciaDV		= null;
	$nome			= null;
	$banco			= null;
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/agenciaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codAgencia=');
$urlNovo			= ROOT_URL."/Fin/agenciaAlt.php?id=".$uid;

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
$tpl->set('COD_AGENCIA'			,$codAgencia);
$tpl->set('AGENCIA'				,$agencia);
$tpl->set('AGENCIADV'			,$agenciaDV);
$tpl->set('COD_BANCO'			,$codBanco);
$tpl->set('BANCO'				,$banco);
$tpl->set('NOME'				,$nome);
$tpl->set('APP_BS_TA_MINLENGTH'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_MINLENGTH'));
$tpl->set('APP_BS_TA_ITENS'		,\Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS'));
$tpl->set('APP_BS_TA_TIMEOUT'	,\Zage\Adm\Parametro::getValor('APP_BS_TA_TIMEOUT'));
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


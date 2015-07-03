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
if (!isset($codFeriadoFilial)) \Zage\App\Erro::halt('Falta de Parâmetros');


#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codFeriadoFilial)) {
	try {
		$info = $em->getRepository('Entidades\ZgfinFeriadoFilial')->findOneBy(array('codFilial' => $system->getCodEmpresa(), 'codigo' => $codFeriadoFilial));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$descricao		= ($info->getDescricao()) ? $info->getDescricao() : null;
	$dia			= ($info->getDia()) ? $info->getDia() : null;
	$mes			= ($info->getMes()) ? $info->getMes() : null;
}else{
	$descricao		= null;
	$dia			= null;
	$mes			= null;
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Fin/feriadoFilialFixoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codFeriadoFilial=');
$urlNovo			= ROOT_URL."/Fin/feriadoFilialFixoAlt.php?id=".$uid;

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
$tpl->set('COD_FERIADO_FILIAL'	,$codFeriadoFilial);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('DIA'					,$dia);
$tpl->set('MES'					,$mes);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


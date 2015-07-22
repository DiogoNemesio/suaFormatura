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
if (!isset($codChip) || empty($codChip)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do banco
#################################################################################
try {
	$info = $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$identificacao	= $info->getIdentificacao();
$numero			= $info->getDdd() . $info->getNumero();
$status			= $info->getCodStatus()->getCodigo();
$codPais		= ($info->getCodPais()) ? $info->getCodPais()->getCodigo() : null;
	

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Wap/chipLis.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,'Registro do Chip');
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));
$tpl->set('COD_CHIP'			,$codChip);
$tpl->set('IDENTIFICACAO'		,$identificacao);
$tpl->set('NUMERO'				,$numero);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
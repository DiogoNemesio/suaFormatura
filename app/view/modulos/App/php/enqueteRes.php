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
if (!isset($codEnquete)) \Zage\App\Erro::halt('Falta de Parâmetros');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codEnquete)) {
	try {
		$info 	 = $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codigo' => $codEnquete));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}

	$pergunta		= ($info->getPergunta()) ? $info->getPergunta() : null;
	$codTipo		= ($info->getCodTipo()) ? $info->getCodTipo()->getCodigo() : null;
}else{
	$pergunta		= null;
	$codTipo		= null;
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/enqueteLis.php?id=".$id;

#################################################################################
## Url Atualizar
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete='.$codEnquete);
$urlAtualizar		= ROOT_URL."/App/enqueteRes.php?id=".$uid;

$urlResposta		= ROOT_URL."/App/respostaLis.php?id=".$uid;
$urlGraphEnquete    = ROOT_URL."/App/enqueteGraph.php?id=".$uid;
$urlGraphEnquetePerc= ROOT_URL."/App/enquetePercGraph.php?id=".$uid;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'				,$_SERVER['SCRIPT_NAME']);
$tpl->set('URLVOLTAR'				,$urlVoltar);
$tpl->set('URLATUALIZAR'			,$urlAtualizar);
$tpl->set('RESPOSTA'				,$urlResposta);
$tpl->set('ID'						,$id);
$tpl->set('COD_ENQUETE'				,$codEnquete);
$tpl->set('PERGUNTA'				,$pergunta);
$tpl->set('TIPO'					,$oTipo);
$tpl->set('URL_GRAPH_ENQUETE'		,$urlGraphEnquete);
$tpl->set('URL_GRAPH_ENQUETE_PERC'	,$urlGraphEnquetePerc);

$tpl->set('DP'						,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


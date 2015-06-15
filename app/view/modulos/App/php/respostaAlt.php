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
if (!isset($codResposta)) \Zage\App\Erro::halt('Falta de Parâmetros');

#################################################################################
## Resgata as informações do banco
#################################################################################
if (!empty($codResposta)) {
	try {
		$info 	 = $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codigo' => $codResposta));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	/** Pergunta **/
	$codPergunta	= ($info->getCodPergunta()) ? $info->getCodPergunta()->getCodigo() : null;
	$resposta		= ($info->getResposta()) ? $info->getResposta() : null;
	$dataResposta	= ($info->getDataResposta() != null) ? $info->getDataResposta()->format($system->config["data"]["dateFormat"]) : null;
	$codUsuario		= ($info->getCodUsuario()) ? $info->getCodUsuario()->getCodigo() : null;	
	
}else{
	/** Resposta **/
	$codPergunta	= null;
	$resposta		= null;
	$dataResposta	= null;
	$codUsuario		= null;
}

#################################################################################
## Select de Tipo Pergunta
#################################################################################
try {
	$aPergunta		= $em->getRepository('Entidades\ZgappEnquetePergunta')->findBy(array(),array('pergunta' => 'ASC'));
	$oPergunta		= $system->geraHtmlCombo($aPergunta,'CODIGO', 'PERGUNTA',	$codPergunta, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/respostaLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codResposta=');
$urlNovo			= ROOT_URL."/App/respostaAlt.php?id=".$uid;

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
$tpl->set('COD_RESPOSTA'		,$codResposta);
$tpl->set('RESPOSTA'			,$resposta);
$tpl->set('DATA_RESPOSTA'		,$dataResposta);
$tpl->set('COD_USUARIO'			,$codUsuario);
$tpl->set('COD_PERGUNTA'		,$oPergunta);

$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


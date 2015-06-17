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
		$info 	 = $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
		$infoVal = $em->getRepository('Entidades\ZgappEnquetePerguntaValor')->findBy(array('codPergunta' => $codEnquete));
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	/** Pergunta **/
	$descricao		= $info->getDescricao();
	$dataPrazo		= ($info->getDataPrazo() != null) ? $info->getDataPrazo()->format($system->config["data"]["datetimeSimplesFormat"]) : null;
	$codTipo		= ($info->getCodTipo()) ? $info->getCodTipo()->getCodigo() : null;
	$pergunta		= ($info->getPergunta()) ? $info->getPergunta() : null;	
	$tamanho		= $info->getTamanho();
	if ($infoVal) {
		$valores = null;
		foreach ($infoVal as $val) {
			$valores		.= $val->getValor().',';
		}
		$valores	= substr($valores,0,-1);
	}else{
		$valores = null;
	}
	
}else{
	/** Pergunta **/
	$descricao		= null;
	$dataPrazo		= null;
	$codTipo		= null;
	$pergunta		= null;
	$tamanho		= null;
	$valores		= null;
}

#################################################################################
## Select de Tipo Pergunta
#################################################################################
try {
	$aTipo		= $em->getRepository('Entidades\ZgappEnquetePerguntaTipo')->findAll(array('indAtivo' => 1));
	$oTipo		= $system->geraHtmlCombo($aTipo,'CODIGO', 'DESCRICAO',	$codTipo, '');
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}
#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/App/enqueteLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid = \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEnquete=');
$urlNovo			= ROOT_URL."/App/enqueteAlt.php?id=".$uid;

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
$tpl->set('COD_ENQUETE'			,$codEnquete);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('DATA_PRAZO'			,$dataPrazo);
$tpl->set('COD_TIPO'			,$oTipo);
$tpl->set('PERGUNTA'			,$pergunta);
$tpl->set('TAMANHO'				,$tamanho);
$tpl->set('VALORES'				,$valores);

$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


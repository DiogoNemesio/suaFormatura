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
## Resgata as informações do banco
#################################################################################
if ($codIndice) {
	try {
		$info			= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('codigo' => $codIndice));
		$infoVal		= $em->getRepository('Entidades\ZgdocIndiceTipoValor')->findBy(array('codIndice' => $codIndice));
		
	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	$codTipoDoc		= $info->getCodDocumentoTipo()->getCodigo();
	$nome			= $info->getNome();
	$descricao		= $info->getDescricao();
	$obrigatorio	= ($info->getIndObrigatorio() 	== 1) ? "checked" : null;
	$visivel		= ($info->getIndVisivel()		== 1) ? "checked" : null;
	$ativo			= ($info->getIndAtivo()			== 1) ? "checked" : null;
	$tipo			= $info->getCodTipo()->getCodigo();
	$tamanho		= $info->getTamanho();
	$mascara		= $info->getMascara();
	$valores		= '';
	$valorPadrao	= $info->getValorPadrao();
	
	if ($infoVal) {
		foreach ($infoVal as $val) {
			$valores		.= $val->getValor().',';
		}
		$valores	= substr($valores,0,-1);
	}
	
}else{
	
	$nome			= '';
	$descricao		= '';
	$obrigatorio	= 'checked';
	$visivel		= 'checked';
	$tipo			= 'T';
	$tamanho		= '';
	$mascara		= '';
	$ativo			= 'checked';
	$valores		= '';
	$valorPadrao	= '';
}

#################################################################################
## Url Voltar
#################################################################################
$urlVoltar			= ROOT_URL."/Doc/docTipoLis.php?id=".$id;

#################################################################################
## Url Novo
#################################################################################
$uid 				= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codIndice='.'&codTipoDoc='.$codTipoDoc);
$urlNovo			= ROOT_URL."/Doc/indiceAlt.php?id=".$uid;

#################################################################################
## Select de Tipos
#################################################################################
try {
	$aIndiceTipo	= $em->getRepository('Entidades\ZgdocIndiceTipo')->findAll();
	$oIndiceTipo	= $system->geraHtmlCombo($aIndiceTipo, 'CODIGO', 'NOME', $tipo, null);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

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
$tpl->set('COD_INDICE'			,$codIndice);
$tpl->set('NOME'				,$nome);
$tpl->set('DESCRICAO'			,$descricao);
$tpl->set('INDICE_TIPO'			,$oIndiceTipo);
$tpl->set('TAMANHO'				,$tamanho);
$tpl->set('OBRIGATORIO'			,$obrigatorio);
$tpl->set('VISIVEL'				,$visivel);
$tpl->set('DOC_TIPO'			,$codTipoDoc);
$tpl->set('MASCARA'				,$mascara);
$tpl->set('ATIVO'				,$ativo);
$tpl->set('VALORES'				,$valores);
$tpl->set('VALOR_PADRAO'		,$valorPadrao);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
## Resgata os parâmetros passados pelo formulario de pesquisa
#################################################################################
if (isset($_GET['codDocumento'])) 		{
	$codDocumento		= \Zage\App\Util::antiInjection($_GET['codDocumento']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codDocumento')));
}

if (isset($_GET['codArquivo'])) {
	$codArquivo		= \Zage\App\Util::antiInjection($_GET['codArquivo']);
}else{
	$codArquivo		= null;
}

if (isset($_GET['width'])) {
	$width			= \Zage\App\Util::antiInjection($_GET['width']);
}else{
	$width			= 800;
}
if (isset($_GET['height'])) {
	$height			= \Zage\App\Util::antiInjection($_GET['height']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'height')));
}



#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Doc/'. basename(__FILE__).'?id='.$id;
$urlVoltar	= ROOT_URL . '/Doc/docIndex.php?id='.$id;


#################################################################################
## Resgata os dados do Documento, dos índices e dos Arquivos 
#################################################################################
try {
	
	$info			= $em->getRepository('Entidades\ZgdocDocumento')->findOneBy(array('codigo' => $codDocumento));

	if (!$info) {
		\Zage\App\Erro::halt($tr->trans("documento não existe"));
	}
	
	$filtros		= array('codDocumento' => $codDocumento);
	
	if ($codArquivo !== null) {
		$filtros['codigo'] = $codArquivo;
	}
	
	$arquivos		= $em->getRepository('Entidades\ZgdocArquivoInfo')->findBy($filtros, array('dataCadastro' => 'DESC'));
	$indices		= \Zage\Doc\Indice::lista($info->getCodTipo()->getCodigo());

	$dataCadastro	= $arquivos[0]->getDataCadastro()->format($system->config["data"]["datetimeFormat"]);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Select dos arquivos
#################################################################################
try {
	$oArquivos	= $system->geraHtmlCombo($arquivos,	'CODIGO', 'NOME', 	$arquivos[0]->getCodigo(), null);
	
	$type		= $arquivos[0]->getMimetype();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Gerar a url de visualização
#################################################################################
$downid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codArquivo='.$arquivos[0]->getCodigo());
$urlDoc		= ROOT_URL . '/Doc/View/'.$downid;
$urlDown	= ROOT_URL . '/Doc/Down/'.$downid;

if ($type == 'application/pdf') {
	$urlView	= PKG_URL .'/pdfjs/web/viewer.html?file='.$urlDoc;
}elseif (strpos($type, "image") === true) {
	//$urlView = '<center><img src="'.$urlDoc.'" width="100%" height="100%"></img></center>';
	$urlView = $urlDoc;
}else{
	//$urlView = '<object	data="'.$urlDoc.'" type="'.$type.'" width="100%" height="100%"><br><center><a href="'.$urlDown.'" class="btn btn-default btn-sm">Download<i class="fa fa-download"></i></a></center></object>';
	$urlView = $urlDoc;
}

#################################################################################
## Criação do formulário de índices
#################################################################################
$htmlForm	= "";
for ($i = 0; $i < sizeof($indices); $i++) {
	
	$idCampo	= \Zage\Doc\Indice::geraIdInput($indices[$i]->getCodigo());
	$htmlForm	.= '<div class="col-sm-12">';
	$htmlForm	.= '<div class="form-group">';
	$htmlForm	.= '<label class="col-sm-5 control-label" for="'.$idCampo.'">'.$indices[$i]->getNome().'</label>';
	$htmlForm	.= '<div class="input-group col-sm-7">';
	$htmlForm	.= \Zage\Doc\Indice::geraHtml($indices[$i]->getCodigo(),$codDocumento,($i+1));
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('COD_DOCUMENTO'		,$codDocumento);
$tpl->set('INDICES'				,$htmlForm);
$tpl->set('COD_DOCUMENTO'		,$codDocumento);
$tpl->set('ARQUIVOS'			,$oArquivos);
$tpl->set('DATA_CADASTRO'		,$dataCadastro);
$tpl->set('URL_DOCUMENTO'		,$urlDoc);
$tpl->set('URL_VIEW'			,$urlView);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

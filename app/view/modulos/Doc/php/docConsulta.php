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
## Resgata os parâmetros
#################################################################################
if (isset($_GET['codTipoDoc'])) 		{
	$codTipoDoc		= \Zage\App\Util::antiInjection($_GET['codTipoDoc']);
}else{
	$codTipoDoc		= null;
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Doc/'. basename(__FILE__).'?id='.$id;
$urlForm	= ROOT_URL . '/Doc/docConsultaLis.php?id='.$id;

#################################################################################
## Select do Tipo de Documento
#################################################################################
try {
	$aDocTipo	= \Zage\Doc\DocumentoTipo::listaTodos();
	
	if ($codTipoDoc == null && $aDocTipo) {
		$codTipoDoc	= $aDocTipo[0]->getCodigo();
	}
	
	$oDocTipo	= $system->geraHtmlCombo($aDocTipo,	'CODIGO', 'NOME', $codTipoDoc, null);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgata os índices
#################################################################################
$indices		= \Zage\Doc\Indice::lista($codTipoDoc);

#################################################################################
## Criação do formulário de índices
#################################################################################
$htmlForm	= "";
for ($i = 0; $i < sizeof($indices); $i++) {
	$idCampo	= \Zage\Doc\Indice::geraIdInput($indices[$i]->getCodigo());
	
	if (($i % 2) == 0) {
		$htmlForm	.= '<div class="row">';
	}
	
	$htmlForm	.= '<div class="col-sm-6">';
	$htmlForm	.= '<div class="form-group">';
	$htmlForm	.= '<label class="col-sm-4 control-label" for="'.$idCampo.'">'.$indices[$i]->getNome().'</label>';
	$htmlForm	.= '<div class="input-group col-sm-6">';
	$htmlForm	.= \Zage\Doc\Indice::geraHtml($indices[$i]->getCodigo(),null,($i+1));
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
	$htmlForm	.= '</div>';
	
	if (($i % 2) != 0) {
		$htmlForm	.= '</div>';
	}
}
if ( (sizeof($indices) > 0) &&  (($i % 2) != 0)) {
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
$tpl->set('ID'					,$id);
$tpl->set('RELOAD_URL'			,$url);
$tpl->set('INDICES'				,$htmlForm);
$tpl->set('DOC_TIPO'			,$oDocTipo);
$tpl->set('COD_TIPO_DOC'		,$codTipoDoc);
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());
$tpl->set('URL_FORM'			,$urlForm);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (!isset($codTipoDoc)) 		{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codTipoDoc')));
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Doc/". basename(__FILE__)."?id=".$id;
$urlIndice	= ROOT_URL . "/Doc/docIndexAlt.php?id=".$id;
$urlDoc		= ROOT_URL . "/Doc/docView.php?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	
	$info			= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipoDoc));

	if (!$info) {
		\Zage\App\Erro::halt($tr->trans("Tipo de documento não existe"));
	}
	
	if (isset($codDocumento) && !empty($codDocumento)) {
		$documentos		= $em->getRepository('Entidades\ZgdocDocumento')->findBy(array('codigo' => $codDocumento));
	}else{
		$documentos		= \Zage\Doc\Documento::listaNaoIndexados($codTipoDoc);
	}

	
	$indices		= \Zage\Doc\Indice::lista($codTipoDoc);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Criação dos Divs e das listas
#################################################################################
$divs	= "";
$lis	= "";
for ($i = 0; $i < sizeof($documentos); $i++) {
	if ($i == 0) {
		$primeiroDoc	= $documentos[$i]->getCodigo();
		$active 		= "active";
	}else{
		$active 		= "";
	}
	$lis	.= '<li id="zgLi_'.$documentos[$i]->getCodigo().'" zg-data-codigo="'.$documentos[$i]->getCodigo().'" zg-data-ordem="'.($i+1).'" class="'.$active.'"><a href="javascript:zgAbreDoc(\''.$documentos[$i]->getCodigo().'\');">'.($i+1).'</a></li>';
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('NUM_DOCS'		,sizeof($documentos));
$tpl->set('NOME_TIPO'		,$info->getNome());
$tpl->set('URL_INDICE'		,$urlIndice);
$tpl->set('URL_DOCUMENTO'	,$urlDoc);
$tpl->set('LIS_DOCS'		,$lis);
$tpl->set('PRIMEIRO_DOC'	,$primeiroDoc);
$tpl->set('DIV_HEIGHT'		,842);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

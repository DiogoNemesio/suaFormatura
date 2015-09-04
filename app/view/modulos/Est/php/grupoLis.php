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
if (isset($_GET['busca'])) 			$busca			= \Zage\App\Util::antiInjection($_GET['busca']);

#################################################################################
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['codGrupo'])) 		$codGrupoSel	= \Zage\App\Util::antiInjection($_GET['codGrupo']);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL."/Est/".basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados da árvore
#################################################################################
try {

	$arvore		= new \Zage\App\Arvore();
	$pastas		= \Zage\Est\Grupo::listaTodas();
	
	$arvore->exibirRaiz(true);
	
	for ($i = 0; $i < sizeof($pastas); $i++) {
		$pastaMae	= ($pastas[$i]->getCodGrupoPai()) ? $pastas[$i]->getCodGrupoPai()->getCodigo() : null;
		$arvore->adicionaPasta($pastas[$i]->getCodigo(), $pastas[$i]->getDescricao(), $pastaMae);
		/*$tipos		= \Zage\Doc\DocumentoTipo::lista($pastas[$i]->getCodigo());
		
		for ($j = 0; $j < sizeof($tipos); $j++) {
			$pastaMae	= ($tipos[$j]->getCodPasta()) ? $tipos[$j]->getCodPasta()->getCodigo() : null;
			$arvore->adicionaItem($tipos[$j]->getCodigo(),$tipos[$j]->getNome(), $pastaMae);
		}*/
	}
	
	if (isset($busca) && (!empty($busca)) ) {
		$filtro		= $arvore->filtrar($busca);
		//$log->debug(serialize($filtro));
	
	}
	
	
} catch(\Exception $e) {
	$result['status'] = 'ERR';
	$result['message'] = $e->getMessage();
	exit;
}

if (!isset($codGrupoSel)) {
	$codGrupoSel	= null;
}

if (isset($busca) && (!empty($busca))) {
	$filtro = '<button class="btn btn-white btn-info" onclick="buscaArvore();"><i class="ace-icon fa fa-times bigger-120 red"></i>Filtro: '.$busca.'</button>';
}else{
	$filtro	= null;
}

//$debug = $arvore->getJsonCode();
//$log->debug($debug);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TREE_DATA'			,$arvore->getJsonCode());
//$tpl->set('TREE_DATA'			,json_encode($arvore->geraArray()));
$tpl->set('TARGET'				,$system->getDivCentral());
$tpl->set('URL'					,$url);
$tpl->set('COD_GRUPO_SEL'		,$codGrupoSel);
$tpl->set('COD_GRUPO_RAIZ'		,\Zage\App\Arvore::_codPastaRaiz);
$tpl->set('FILTRO'				,$filtro);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


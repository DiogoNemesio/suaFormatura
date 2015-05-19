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
if (isset($_GET['busDeb'])) 			$busDeb				= \Zage\App\Util::antiInjection($_GET['busDeb']);
if (isset($_GET['busCre'])) 			$busCre				= \Zage\App\Util::antiInjection($_GET['busCre']);

#################################################################################
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['codCategoria'])) 		$codCategoriaSel	= \Zage\App\Util::antiInjection($_GET['codCategoria']);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL."/Fin/".basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados da árvore
#################################################################################
try {

	$arvDeb			= new \Zage\App\Arvore();
	$arvCre			= new \Zage\App\Arvore();
	$catDeb			= \Zage\Fin\Categoria::lista("D",null);
	$catCre			= \Zage\Fin\Categoria::lista("C",null);
	
	$arvDeb->exibirRaiz(true);
	$arvCre->exibirRaiz(true);
	
	for ($i = 0; $i < sizeof($catDeb); $i++) {
		$catPai		= ($catDeb[$i]->getCodCategoriaPai()) ? $catDeb[$i]->getCodCategoriaPai()->getCodigo() : null;
		$indice		= $arvDeb->adicionaPasta($catDeb[$i]->getCodigo(), $catDeb[$i]->getDescricao(), $catPai);
		$catFilhas	= \Zage\Fin\Categoria::lista($catDeb[$i]->getCodTipo()->getCodigo(),$catDeb[$i]->getCodigo());
		
		if ($catDeb[$i]->getIndAtiva() == 0)	$arvDeb->desabilitaItem($indice);
		
		for ($j = 0; $j < sizeof($catFilhas); $j++) {
			$catPai		= ($catFilhas[$j]->getCodCategoriaPai()) ? $catFilhas[$j]->getCodCategoriaPai()->getCodigo() : null;
			$indice 	= $arvDeb->adicionaItem($catFilhas[$j]->getCodigo(),$catFilhas[$j]->getDescricao(), $catPai);
			if ($catFilhas[$j]->getIndAtiva() == 0)	$arvDeb->desabilitaItem($indice);
		}
	}
	
	for ($i = 0; $i < sizeof($catCre); $i++) {
		$catPai		= ($catCre[$i]->getCodCategoriaPai()) ? $catCre[$i]->getCodCategoriaPai()->getCodigo() : null;
		$indice		= $arvCre->adicionaPasta($catCre[$i]->getCodigo(), $catCre[$i]->getDescricao(), $catPai);
		$catFilhas	= \Zage\Fin\Categoria::lista($catCre[$i]->getCodTipo()->getCodigo(),$catCre[$i]->getCodigo());
	
		if ($catCre[$i]->getIndAtiva() == 0)	$arvCre->desabilitaItem($indice);
		
		for ($j = 0; $j < sizeof($catFilhas); $j++) {
			$catPai		= ($catFilhas[$j]->getCodCategoriaPai()) ? $catFilhas[$j]->getCodCategoriaPai()->getCodigo() : null;
			$indice 	= $arvCre->adicionaItem($catFilhas[$j]->getCodigo(),$catFilhas[$j]->getDescricao(), $catPai);
			if ($catFilhas[$j]->getIndAtiva() == 0)	$arvCre->desabilitaItem($indice);
		}
	}
	
	if (isset($busDeb) && (!empty($busDeb)) ) {
		$arvDeb->filtrar($busDeb);
		$filtroDeb 	= '<button class="btn btn-white btn-info" onclick="buscaArvoreDeb();"><i class="ace-icon fa fa-times bigger-120 red"></i>Filtro: '.$busDeb.'</button>';
	}else{
		$filtroDeb 	= null;
	}
	
	if (isset($busCre) && (!empty($busCre)) ) {
		$arvCre->filtrar($busCre);
		$filtroCre 	= '<button class="btn btn-white btn-info" onclick="buscaArvoreCre();"><i class="ace-icon fa fa-times bigger-120 red"></i>Filtro: '.$busCre.'</button>';
	}else{
		$filtroCre 	= null;
	}
	
} catch(\Exception $e) {
	$result['status'] = 'ERR';
	$result['message'] = $e->getMessage();
	exit;
}

if (!isset($codCategoriaSel)) {
	$codCategoriaSel	= null;
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
$tpl->set('TREE_DATA_DEB'		,$arvDeb->getJsonCode());
$tpl->set('TREE_DATA_CRE'		,$arvCre->getJsonCode());
$tpl->set('TARGET'				,$system->getDivCentral());
$tpl->set('URL'					,$url);
$tpl->set('COD_PASTA_SEL'		,$codCategoriaSel);
$tpl->set('COD_PASTA_RAIZ'		,\Zage\App\Arvore::_codPastaRaiz);
$tpl->set('FILTRO_DEB'			,$filtroDeb);
$tpl->set('FILTRO_CRE'			,$filtroCre);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


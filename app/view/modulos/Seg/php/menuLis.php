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
if (isset($_GET['busca'])) 			$busca		= \Zage\App\Util::antiInjection($_GET['busca']);
if (isset($_GET['codModulo'])) 		$codModulo	= \Zage\App\Util::antiInjection($_GET['codModulo']);

#################################################################################
## Resgata os parâmetros passados
#################################################################################
if (isset($_GET['codPasta'])) 		$codPastaSel	= \Zage\App\Util::antiInjection($_GET['codPasta']);


#################################################################################
## Resgata os dados da árvore
#################################################################################
try {

	$modulos	= $em->getRepository('Entidades\ZgappModulo')->findAll();

	if (isset($codModulo) && (!empty($codModulo))) {
		$_SESSION["_segCodModulo"]	= $codModulo;
	}elseif (!isset($_SESSION["_segCodModulo"]) || (empty($_SESSION["_segCodModulo"]))) {
		$codModulo 					= $modulos[0]->getCodigo();
		$_SESSION["_segCodModulo"]	= $codModulo;
	}else{
		$codModulo		= $_SESSION["_segCodModulo"];
	}
	
	$arvore		= new \Zage\App\Arvore();
	$arvore->exibirRaiz(true);
	$menus		= $em->getRepository('Entidades\ZgappMenu')->findBy(array('indFixo' => 0,'codTipo' => "M",'codModulo' => $codModulo),array('nivel' => 'ASC','codigo' => 'ASC'));
	
	for ($i = 0; $i < sizeof($menus); $i++) {
		$pastaMae	= ($menus[$i]->getCodMenuPai()) ? $menus[$i]->getCodMenuPai()->getCodigo() : null;
		$arvore->adicionaPasta($menus[$i]->getCodigo(), $menus[$i]->getNome(), $pastaMae);
		$links	 = $em->getRepository('Entidades\ZgappMenu')->findBy(array('indFixo' => 0,'codTipo' => "L","codMenuPai" => $menus[$i]->getCodigo()),array('nivel' => 'ASC','codigo' => 'ASC'));
		
		for ($j = 0; $j < sizeof($links); $j++) {
			$pastaMae	= ($links[$j]->getCodMenuPai()) ? $links[$j]->getCodMenuPai()->getCodigo() : null;
			$arvore->adicionaItem($links[$j]->getCodigo(),$links[$j]->getNome(), $pastaMae);
		}
	}
	
	$links	 = $em->getRepository('Entidades\ZgappMenu')->findBy(array('indFixo' => 0,'codTipo' => "L",'codModulo' => $codModulo,"codMenuPai" => null),array('nivel' => 'ASC','codigo' => 'ASC'));
	for ($j = 0; $j < sizeof($links); $j++) {
		$pastaMae	= ($links[$j]->getCodMenuPai()) ? $links[$j]->getCodMenuPai()->getCodigo() : null;
		$arvore->adicionaItem($links[$j]->getCodigo(),$links[$j]->getNome(), $pastaMae);
	}
	
	if (isset($busca) && (!empty($busca)) ) {
		$filtro		= $arvore->filtrar($busca);
	}
	
	
} catch(\Exception $e) {
	$result['status'] = 'ERR';
	$result['message'] = $e->getMessage();
	exit;
}

if (!isset($codPastaSel)) {
	$codPastaSel	= null;
}


#################################################################################
## Select dos módulos
#################################################################################
try {
	$oModulos	= $system->geraHtmlCombo($modulos,	'CODIGO', 'NOME', $codModulo, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}


if (isset($busca) && (!empty($busca))) {
	$filtro = '<button class="btn btn-white btn-info" onclick="buscaArvore();"><i class="ace-icon fa fa-times bigger-120 red"></i>Filtro: '.$busca.'</button>';
}else{
	$filtro	= null;
}

#################################################################################
## Resgata a url desse script
#################################################################################
$pid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codModulo='.$codModulo);
$url			= ROOT_URL."/Seg/".basename(__FILE__)."?id=".$id;
$menuPerfilUrl	= ROOT_URL."/Seg/menuPerfilLis.php?id=".$pid;



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
$tpl->set('TARGET'				,$system->getDivCentral());
$tpl->set('URL'					,$url);
$tpl->set('COD_PASTA_SEL'		,$codPastaSel);
$tpl->set('COD_PASTA_RAIZ'		,\Zage\App\Arvore::_codPastaRaiz);
$tpl->set('MODULOS'				,$oModulos);
$tpl->set('COD_MODULO'			,$codModulo);
$tpl->set('FILTRO'				,$filtro);
$tpl->set('MENU_PERFIL_URL'		,$menuPerfilUrl);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


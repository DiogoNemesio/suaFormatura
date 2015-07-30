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
if (isset($_GET['codRifa'])) 		$codRifa	= \Zage\App\Util::antiInjection($_GET['codRifa']);

#################################################################################
## Select dos rifas
#################################################################################
try {
	$rifas	= $em->getRepository('Entidades\ZgfmtRifa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'indRifaEletronica' => 1));
	
	$oRifas	= $system->geraHtmlCombo($rifas,	'CODIGO', 'NOME', $codRifa, null);

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
}

#################################################################################
## Resgatar informações da RIFA 
#################################################################################
if ($codRifa != null){
	$infoRifa = $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codRifa));
}else{
	$infoRifa = $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $rifas[0]->getCodigo()));
}

#################################################################################
## Resgata a url desse script
#################################################################################
$pid			= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codModulo='.$codRifa);
$url			= ROOT_URL."/Fmt/".basename(__FILE__)."?id=".$id;
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
$tpl->set('IC'					,$_icone_);

$tpl->set('URL'					,$url);

$tpl->set('RIFAS'				,$oRifas);
$tpl->set('NOME_RIFA'			,$infoRifa->getNome());
$tpl->set('VALOR_RIFA'			,$infoRifa->getValorUnitario());


$tpl->set('COD_RIFA'			,$codRifa);
$tpl->set('FILTRO'				,$filtro);
$tpl->set('DP'					,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


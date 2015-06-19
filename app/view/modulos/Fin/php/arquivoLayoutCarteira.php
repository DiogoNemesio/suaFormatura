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
if (!isset($codLayout)) \Zage\App\Erro::halt('Falta de Parâmetros 2');

#################################################################################
## Resgata as informações do layout
#################################################################################
$info			= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));
if (!$info)		\Zage\App\Erro::halt('Layout não encontrado !!!');

#################################################################################
## Lista de carteiras
#################################################################################
$carteiras		= $em->getRepository('Entidades\ZgfinCarteira')->findBy(array('codBanco' => $info->getCodBanco()->getCodigo()),array('codCarteira' => ASC));
$cartAss		= $em->getRepository('Entidades\ZgfinArquivoLayoutCarteira')->findBy(array('codLayout' => $codLayout));

$arrayCartAss 	= array();

for ($i = 0; $i < sizeof($cartAss); $i++) {
	$arrayCartAss[$i] = $cartAss[$i]->getCodCarteira()->getCodigo();
}

$htmlCart		= "";

for ($i = 0; $i < sizeof($carteiras); $i++) {

	if (in_array($carteiras[$i]->getCodigo(), $arrayCartAss)){
 		$selected = 'selected';
 	}else{
 		$selected = '';
 	}

	$htmlCart .= '<option value="'.$carteiras[$i]->getCodigo().'" '.$selected.'>'.$carteiras[$i]->getCodCarteira().'</option>';
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
$tpl->set('TITULO'				,'Carteiras associadas ao Layout');
$tpl->set('COD_LAYOUT'			,$codLayout);
$tpl->set('MENSAGEM'			,null);
$tpl->set('DUAL_LIST'			,$htmlCart);
$tpl->set('URL_VOLTAR'			,$urlVoltar);
$tpl->set('DP_MODAL'			,\Zage\App\Util::getCaminhoCorrespondente(__FILE__,\Zage\App\ZWS::EXT_DP,\Zage\App\ZWS::CAMINHO_RELATIVO));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


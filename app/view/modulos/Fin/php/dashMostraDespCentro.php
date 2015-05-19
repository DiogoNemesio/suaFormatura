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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['mesFiltro']))		$mesFiltro			= \Zage\App\Util::antiInjection($_POST['mesFiltro']);

if (!isset($mesFiltro)) {
	exit;
}


#################################################################################
## Calcular as datas da pesquisa
#################################################################################
$mes			= substr($mesFiltro,0,2);
$ano			= substr($mesFiltro,3,4);
$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes),1,$ano));
$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+1),0,$ano));
$hoje			= date($system->config["data"]["dateFormat"]);

if (\DateTime::createFromFormat($system->config["data"]["dateFormat"],$hoje) < \DateTime::createFromFormat($system->config["data"]["dateFormat"],$dataFim)) {
	$dataSaldo		= $hoje;
}else{
	$dataSaldo		= $dataFim;
}


#################################################################################
## Resgata as despesas por categoria
#################################################################################
$aStatus	= array("A","P","L");
try {
	$contas	= \Zage\Fin\ContaPagar::listaPorCentroCusto($dataIni,$dataFim,"V",null,null,null,$aStatus,null,null,null,null,null); 
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta os dados do Gráfico e 
#################################################################################
$htmlDiv		= "";
$data			= array();


for ($i = 0; $i < sizeof($contas); $i++) {
	//$data[$i]["label"]	= '<div class=\\\'col-sm-12\\\'><div class=\\\'col-sm-6\\\'><div class=\\\'pull-left\\\'>'.htmlentities($contas[$i]["categoria"]).'</div></div><div class=\\\'col-sm-6\\\'><div class=\\\'pull-right\\\'>'.\Zage\App\Util::to_money($contas[$i]["valor"]).'</div></div></div>';
	$data[$i]["label"]	= htmlentities($contas[$i]["centroCusto"]);
	$data[$i]["data"]	= $contas[$i]["valor"];
	$data[$i]["color"]	= \Zage\App\Util::geraCorAleatoria();
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRAPH_DATA'			,json_encode($data));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

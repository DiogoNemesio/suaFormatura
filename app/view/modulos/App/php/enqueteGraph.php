<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

global $em,$system;

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
if (isset($_POST['codEnquete']))		$codEnquete		= \Zage\App\Util::antiInjection($_POST['codEnquete']);

#################################################################################
## Verificar os parâmetros
#################################################################################
if (!isset($codEnquete) || (!$codEnquete)) {
	die('Parâmentro não informando : COD_ENQUENTE');
}

#################################################################################
## Resgata os dados dos resultado
#################################################################################
$qb 	= $em->createQueryBuilder();
	
try {
	$qb->select('er.resposta, count(er.resposta) as qtde')
	->from('\Entidades\ZgappEnqueteResposta','er')
	->where($qb->expr()->andX(
			$qb->expr()->eq('er.codPergunta'	, ':codEnquete')
	))
	->groupBy('er.resposta')
	->setParameter('codEnquete'	,$codEnquete);

	$query 		= $qb->getQuery();
	$respostas	= $query->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta os dados do Gráfico e 
#################################################################################
$htmlDiv		= "";
$data			= array();

for ($i = 0; $i < sizeof($respostas); $i++) {
	$data[$i]["label"]	= $respostas[$i]["resposta"];
	$data[$i]["data"]	= $respostas[$i]["qtde"];
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

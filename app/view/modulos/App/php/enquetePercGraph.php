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
	die('Parâmentro não informando : COD_ENQUETE');
}

#################################################################################
## Resgata os dados dos resultado
#################################################################################
$qb1 	= $em->createQueryBuilder();
$qb2 	= $em->createQueryBuilder();

$usuAtivos 		= \Zage\Seg\Usuario::listaUsuarioOrganizacaoAtivo($system->getCodOrganizacao(), F);
$usuAtivosNum 	= sizeof($usuAtivos);

try {
	$qb2->select('count(er.resposta) as qtde')
	->from('\Entidades\ZgappEnqueteResposta','er')
	->where($qb2->expr()->andX(
			$qb2->expr()->eq('er.codPergunta'	, ':codEnquete')
	))
	->setParameter('codEnquete'	,$codEnquete);

	$query	 		= $qb2->getQuery();
	$numRespostas	= $query->getSingleScalarResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta os dados do Gráfico
#################################################################################
$data			= array();
$numNaoResp		= ($usuAtivosNum - $numRespostas);
if ($numNaoResp < 0) $numNaoResp = 0; 

$data[0]["label"]	= htmlentities("Faltam responder");
$data[0]["data"]	= $numNaoResp;
$data[0]["color"]	= 'grey';

$data[1]["label"]	= htmlentities("Formandos que respoderam");
$data[1]["data"]	= $numRespostas;
$data[1]["color"]	= 'green';

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

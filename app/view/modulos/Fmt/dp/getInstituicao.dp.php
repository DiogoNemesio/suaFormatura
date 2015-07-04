<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	include_once('../includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['q']))					$q			    = \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codInstituicao']))		$codInstituicao	= \Zage\App\Util::antiInjection($_GET["codInstituicao"]);

if (isset($codInstituicao)) {
	$instituicoes		= $em->getRepository('Entidades\ZgfmtInstituicao')->findBy(array('codigo' => $codInstituicao));
}else{
	$instituicoes		= \Zage\Fmt\Instituicao::busca($q);
}


$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($instituicoes); $i++) {
	$array[$i]["id"]		= $instituicoes[$i]->getCodigo();
	$array[$i]["text"]		= '(' . $instituicoes[$i]->getSigla() . ') '.$instituicoes[$i]->getNome();
}

echo json_encode($array);
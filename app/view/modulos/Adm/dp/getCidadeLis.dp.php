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
if (isset($_GET['q']))					$q			= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codCidade']))			$codCidade	= \Zage\App\Util::antiInjection($_GET["codCidade"]);

if (isset($codCidade)) {
	$cidades	= $em->getRepository('Entidades\ZgadmCidade')->findBy(array('codigo' => $codCidade));
}else{
	$cidades	= \Zage\Adm\Cidade::busca($q);
}


$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValorSistema('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($cidades); $i++) {
	$array[$i]["id"]		= $cidades[$i]->getCodigo();
	$array[$i]["text"]		= $cidades[$i]->getCodUf()->getCodUf() . ' / '.$cidades[$i]->getNome();
	//if ($i > $numItens) break;
}

echo json_encode($array);
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
if (isset($_GET['q']))				$q			= \Zage\App\Util::antiInjection($_GET["q"]);
if (isset($_GET['codCbo']))			$codCbo	= \Zage\App\Util::antiInjection($_GET["codCbo"]);

if (isset($codCbo)) {
	$cbo	= $em->getRepository('Entidades\ZgrhuFuncionarioCbo')->findBy(array('codigo' => $codCbo));
}else{
	$cbo	= \Zage\Rhu\Cargo::buscaCbo($q);
}


$array		= array();
//$numItens	= \Zage\Adm\Parametro::getValor('APP_BS_TA_ITENS');

for ($i = 0; $i < sizeof($cbo); $i++) {
	$array[$i]["id"]		= $cbo[$i]->getCodigo();
	$array[$i]["text"]		= $cbo[$i]->getCodCbo() . ' / '.$cbo[$i]->getDescricao();
}

echo json_encode($array);
<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . '/includeNoAuth.php');
}else{
	include_once(DOC_ROOT . '/includeNoAuth.php');
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['cep']))			$cep			= \Zage\App\Util::antiInjection($_GET["cep"]);

$array				= array();

if (!$cep) {
	$array["codCidade"]			= null;
	$array["descCidade"]		= null;
	$array["codUf"]				= null;
	$array["descUf"]			= null;
	$array["codLogradouro"]		= null;
	$array["descLogradouro"]	= null;
	$array["codBairro"]			= null;
	$array["descBairro"]		= null;
	
	echo json_encode($array);
	exit;
}

$ret = \Zage\Adm\Endereco::buscaPorCep($cep);

if ($ret && (sizeof($ret) > 0)) {
	$array["codCidade"]			= $ret[0]->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodigo();
	$array["descCidade"]		= $ret[0]->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome();
	$array["codUf"]				= $ret[0]->getCodBairro()->getCodLocalidade()->getCodUf()->getCodUf();
	$array["descUf"]			= $ret[0]->getCodBairro()->getCodLocalidade()->getCodUf()->getNome();
	$array["codLogradouro"]		= $ret[0]->getCodigo();
	$array["descLogradouro"]	= $ret[0]->getDescricao();
	$array["codBairro"]			= $ret[0]->getCodBairro()->getCodigo();
	$array["descBairro"]		= $ret[0]->getCodBairro()->getDescricao();
		
}else{
	$array["codCidade"]			= null;
	$array["descCidade"]		= null;
	$array["codUf"]				= null;
	$array["descUf"]			= null;
	$array["codLogradouro"]		= null;
	$array["descLogradouro"]	= null;
	$array["codBairro"]			= null;
	$array["descBairro"]		= null;

}

echo json_encode($array);
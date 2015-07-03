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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codPessoa']))			$codPessoa			= \Zage\App\Util::antiInjection($_GET["codPessoa"]);

$array				= array();
$array["nome"]		= null;
$array["dataCad"]	= null;
$array["cgc"]		= null;
$array["indCli"]	= null;
$array["indFor"]	= null;
$array["indTra"]	= null;
$array["email"]		= null;


if (!isset($codPessoa) || empty($codPessoa)) {
	echo json_encode($array);
	exit;
}

$pessoa			= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codPessoa));

if ($pessoa) {
	$array["nome"]		= $pessoa->getNome();
	$array["dataCad"]	= ($pessoa->getDataCadastro() != null) 		? $pessoa->getDataCadastro()->format($system->config["data"]["dateFormat"]) : null;
	
	if ($pessoa->getCodTipoPessoa()->getCodigo() == "F") {
		$array["cgc"]	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($pessoa->getCgc());
	}else{
		$array["cgc"]	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($pessoa->getCgc());
	}
	
	$array["indCli"]	= $pessoa->getIndCliente();
	$array["indFor"]	= $pessoa->getIndFornecedor();
	$array["indTra"]	= $pessoa->getIndTransportadora();
	$array["email"]		= $pessoa->getEmail();
	
}

echo json_encode($array);
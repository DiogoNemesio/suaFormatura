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
if (isset($_GET['q']))			$q			= \Zage\App\Util::antiInjection($_GET["q"]);

if (strlen($q) == 18) {
	$cgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->retiraMascara($q);
}elseif (strlen($q) == 14) {
	$cgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->retiraMascara($q);
}else{
	echo json_encode(array());
	exit;
}
//$log->debug("CGC: ".$cgc);

$array		= array();

if ((int) $cgc != 0) {
	$cliente		= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $cgc));	
}else{
	$cliente		= null;
}

if ($cliente) {
	$array[0]	= $cliente->getCgc();
}

echo json_encode($array);
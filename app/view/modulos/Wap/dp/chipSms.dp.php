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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codChip']))			$codChip		= \Zage\App\Util::antiInjection($_POST['codChip']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codChip)) {
	$err	= $tr->trans("Falta de parâmetros !!");
}else{
	#################################################################################
	## Resgatar as informações do Chip
	#################################################################################
	$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
	if (!$oChip) {
		$err	= $tr->trans("Chip não encontrado !!");
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Solicitar o código SMS
#################################################################################
try {
	$chip		= new \Zage\Wap\Chip();
	$chip->_setCodigo($codChip);
	$return		= $chip->solicitaCodigoPorSms();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo().'|'.$tr->trans("Solicitação efetuada com sucesso"));

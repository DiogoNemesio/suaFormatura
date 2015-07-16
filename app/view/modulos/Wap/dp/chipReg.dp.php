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
if (isset($_POST['code']))				$code			= \Zage\App\Util::antiInjection($_POST['code']);

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

/** SMS Code **/
if (!isset($code) || empty($code)) {
	$err	= $tr->trans("Campo Código SMS é obrigatório !!");
}elseif ((!empty($code)) && (strlen($code) < 3)) {
	$err	= $tr->trans("Código SMS deve conter mais de 3 caracteres!");
}else{
	$code 	= str_replace("-", "", $code);
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Fazer o registro
#################################################################################
try {
	
	$chip 	= new \Zage\Wap\Chip();
	$chip->_setCodigo($codChip);
	$chip->setCode($code);
	
	$chip->registrar();

} catch (\Exception $e) {
	$log->err("Falha no registro do chip: $waUser -> ".$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Falha ao registrar o chip, entre em contato com os administradores do sistema através do email: contato@suaformatura.com"));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo().'|'.htmlentities($tr->trans("Registro efetuado com sucesso")));

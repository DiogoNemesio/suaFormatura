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

$codStatus	= $oChip->getCodStatus()->getCodigo(); 

if ($codStatus != "A" && $codStatus != "B")	{
	$err	= $tr->trans("Status não permite Bloqueio / Desbloqueio!!");
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}

#################################################################################
## Fazer o bloqueio / desbloqueio
#################################################################################
try {
	
	if ($codStatus == "A") {
		$oNovoStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "B");
		$dataBloqueio	= new \DateTime("now");
		$acao			= "Bloqueio";
	}else{
		$oNovoStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "A");
		$dataBloqueio	= null;
		$acao			= "Desbloqueio";
	}
	
	$oChip->setCodStatus($oNovoStatus);
	$oChip->setDataBloqueio($dataBloqueio);
	$em ->persist($oChip);
	$em->flush();
	$em->detach($oChip);
	
} catch (\Exception $e) {
	$log->err("Falha ao bloquear/desbloquear o chip: ".$oChip->getCodigo()." -> ".$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Falha ao bloquear/desbloquear o chip, entre em contato com os administradores do sistema através do email: ".$system->config["mail"]["admin"]));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo().'|'.htmlentities($tr->trans("$acao efetuado com sucesso")));

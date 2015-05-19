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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codDisp']))			$codDisp		= \Zage\App\Util::antiInjection($_POST['codDisp']);
if (isset($_POST['identificacao']))		$identificacao	= \Zage\App\Util::antiInjection($_POST['identificacao']);
if (isset($_POST['codTipo'])) 			$codTipo		= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['codStatus']))			$codStatus		= \Zage\App\Util::antiInjection($_POST['codStatus']);
if (isset($_POST['codLocal']))			$codLocal		= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['codEndereco']))	 	$codEndereco	= \Zage\App\Util::antiInjection($_POST['codEndereco']);


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if ((!empty($identificacao)) && (!is_numeric($identificacao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo identificação deve ser numérico"));
	$err	= 1;
}

$oDisp	= $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(),'identificacao' => $identificacao));

if (($oDisp != null) && ($oDisp->getCodigo() != $codDisp)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("IDENTIFICAÇÃO do dispositivo já existe"));
	$err 	= 1;
}



$oTipo	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo do dispositivo não encontrado"));
	$err 	= 1;
}

$oStatus	= $em->getRepository('Entidades\ZgdocDispositivoArmStatusTipo')->findOneBy(array('codigo' => $codStatus));
if (!$oStatus) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Status do dispositivo não encontrado"));
	$err 	= 1;
}

if (!empty($codEndereco)) {
	$oEnd	= $em->getRepository('Entidades\ZgdocEndereco')->findOneBy(array('codigo' => $codEndereco));
	if (!$oEnd) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Endereço do dispositivo não encontrado"));
		$err 	= 1;
	}
}

if (!empty($codLocal)) {
	$oLocal	= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codigo' => $codLocal));
	if (!$oLocal) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Local do dispositivo não encontrado"));
		$err 	= 1;
	}
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codDisp) && (!empty($codDisp))) {
 		$oDisp	= $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codigo' => $codDisp));
 		if (!$oDisp) {
 			$oDisp	= new \Entidades\ZgdocDispositivoArm();
 			$oDisp->setDataCadastro(new \DateTime("now"));
 			$oDisp->setIdentificacao(\Zage\Adm\Semaforo::proximoValor($system->getCodEmpresa(), 'DOC_DISP_ARM_IDENTIFICACAO'));
 		}else{
 			$oDisp->setIdentificacao($identificacao);
 		}
 	}else{
 		$oDisp	= new \Entidades\ZgdocDispositivoArm();
 		$oDisp->setDataCadastro(new \DateTime("now"));
 		$oDisp->setIdentificacao(\Zage\Adm\Semaforo::proximoValor($system->getCodEmpresa(), 'DOC_DISP_ARM_IDENTIFICACAO'));
 	}
 	
 	$oDisp->setCodEmpresa($emp);
 	$oDisp->setCodTipo($oTipo);
 	$oDisp->setCodStatus($oStatus);
 	
 	if ($codStatus == "E") {
 		$oDisp->setDataEliminacao(new \DateTime("now"));
 	}
 	
 	if (!empty($codLocal)) 		$oDisp->setCodLocalAtual($oLocal);
 	if (!empty($codEndereco)) 	$oDisp->setCodEnderecoAtual($oEnd);
 	
 	$em->persist($oDisp);
 	$em->flush();
 	$em->detach($oDisp);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO," Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oDisp->getCodigo());
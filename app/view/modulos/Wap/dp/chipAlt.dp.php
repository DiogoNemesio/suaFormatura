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
if (isset($_POST['codChip']))				$codChip		= \Zage\App\Util::antiInjection($_POST['codChip']);
if (isset($_POST['identificacao']))			$identificacao	= \Zage\App\Util::antiInjection($_POST['identificacao']);
if (isset($_POST['numero']))				$numero			= \Zage\App\Util::antiInjection($_POST['numero']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codChip)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de parâmetros !!"));
	$err	= 1;
}

/** Identificação **/
if (!isset($identificacao) || empty($identificacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Identificação é obrigatório !!"));
	$err	= 1;
}elseif ((!empty($ident)) && (strlen($ident) > 40)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A identificação não deve conter mais de 40 caracteres!"));
	$err	= 1;
}


/** Número **/
if (!isset($numero) || empty($numero)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Número é obrigatório !!"));
	$err	= 1;
}

/** Separar o ddd do número **/
$ddd		= substr($numero,0,2);
$celular	= substr($numero,2);


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	#################################################################################
	## Resgatar o status que será salvo
	#################################################################################
	$oStatus	= $em->getReference('\Entidades\ZgwapChipStatus', "R");
	
	#################################################################################
	## Resgatar a organização
	#################################################################################
	$oOrg	= $em->getReference('\Entidades\ZgadmOrganizacao', $system->getCodOrganizacao());
	
	
	if (isset($codChip) && (!empty($codChip))) {
 		$oChip	= $em->getRepository('\Entidades\ZgwapChip')->findOneBy(array('codigo' => $codChip));
 		if (!$oChip) {
 			$oChip	= new \Entidades\ZgwapChip();
 			$oChip->setDataCadastro(new \DateTime("now"));
 		}else{
 			$oStatus	= $oChip->getCodStatus();
 		}
 	}else{
 		$oChip	= new \Entidades\ZgwapChip();
 		$oChip->setDataCadastro(new \DateTime("now"));
 	}
 	
 	$oChip->setDdd($ddd);
 	$oChip->setIdentificacao($identificacao);
 	$oChip->setNumero($celular);
 	$oChip->setCodStatus($oStatus);
 	$oChip->setCodOrganizacao($oOrg);
 	
 	$em->persist($oChip);
 	$em->flush();
 	$em->detach($oChip);
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oChip->getCodigo());

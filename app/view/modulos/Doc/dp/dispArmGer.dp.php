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
if (isset($_POST['codTipo']))			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['gerIdent']))	 		$ident				= \Zage\App\Util::antiInjection($_POST['gerIdent']);

if (!isset($codTipo))  {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Falta de Parâmetros")));
	exit;
}


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Ident **/
if ((!isset($ident) || (empty($ident)))) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Identificação é obrigatório")));
	exit;
}

$oTipo		= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('codigo' => $codTipo,'codOrganizacao' => $system->getCodOrganizacao()));
if (!$oTipo) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Tipo do Dispositivo não existe")));
	exit;
}

$ativo		= 1;
$bloqueado	= 0;

/** Monta os arrays de cada parte **/
$aIdent	= split("-",$ident);

/** valida os campos **/

/** Identificação **/
if (sizeof($aIdent) > 2) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo Identificação inválido")));
	exit;
}else{
	if (sizeof($aIdent) == 1) {
		$identIni	= (int) $ident;
		$identFim	= (int) $ident;
	}else{
		$identIni	= (int) $aIdent[0]; 
		$identFim	= (int) $aIdent[1];
	}
	
	if (!is_integer($identIni) || (!is_integer($identFim))) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Campo identificação inválido")));
		exit;
	}
}

$numIdent	= 0; /** Número de dispositivos **/
$numCri		= 0; /** Número de dispositivos criados **/

$oTipo	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo do dispositivo não encontrado"));
	$err 	= 1;
}

$oStatus	= $em->getRepository('Entidades\ZgdocDispositivoArmStatusTipo')->findOneBy(array('codigo' => "A"));
if (!$oStatus) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Status do dispositivo não encontrado"));
	$err 	= 1;
}


/** Faz o loop para montagem do nome e inserção no banco **/
for ($i = $identIni; $i <= $identFim; $i++) {
	
	/** Validação  **/
	$oDisp	= $em->getRepository('Entidades\ZgdocDispositivoArm')->findOneBy(array('codEmpresa' => $system->getCodEmpresa(),'identificacao' => $i));
	
	if ($oDisp == null) {
	
		#################################################################################
		## Salvar no banco
		#################################################################################
		try {
			
			$oDisp	= new \Entidades\ZgdocDispositivoArm();
			$oDisp->setDataCadastro(new \DateTime("now"));
			$oDisp->setIdentificacao($i);
			$oDisp->setCodEmpresa($emp);
			$oDisp->setCodTipo($oTipo);
			$oDisp->setCodStatus($oStatus);
				
			$numCri++;
			
			$em->persist($oDisp);
		
		} catch (\Exception $e) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
			exit;
		}
	}
	
	$numIdent++;
}

$em->flush();
$em->clear(); // Detaches all objects from Doctrine!

/** Altera o valor do Semáforo **/
\Zage\Adm\Semaforo::setValor($system->getCodEmpresa(), 'DOC_DISP_ARM_IDENTIFICACAO', $identFim);

echo '0'.\Zage\App\Util::encodeUrl('|'.$numCri.'|'.htmlentities($tr->trans("Foram gerados %s dispositivos com sucesso",array('%s' => $numCri))));
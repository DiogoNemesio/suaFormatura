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
if (isset($_POST['codAgencia']))		$codAgencia			= \Zage\App\Util::antiInjection($_POST['codAgencia']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['agencia']))			$agencia			= \Zage\App\Util::antiInjection($_POST['agencia']);
if (isset($_POST['agenciaDV']))			$agenciaDV			= \Zage\App\Util::antiInjection($_POST['agenciaDV']);
if (isset($_POST['codBanco']))	 		$codBanco			= \Zage\App\Util::antiInjection($_POST['codBanco']);
if (isset($_POST['banco']))	 			$banco				= \Zage\App\Util::antiInjection($_POST['banco']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe um nome para identificar esta agência.");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O nome para identificar esta agência não deve conter mais de 60 caracteres.");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodorganizacao(), 'nome' => $nome ));

if (($oNome != null) && ($oNome->getCodigo() != $codAgencia)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe uma identificação cadastrada igual a informada. Por favor, informe outra para facilitar a utilização no sistema!"));
	$err 	= 1;
}

/** Agência **/
if (!isset($agencia) || (empty($agencia))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe o número da agência.");
	$err	= 1;
}

if ((!empty($agencia)) && (strlen($agencia) > 8)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O número da agência não deve conter mais de 8 caracteres.");
	$err	= 1;
}

$oAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodorganizacao(), 'agencia' => $agencia ,'codBanco' =>$codBanco ));

if (($oAgencia != null) && ($oAgencia->getCodigo() != $codAgencia)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Esta agência já está cadastrada."));
	$err 	= 1;
}

/** Banco **/
if (!isset($codBanco) || (empty($codBanco))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe o banco da agência.");
	$err	= 1;
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

/** Banco **/
$infoBanco		= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco));

if ($infoBanco == false) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Banco inválido, selecione um banco válido"));
	$err	= 1;
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

/** Dígito Verificador da Agência **/
if (in_array($infoBanco->getCodBanco(), array('001', '041', '237'), true)) {
	if(!isset($agenciaDV) || empty($agenciaDV)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe o dígito verificador da agência.");
		$err	= 1;
	}
}else{
	$agenciaDV = null;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codAgencia) && (!empty($codAgencia))) {
 		$oAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codAgencia));
 		if (!$oAgencia) $oAgencia	= new \Entidades\ZgfinAgencia();
 	}else{
 		$oAgencia	= new \Entidades\ZgfinAgencia();
 	}
 	
 	
 	#################################################################################
 	## Resgatar o objeto da Organização
 	#################################################################################
 	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	
 	$oAgencia->setCodOrganizacao($oOrg);
 	$oAgencia->setCodBanco($infoBanco);
 	$oAgencia->setNome($nome);
 	$oAgencia->setAgencia($agencia);
 	$oAgencia->setAgenciaDV($agenciaDV);
 	
 	$em->persist($oAgencia);
 	$em->flush();
 	$em->detach($oAgencia);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oAgencia->getCodigo());
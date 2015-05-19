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
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** Agência **/
if (!isset($agencia) || (empty($agencia))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo AGÊNCIA é obrigatório");
	$err	= 1;
}

if ((!empty($agencia)) && (strlen($agencia) > 8)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo AGÊNCIA não deve conter mais de 8 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodorganizacao(), 'nome' => $nome ));

if (($oNome != null) && ($oNome->getCodigo() != $codAgencia)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("NOME da Agência já existe"));
	$err 	= 1;
}

$oCodAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodorganizacao(), 'agencia' => $agencia ));

if (($oCodAgencia != null) && ($oCodAgencia->getCodigo() != $codAgencia)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Código da Agência já existe"));
	$err 	= 1;
}

/** Banco **/
if (!isset($codBanco) || (empty($codBanco))) {
	
	if (isset($banco) && !empty($banco)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Banco inválido, selecione um banco válido");
	}else{
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo BANCO é obrigatório");
	}
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

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codAgencia) && (!empty($codAgencia))) {
 		$oAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codEmpresa' => $system->getCodMatriz(), 'codigo' => $codAgencia));
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
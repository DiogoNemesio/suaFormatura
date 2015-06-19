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
if (isset($_POST['codLayout']))			$codLayout			= \Zage\App\Util::antiInjection($_POST['codLayout']);
if (isset($_POST['codBanco']))			$codBanco			= \Zage\App\Util::antiInjection($_POST['codBanco']);
if (isset($_POST['codTipo']))			$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('nome' => $nome));

if (($oNome != null) && ($oNome->getCodigo() != $codLayout)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome já existe"));
	$err 	= 1;
}


/** Banco **/
if (!isset($codBanco) || (empty($codBanco))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Banco é obrigatório");
	$err	= 1;
}

$oBanco	= $em->getRepository('Entidades\ZgfinBanco')->findOneBy(array('codigo' => $codBanco));

if (!$oBanco) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Banco não encontrado !!!");
	$err	= 1;
}

/** Tipo **/
if (!isset($codTipo) || (empty($codTipo))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Tipo é obrigatório");
	$err	= 1;
}

$oTipo	= $em->getRepository('Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $codTipo));

if (!$oTipo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Tipo de Layout não encontrado !!!");
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	if (isset($codLayout) && (!empty($codLayout))) {
 		$oLayout	= $em->getRepository('Entidades\ZgfinArquivoLayout')->findOneBy(array('codigo' => $codLayout));
 		if (!$oLayout) $oLayout	= new \Entidades\ZgfinArquivoLayout();
 	}else{
 		$oLayout	= new \Entidades\ZgfinArquivoLayout();
 	}
 	
 	$oLayout->setCodBanco($oBanco);
 	$oLayout->setCodTipoLayout($oTipo);
 	$oLayout->setNome($nome);
 	
 	$em->persist($oLayout);
 	$em->flush();
 	$em->detach($oLayout);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oLayout->getCodigo());
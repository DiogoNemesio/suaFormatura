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
if (isset($_POST['codSecao']))		$codSecao			= \Zage\App\Util::antiInjection($_POST['codSecao']);
if (isset($_POST['nome'])) 			$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['icone']))			$icone				= \Zage\App\Util::antiInjection($_POST['icone']);
if (isset($_POST['codModulo']))	 	$codModulo			= \Zage\App\Util::antiInjection($_POST['codModulo']);
if (isset($_POST['ordem']))	 		$ordem				= \Zage\App\Util::antiInjection($_POST['ordem']);

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

if ((!empty($nome)) && (strlen($nome) > 40)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 40 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgappParametroSecao')->findOneBy(array('codModulo' => $codModulo, 'nome' => $nome ));
if (($oNome != null) && ($oNome->getCodigo() != $codSecao)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("NOME da seção já existe"));
	$err 	= 1;
}

/** Módulo **/
if (!isset($codModulo) || (empty($codModulo))) {
	
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo Módulo é obrigatório");
	$err	= 1;
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

/** Módulo **/
$oModulo		= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $codModulo));

if ($oModulo == false) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Módulo inválido, selecione um módulo válido"));
	$err	= 1;
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar
#################################################################################
try {
	
	if (isset($codSecao) && (!empty($codSecao))) {
 		$oSecao	= $em->getRepository('Entidades\ZgappParametroSecao')->findOneBy(array('codigo' => $codSecao));
 		if (!$oSecao) $oSecao	= new \Entidades\ZgappParametroSecao();
 	}else{
 		$oSecao	= new \Entidades\ZgappParametroSecao();
 	}
 	
 	$oSecao->setCodModulo($oModulo);
 	$oSecao->setIcone($icone);
 	$oSecao->setNome($nome);
 	$oSecao->setOrdem($ordem);
 	
 	$em->persist($oSecao);
 	$em->flush();
 	$em->detach($oSecao);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSecao->getCodigo());
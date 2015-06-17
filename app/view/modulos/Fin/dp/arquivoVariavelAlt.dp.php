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
if (isset($_POST['codVariavel']))		$codVariavel		= \Zage\App\Util::antiInjection($_POST['codVariavel']);
if (isset($_POST['variavel']))			$variavel			= \Zage\App\Util::antiInjection($_POST['variavel']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Descrição **/
if (!isset($descricao) || (empty($descricao))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO é obrigatório");
	$err	= 1;
}

if ((!empty($descricao)) && (strlen($descricao) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo NOME não deve conter mais de 100 caracteres");
	$err	= 1;
}

/** Variável **/
if (!isset($variavel) || (empty($variavel))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo VARIÁVEL é obrigatório");
	$err	= 1;
}

if ((!empty($variavel)) && (strlen($variavel) > 20)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo VARIÁVEL não deve conter mais de 20 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findOneBy(array('variavel' => $variavel ));

if (($oNome != null) && ($oNome->getCodigo() != $codVariavel)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Variável já existe"));
	$err 	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codVariavel) && (!empty($codVariavel))) {
 		$oVariavel	= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findOneBy(array('codigo' => $codVariavel));
 		if (!$oVariavel) $oVariavel	= new \Entidades\ZgfinArquivoVariavel();
 	}else{
 		$oVariavel	= new \Entidades\ZgfinArquivoVariavel();
 	}
 	
 	$oVariavel->setVariavel($variavel);
 	$oVariavel->setDescricao($descricao);
 	
 	$em->persist($oVariavel);
 	$em->flush();
 	$em->detach($oVariavel);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oVariavel->getCodigo());
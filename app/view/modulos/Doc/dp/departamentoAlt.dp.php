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
if (isset($_POST['codDepartamento']))	$codDepartamento	= \Zage\App\Util::antiInjection($_POST['codDepartamento']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME é obrigatório");
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo NOME não deve conter mais de 60 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgdocDepartamento')->findOneBy(array('nome' => $nome, 'codEmpresa' => $system->getCodEmpresa()));

if (($oNome != null) && ($oNome->getCodigo() != $codDepartamento)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("NOME do departamento já existe"));
	$err 	= 1;
}

/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 100)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRIÇÃO não deve conter mais de 100 caracteres"));
	$err	= 1;
}
 
/** Ativo **/

if (!empty($codDepartamento) && empty($ativo)){
	
	$oLocal	= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codDepartamento' => $codDepartamento, 'indAtivo' => 1));
	
	if($oLocal != null){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Ainda existe LOCAL ativo neste departamento"));
		$err	= 1;
	}
}


if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codDepartamento) && (!empty($codDepartamento))) {
 		$oDepartamento	= $em->getRepository('Entidades\ZgdocDepartamento')->findOneBy(array('codigo' => $codDepartamento));
 		if (!$oDepartamento) $oDepartamento	= new \Entidades\ZgdocDepartamento();
 	}else{
 		$oDepartamento	= new \Entidades\ZgdocDepartamento();
 	}
 	
 	$oEmpresa		= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codigo' => $system->getCodEmpresa()));
 	
 	$oDepartamento->setCodEmpresa($oEmpresa);
 	$oDepartamento->setNome($nome);
 	$oDepartamento->setDescricao($descricao);
 	$oDepartamento->setIndAtivo($ativo);
 	
 	$em->persist($oDepartamento);
 	$em->flush();
 	$em->detach($oDepartamento);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oDepartamento->getCodigo());
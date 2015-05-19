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
if (isset($_POST['codLocal']))			$codLocal			= \Zage\App\Util::antiInjection($_POST['codLocal']);
if (isset($_POST['nome'])) 				$nome				= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['departamento']))		$departamento		= \Zage\App\Util::antiInjection($_POST['departamento']);

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

$oNome	= \Zage\Doc\Local::buscaLocal($nome, $departamento);

if ($oNome != null && ($oNome[0]->getCodigo() != $codLocal)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," NOME do local de arquivo já existe");
	$err	= 1;
}

 
/** Descrição**/
if ((!empty($descricao)) && (strlen($descricao) > 100)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO," Campo DESCRIÇÃO não deve conter mais de 100 caracteres");
	$err	= 1;
}
 
/** Ativo **/
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
	
	if (isset($codLocal) && (!empty($codLocal))) {
 		$oLocal	= $em->getRepository('Entidades\ZgdocLocal')->findOneBy(array('codigo' => $codLocal));
 		if (!$oLocal) $oLocal	= new \Entidades\ZgdocDepartamento();
 	}else{
 		$oLocal	= new \Entidades\ZgdocLocal();
 	}
 	
 	$oDepartamento		= $em->getRepository('Entidades\ZgdocDepartamento')->findOneBy(array('codigo' => $departamento));
 	
 	$oLocal->setCodDepartamento($oDepartamento);
 	$oLocal->setNome($nome);
 	$oLocal->setDescricao($descricao);
 	$oLocal->setIndAtivo($ativo);
 	
 	$em->persist($oLocal);
 	$em->flush();
 	$em->detach($oLocal);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oLocal->getCodigo());
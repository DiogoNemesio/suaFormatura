<?php
use Zend\Filter\File\UpperCase;
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
if (isset($_POST['codTipo']))			$codTipo		= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['sigla']))				$sigla			= strtoupper(\Zage\App\Util::antiInjection($_POST['sigla']));
if (isset($_POST['qtdeMax']))			$qtdeMax		= \Zage\App\Util::antiInjection($_POST['qtdeMax']);
if (isset($_POST['ativo']))	 			$ativo			= \Zage\App\Util::antiInjection($_POST['ativo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" Campo NOME é obrigatório"));
	$err	= 1;
}

if ((!empty($nome)) && (strlen($nome) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" Campo NOME não deve conter mais de 60 caracteres"));
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('nome' => $nome));

if (($oNome != null) && ($oNome->getCodigo() != $codTipo)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" NOME do dispositivo já existe"));
	$err 	= 1;
}

/** Sigla **/
if ((!empty($descricao)) && (strlen($descricao) > 10)) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" Campo SIGLA não deve conter mais de 10 caracteres"));
	$err	= 1;
}

$oSigla	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('sigla' => $sigla));

if (($oSigla != null) && ($oSigla->getCodigo() != $codTipo)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans(" SIGLA do dispositivo já existe"));
	$err 	= 1;
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
 
$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

if (!$oOrg) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Organização não encontrada')));
	exit;
}


#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codTipo) && (!empty($codTipo))) {
 		$oDispTipo	= $em->getRepository('Entidades\ZgdocDispositivoArmTipo')->findOneBy(array('codigo' => $codTipo));
 		if (!$oDispTipo) $oDispTipo	= new \Entidades\ZgdocDispositivoArmTipo();
 	}else{
 		$oDispTipo	= new \Entidades\ZgdocDispositivoArmTipo();
 	}
 	
 	$oDispTipo->setCodOrganizacao($oOrg);
 	$oDispTipo->setNome($nome);
 	$oDispTipo->setSigla($sigla);
 	$oDispTipo->setQtdeMax($qtdeMax);
 	$oDispTipo->setIndAtivo($ativo);
 	
 	$em->persist($oDispTipo);
 	$em->flush();
 	$em->detach($oDispTipo);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO," Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oDispTipo->getCodigo());
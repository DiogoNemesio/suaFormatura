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
if (isset($_POST['codCentro']))			$codCentro			= \Zage\App\Util::antiInjection($_POST['codCentro']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['ativo']))	 			$ativo				= \Zage\App\Util::antiInjection($_POST['ativo']);
if (isset($_POST['debito']))	 		$debito				= \Zage\App\Util::antiInjection($_POST['debito']);
if (isset($_POST['credito']))	 		$credito			= \Zage\App\Util::antiInjection($_POST['credito']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** DESCRIÇÃO **/
if (!isset($descricao) || (empty($descricao))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Informe o nome do centro de custo.");
	$err	= 1;
}

if ((!empty($descricao)) && (strlen($descricao) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"O nome do centro de custo não deve conter mais de 60 caracteres.");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'descricao' => $descricao ));

if (($oNome != null) && ($oNome->getCodigo() != $codCentro)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um centro de custo com este nome."));
	$err 	= 1;
}

if (isset($ativo) && (!empty($ativo))) {
	$ativo	= 1;
}else{
	$ativo	= 0;
}

if (isset($debito) && (!empty($debito))) {
	$debito	= 1;
}else{
	$debito	= 0;
}

if (isset($credito) && (!empty($credito))) {
	$credito	= 1;
}else{
	$credito	= 0;
}

if ($credito == 0 && $debito == 0) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O centro de custo deve ser pelo menos de débito ou crédito"));
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
	
	if (isset($codCentro) && (!empty($codCentro))) {
 		$oConta	= $em->getRepository('Entidades\ZgfinCentroCusto')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codCentro));
 		if (!$oConta) $oConta	= new \Entidades\ZgfinCentroCusto();
 	}else{
 		$oConta	= new \Entidades\ZgfinCentroCusto();
 	}
 	
 	$oMat		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
 	$oCCTipo	= $em->getRepository('Entidades\ZgfinCentroCustoTipo')->findOneBy(array('codigo' => P));
 	
 	$oConta->setCodOrganizacao($oMat);
 	$oConta->setDescricao($descricao);
 	$oConta->setIndDebito($debito);
 	$oConta->setIndCredito($credito);
 	$oConta->setIndAtivo($ativo);
 	$oConta->setCodTipoCentroCusto($oCCTipo);
 	
 	$em->persist($oConta);
 	$em->flush();
 	$em->detach($oConta);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oConta->getCodigo());
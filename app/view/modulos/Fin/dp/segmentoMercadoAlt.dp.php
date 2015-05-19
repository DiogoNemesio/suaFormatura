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
if (isset($_POST['codSegmento']))		$codSegmento		= \Zage\App\Util::antiInjection($_POST['codSegmento']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** DESCRIÇÃO **/
if (!isset($descricao) || (empty($descricao))) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO é obrigatório");
	$err	= 1;
}

if ((!empty($descricao)) && (strlen($descricao) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRIÇÃO não deve conter mais de 60 caracteres");
	$err	= 1;
}

$oNome	= $em->getRepository('Entidades\ZgfinSegmentoMercado')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'descricao' => $descricao ));

if (($oNome != null) && ($oNome->getCodigo() != $codSegmento)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Descrição do Segmento de Mercado já existe"));
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
	
	if (isset($codSegmento) && (!empty($codSegmento))) {
 		$oSeg	= $em->getRepository('Entidades\ZgfinSegmentoMercado')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codSegmento));
 		if (!$oSeg) $oSeg	= new \Entidades\ZgfinSegmentoMercado();
 	}else{
 		$oSeg	= new \Entidades\ZgfinSegmentoMercado();
 	}
 	
 	#################################################################################
 	## Resgatar o objeto da Organização
 	#################################################################################
 	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

 	$oSeg->setCodOrganizacao($oOrg);
 	$oSeg->setDescricao($descricao);
 	
 	$em->persist($oSeg);
 	$em->flush();
 	$em->detach($oSeg);
 	 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oSeg->getCodigo());
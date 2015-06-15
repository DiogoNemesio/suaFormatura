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
if (isset($_POST['_zgParametro'])) 		$_zgParametro	= \Zage\App\Util::antiInjection($_POST['_zgParametro']);

#################################################################################
## Fazer o loop nos parâmetros para salva-los
#################################################################################
foreach ($_zgParametro as $codParametro => $valor) {
	
	$oParametro		= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codParametro));
	if (!$oParametro) die ("Parâmetro não encontrado : ".$codParametro);
	
	#################################################################################
	## Resgatar o valor já salvo
	#################################################################################
	$oValor		= $em->getRepository('Entidades\ZgadmParametroUsuario')->findOneBy(array('codUsuario' => $system->getCodUsuario(), 'codParametro' => $codParametro));
	
	if (!$oValor)	$oValor = new \Entidades\ZgadmParametroUsuario();
	
	#################################################################################
	## Busca o objeto da organização
	#################################################################################
	$oUser		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
	
	$oValor->setCodUsuario($oUser);
	$oValor->setCodParametro($oParametro);
	$oValor->setValor($valor);
	
	$em->persist($oValor);
	
}

try {
	$em->flush();
	$em->clear();
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível salvar: ".$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}
	
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Parâmetro salvos com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('||');
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
if (isset($_POST['codEnquete'])) 		$codEnquete		= \Zage\App\Util::antiInjection($_POST['codEnquete']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e finalizar
#################################################################################
try {

	if (!isset($codEnquete) || (!$codEnquete)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oEnquete 	 = $em->getRepository('Entidades\ZgappEnquetePergunta')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codEnquete));
	
	if (!$oEnquete) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Enquete não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oStatus		= $em->getRepository('Entidades\ZgappEnqueteStatus')->findOneBy(array('codigo' => 'C'));
	
	$oEnquete->setCodStatus($oStatus);
 	$oEnquete->setDataPrazo(new \DateTime("now"));
		
	$em->persist($oEnquete);
	$em->flush();
	$em->detach($oEnquete);

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Enquete finalizada com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEnquete->getCodigo().'|');
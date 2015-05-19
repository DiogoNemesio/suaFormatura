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
if (isset($_POST['codAgencia'])) 		$codAgencia		= \Zage\App\Util::antiInjection($_POST['codAgencia']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codAgencia) || (!$codAgencia)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oAgencia	= $em->getRepository('Entidades\ZgfinAgencia')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(), 'codigo' => $codAgencia));

	if (!$oAgencia) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Agência não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oAgencia);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Agência excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oAgencia->getCodigo().'|');
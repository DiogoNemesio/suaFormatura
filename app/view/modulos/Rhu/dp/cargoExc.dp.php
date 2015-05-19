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
if (isset($_POST['codCargo'])) 		$codCargo		= \Zage\App\Util::antiInjection($_POST['codCargo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se existe e excluir
#################################################################################
try {

	if (!isset($codCargo) || (!$codCargo)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oFuncoes		= $em->getRepository('Entidades\ZgrhuFuncionarioFuncao')->findBy(array('codCargo' => $codCargo));
		
	if (!$oFuncoes) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Fucao não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	for ($i = 0; $i < sizeof($oFuncoes); $i++) {
		$em->remove($oFuncoes[$i]);
	}
	
	$oCargo = 	$em->getRepository ( 'Entidades\ZgrhuFuncionarioCargo' )->findOneBy(array ('codigo' => $codCargo));
	
	if (!$oCargo) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Cargo não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oCargo);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Cargo excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oCargo->getCodigo().'|');
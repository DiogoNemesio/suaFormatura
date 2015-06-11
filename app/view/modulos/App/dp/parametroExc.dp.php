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
if (isset($_POST['codParametro'])) 		$codParametro		= \Zage\App\Util::antiInjection($_POST['codParametro']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se o parâmetro existe e excluir
#################################################################################
try {

	if (!isset($codParametro) || (!$codParametro)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codParametro));
	

	if (!$oParametro) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	#################################################################################
	## Excluir os valores do tipo de parâmetro lista
	#################################################################################
	$oValores	= $em->getRepository('Entidades\ZgappParametroTipoValor')->findBy(array('codParametro' => $codParametro));
	for ($i = 0; $i < sizeof($oValores); $i++) {
		$em->remove($oValores[$i]);
	}
	
	$em->remove($oParametro);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Parâmetro excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oParametro->getCodigo().'|');
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
if (isset($_POST['codResposta'])) 		$codResposta	= \Zage\App\Util::antiInjection($_POST['codResposta']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codResposta) || (!$codResposta)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oResposta 	 = $em->getRepository('Entidades\ZgappEnqueteResposta')->findOneBy(array('codigo' => $codResposta));
	
	if (!$oResposta) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Resposta não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oResposta);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Resposta excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oResposta->getCodigo().'|');
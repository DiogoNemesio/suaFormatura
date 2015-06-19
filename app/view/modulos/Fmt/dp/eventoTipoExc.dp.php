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
if (isset($_POST['codEventoTipo'])) 		$codEventoTipo	= \Zage\App\Util::antiInjection($_POST['codEventoTipo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codEventoTipo) || (!$codEventoTipo)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oEventoTipo 	 = $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codEventoTipo));
	
	if (!$oEventoTipo) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Tipo Evento não encontrado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oEventoTipo);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Tipo Evento excluído com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEventoTipo->getCodigo().'|');
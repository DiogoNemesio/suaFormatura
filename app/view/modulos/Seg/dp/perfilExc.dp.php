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
if (isset($_POST['codPerfil'])) 		$codPerfil		= \Zage\App\Util::antiInjection($_POST['codPerfil']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codPerfil) || (!$codPerfil)) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Parâmetro %s não informado',array('%s' => "codPerfil")))));
	}
	
	$oPerfil	= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));

	if (!$oPerfil) {
		die('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Perfil não encontrado'))));
	}
	
	$em->remove($oPerfil);
	$em->flush();

} catch (\Exception $e) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('|'.$oPerfil->getCodigo().'|'.htmlentities($tr->trans("Perfil excluído com sucesso")));

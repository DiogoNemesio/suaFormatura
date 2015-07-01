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
if (isset($_POST['codConvidado'])) 		$codConvidado	= \Zage\App\Util::antiInjection($_POST['codConvidado']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codConvidado) || (!$codConvidado)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado"))));
		$err = 1;
	}
	
	$oConvidado 	 = $em->getRepository('Entidades\ZgfmtListaConvidado')->findOneBy(array('codigo' => $codConvidado));
	
	if (!$oConvidado) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Convidado não encontrado"))));
		$err = 1;
	}
	
	$em->remove($oConvidado);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'."Convidado exclu&Iacute;do com sucesso!");
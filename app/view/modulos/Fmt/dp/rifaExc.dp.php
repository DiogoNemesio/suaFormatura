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
if (isset($_POST['codRifa'])) 		$codRifa	= \Zage\App\Util::antiInjection($_POST['codRifa']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificaçãoes e excluir
#################################################################################
try {

	if (!isset($codRifa) || (!$codRifa)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_RIFA"))));
		$err = 1;
	}
	
	$oRifa 	 	= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
	$oRifaNum 	= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $codRifa));
	
	if (!$oRifa) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Rifa não encontrada"))));
		$err = 1;
	}elseif ($oRifaNum){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta rifa não pode ser excluída pois já teve seus bilhetes gerados."))));
		$err = 1;
	}
	
	$em->remove($oRifa);
	$em->flush();

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Rifa excluída com sucesso!")));
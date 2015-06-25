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
if (isset($_POST['codEvento'])) 		$codEvento		= \Zage\App\Util::antiInjection($_POST['codEvento']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pasta existe e excluir
#################################################################################
try {

	if (!isset($codEvento) || (!$codEvento)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado"))));
		$err = 1;
	}
	
	$oEvento 	 = $em->getRepository('Entidades\ZgfmtEvento')->findOneBy(array('codigo' => $codEvento));
	
	if (!$oEvento) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Evento não encontrado"))));
		$err = 1;
	}
	
	$em->remove($oEvento);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'."Evento exclu&Iacute;do com sucesso!");
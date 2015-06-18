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
if (isset($_POST['codVariavel'])) 		$codVariavel		= \Zage\App\Util::antiInjection($_POST['codVariavel']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a variável existe e excluir
#################################################################################
try {

	if (!isset($codVariavel) || (!$codVariavel)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oVariavel	= $em->getRepository('Entidades\ZgfinArquivoVariavel')->findOneBy(array('codigo' => $codVariavel));

	if (!$oVariavel) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Variável não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oVariavel);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Variável excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oVariavel->getCodigo().'|');
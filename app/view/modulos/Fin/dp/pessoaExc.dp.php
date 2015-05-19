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
if (isset($_POST['codPessoa'])) 		$codPessoa		= \Zage\App\Util::antiInjection($_POST['codPessoa']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificar se a pessoa existe e excluir
#################################################################################
try {

	if (!isset($codPessoa) || (!$codPessoa)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$erro	= \Zage\Fin\Pessoa::exclui($codPessoa);
	
	if ($erro != null) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$erro);
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Pessoa excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$codPessoa.'|');
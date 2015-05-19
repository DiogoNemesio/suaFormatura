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
## Verificar se existe e excluir
#################################################################################
try {

	if (!isset($codPessoa) || (!$codPessoa)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Parâmetro não informado'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$oPessoa = 	$em->getRepository ( 'Entidades\ZgrhuPessoa' )->findOneBy (array ('codigo' => $codPessoa));
	
	if (!$oPessoa) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Pessoa não encontrada'));
		die('1'.\Zage\App\Util::encodeUrl('||'));
	}
	
	$em->remove($oPessoa);
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Pessoa excluída com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oPessoa->getCodigo().'|');
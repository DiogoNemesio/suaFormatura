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
if (isset($_POST['codDicionario']))		$codDicionario	= \Zage\App\Util::antiInjection($_POST['codDicionario']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['descricao']))			$descricao		= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['audit'])) 			$audit			= \Zage\App\Util::antiInjection($_POST['audit']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
if (!isset($codDicionario) || !$codDicionario) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Falta de parâmetros'));
	echo '1'.\Zage\App\Util::encodeUrl('||');
	exit;
}

/** Audit **/
if (isset($audit) && (!empty($audit))) {
	$audit	= 1;
}else{
	$audit	= 0;
}


#################################################################################
## Salvar no banco
#################################################################################
try {

	$oDict	= $em->getRepository('Entidades\ZgsegDicionario')->findOneBy(array('codigo' => $codDicionario));
	
	if (!$oDict) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans('Dicionário não encontrado: %s',array('%s' => $codDicionario)));
		echo '1'.\Zage\App\Util::encodeUrl('||');
		exit;
	}

	//$oDict->setNome($nome);
	$oDict->setDescricao($descricao);
	$oDict->setIndAudit($audit);
	
	
	$em->persist($oDict);
	$em->flush();
	$em->detach($oDict);

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oDict->getCodigo());

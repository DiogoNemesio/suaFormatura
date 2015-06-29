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
## Variáveis globais
#################################################################################
global $em,$system,$tr;

#################################################################################
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codAtividade']))			$codAtividade		= \Zage\App\Util::antiInjection($_POST['codAtividade']);
if (isset($_POST['codTipo']))				$codTipo			= \Zage\App\Util::antiInjection($_POST['codTipo']);
if (isset($_POST['identificacao']))			$identificacao		= \Zage\App\Util::antiInjection($_POST['identificacao']);
if (isset($_POST['descricao']))				$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Código **/
if (!isset($codAtividade)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Falta de parâmetros !!"));
	$err	= 1;
}

/** Tipo de Atividade **/
if (!isset($codTipo) || empty($codTipo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Tipo é obrigatório !!"));
	$err	= 1;
}

$oTipo	= $em->getRepository('Entidades\ZgutlAtividadeTipo')->findOneBy(array('codigo' => $codTipo));
if (!$oTipo) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Tipo de atividade não encontrado !!"));
	$err	= 1;
}

/** Identificação **/
if (!isset($identificacao) || empty($identificacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Identificação é obrigatório !!"));
	$err	= 1;
}elseif ((!empty($ident)) && (strlen($ident) > 40)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A identificação não deve conter mais de 40 caracteres!"));
	$err	= 1;
}else{
	$oAtividade	= $em->getRepository('Entidades\ZgutlAtividade')->findOneBy(array('identificacao' => $identificacao));

	if($oAtividade != null && ($oAtividade->getCodigo() != $codAtividade)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe uma atividade cadastrada com esta identificação! Por favor, informe outra."));
		$err	= 1;
	}
}


/** Descrição **/
if (!isset($descricao) || empty($descricao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Descrição é obrigatório !!"));
	$err	= 1;
}


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
	if (isset($codAtividade) && (!empty($codAtividade))) {
 		$oAtividade	= $em->getRepository('\Entidades\ZgutlAtividade')->findOneBy(array('codigo' => $codAtividade));
 		if (!$oAtividade) $oAtividade	= new \Entidades\ZgutlAtividade();
 	}else{
 		$oAtividade	= new \Entidades\ZgutlAtividade();
 	}
 	
 	$oAtividade->setCodTipoAtividade($oTipo);
 	$oAtividade->setDescricao($descricao);
 	$oAtividade->setIdentificacao($identificacao);
 	$em->persist($oAtividade);
 	$em->flush();
 	$em->detach($oAtividade);
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oAtividade->getCodigo());

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
## Resgata os parâmetros passados pelo formulário
#################################################################################
if (isset($_POST['codEventoTipo']))				$codEventoTipo			= \Zage\App\Util::antiInjection($_POST['codEventoTipo']);
if (isset($_POST['descricao']))					$descricao				= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['indAtivo']))					$indAtivo				= \Zage\App\Util::antiInjection($_POST['indAtivo']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Cargo **/
if ((empty($descricao))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo DESCRICAO é obrigatório"));
	$err	= 1;
}

if ((!empty($descricao)) && (strlen($descricao) > 60)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Campo DESCRICAO não deve conter mais de 60 caracteres");
	$err	= 1;
}

/** IndAtivo **/
if (isset($indAtivo) && (!empty($indAtivo))) {
	$indAtivo	= 1;
}else{
	$indAtivo	= 0;
}

$oDescricao	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('descricao' => $descricao));

if($oDescricao != null && ($oDescricao->getCodigo() != $codEventoTipo)){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Esta DESCRICAO já foi cadastrada!"));
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
	
	if (isset($codEventoTipo) && (!empty($codEventoTipo))) {
 		$oEventoTipo	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findOneBy(array('codigo' => $codEventoTipo));
 		if (!$oEventoTipo) $oEventoTipo	= new \Entidades\ZgfmtEventoTipo();
 	}else{
 		$oEventoTipo	= new \Entidades\ZgfmtEventoTipo();
 	}
 	
 	$oEventoTipo->setDescricao($descricao); 
 	$oEventoTipo->setIndAtivo($indAtivo);
 	
 	$em->persist($oEventoTipo);
 	$em->flush();
 	$em->detach($oEventoTipo);
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oEventoTipo->getCodigo());

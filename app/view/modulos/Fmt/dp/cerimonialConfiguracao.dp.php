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
global $em,$system,$tr,$log;

#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['indAceitarOrc']))				$indAceitarOrc			= \Zage\App\Util::antiInjection($_POST['indAceitarOrc']);
if (isset($_POST['indRetirarItemPadrao']))		$indRetirarItemPadrao	= \Zage\App\Util::antiInjection($_POST['indRetirarItemPadrao']);
if (isset($_POST['indDardesconto'])) 			$indDardesconto			= \Zage\App\Util::antiInjection($_POST['indDardesconto']);
if (isset($_POST['indAlterarObs'])) 			$indAlterarObs			= \Zage\App\Util::antiInjection($_POST['indAlterarObs']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* CONFIGURAÇÃO *********/
$oOrgCer	= $em->getRepository('Entidades\ZgfmtOrganizacaoCerimonial')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));

if (!$oOrgCer)	{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Ops! Sua organização ainda não está configurada para criar formaturas. Entre em contato com o nosso suporte."));
	$err	= 1;
}

/******* ACEITE *********/
if (isset($indAceitarOrc) && (!empty($indAceitarOrc))) {
	$indAceitarOrc	= 1;
}else{
	$indAceitarOrc	= 0;
}

/******* ITEM PADRÃO *********/
if (isset($indRetirarItemPadrao) && (!empty($indRetirarItemPadrao))) {
	$indRetirarItemPadrao	= 1;
}else{
	$indRetirarItemPadrao	= 0;
}

/******* OBS *********/
if (isset($indAlterarObs) && (!empty($indAlterarObs))) {
	$indAlterarObs	= 1;
}else{
	$indAlterarObs	= 0;
}

/******* DAR DESCONTO *********/
if (isset($indDardesconto) && (!empty($indDardesconto))) {
	$indDardesconto	= 1;
}else{
	$indDardesconto	= 0;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
 	exit;
}
 
#################################################################################
## Salvar no banco
#################################################################################
try {
	
 	#################################################################################
 	## ORGANIZAÇÃO - CERIMONIAL
 	################################################################################# 	
 	$oOrgCer->setIndVendedorAceite($indAceitarOrc);
 	$oOrgCer->setIndVendedorDesmarcarPadrao($indRetirarItemPadrao);
 	$oOrgCer->setIndVendedorDarCortesia($indDardesconto);
 	$oOrgCer->setIndVendedorAlterarObs($indAlterarObs); 	
 	
 	$em->persist($oOrgCer);

	#################################################################################
 	## Salvar as informações
 	#################################################################################
 	try {
 		$em->flush();
 		$em->clear();
 	} catch (Exception $e) {
 		$log->debug("Erro ao salvar o Organização:". $e->getTraceAsString());
 		throw new \Exception("Ops! Encontramos um problema para realizar a operação. Tente novamente em instantes e caso o problema continue entre em contato com o nosso suporte.");
 	}
 	
 	
} catch (\Exception $e) {
 	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,"Informações salvas com sucesso");
echo '0'.\Zage\App\Util::encodeUrl('|'.$oOrganizacao->getCodigo());
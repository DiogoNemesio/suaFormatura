<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
 	include_once('../include.php');
}

global $em,$log,$system;


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['codConta']))			$codConta			= \Zage\App\Util::antiInjection($_POST['codConta']);

$err	= null;

if (!isset($codConta) || empty($codConta)) {
	$err = $tr->trans("Falta de parâmetros (COD_CONTA)");
}else{
	$aSelContas	= explode(",", $codConta);
}

if ($err) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}


#################################################################################
## Resgata as informações do banco
#################################################################################
$contas		= $em->getRepository('Entidades\ZgfinContaReceber')->findBy(array('codOrganizacao' => $system->getcodOrganizacao(), 'codigo' => $aSelContas));


#################################################################################
## Validação, verificar se as contas podem ser excluídas
#################################################################################
$podeExc	= true;
for ($i = 0; $i < sizeof($contas); $i++) {

	#################################################################################
	## Resgata o perfil da conta
	#################################################################################
	$codPerfil	= ($contas[$i]->getCodContaPerfil()) ? $contas[$i]->getCodContaPerfil()->getCodigo() : 0;

	if (!\Zage\Fin\ContaAcao::verificaAcaoPermitida($codPerfil, $contas[$i]->getCodStatus()->getCodigo(), "EXC")) {
		$podeExc	= false;
	}
}
if (!$podeExc)	{
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Erro de violação de acesso, tentativa de exclusão indevida")));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$em->getConnection()->beginTransaction();
try {

	for ($i = 0; $i < sizeof($contas); $i++) {
		
		$conta		= new \Zage\Fin\ContaReceber();
		$erro		= $conta->exclui($contas[$i]->getCodigo());
		
		if ($erro != false) {
			$em->getConnection()->rollback();
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($erro));
			exit;
		}
	}
	$em->flush();
	$em->clear();
	$em->getConnection()->commit();	
	
	if (sizeof($aSelContas) > 1) {
		$mensagem	= $tr->trans("%s Contas excluídas com sucesso",array('%s' => sizeof($aSelContas)));
	}else{
		$mensagem	= $tr->trans("Conta excluída com sucesso");
	}
	
} catch (\Exception $e) {
	$em->getConnection()->rollback();
 	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 	exit;
}
 
echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($mensagem));
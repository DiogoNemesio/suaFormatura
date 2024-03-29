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
if (isset($_POST['codOrganizacao'])) 		$codOrganizacao			= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
if (isset($_POST['motivo']))	 			$motivo					= \Zage\App\Util::antiInjection($_POST['motivo']);
if (isset($_POST['obs']))		 			$obs					= \Zage\App\Util::antiInjection($_POST['obs']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Validações
#################################################################################
try {
	if (!isset($codOrganizacao) || (!$codOrganizacao)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_ORGANIZACAO"))));
		$err = 1;
	}

	/*** Verificar se o usuario existe ***/
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));

	if (!$oOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Organização não encontranda!"))));
		$err = 1;
	}elseif ($oOrg->getCodStatus()->getCodigo() == "C"){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A formatura já está cancelada."))));
		$err = 1;
	}

	/*** Verificar se a organização tem associação com o usuario ***/
	$numFormandos = \Zage\Fmt\Formatura::getNumFormandos($codOrganizacao);
	if ($numFormandos > 0){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Está formatura já teve um formando cadastrado e deve ser finalizada."))));
		$err = 1;
	}
	
	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Cancelar acesso de todos os usuários
	#################################################################################
	/*** Cancelar Usuário - Organizacao ***/
	$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codOrganizacao' => $codOrganizacao));
	$oUsuOrgStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => C));
	
	for ($i = 0; $i < sizeof($oUsuOrg); $i++) {
		
		//Cancelar Usuario
		if ($oUsuOrg[$i]->getCodStatus()->getCodigo() !="C"){
			$oUsuOrg[$i]->setCodStatus($oUsuOrgStatus);
			$oUsuOrg[$i]->setDataCancelamento(new \DateTime());
			$em->persist($oUsuOrg[$i]);
		}
		
		// Cancelar convite
		$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $oUsuOrg[$i]->getCodUsuario()->getCodigo() , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
		if($oConvite){
			$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => "C"));
		
			for ($j = 0; $j < sizeof($oConvite); $j++) {
				$oConvite[$j]->setCodStatus($oConviteStatus);
				$oConvite[$j]->setDataCancelamento(new \DateTime());
				$em->persist($oConvite[$j]);
			}
		}

	}	
	
	/*** Cancelar na ORGANIZACAO ***/
	$oOrgStatus		= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => "C"));
	$oMotivo		= $em->getRepository('Entidades\ZgadmOrganizacaoMotivoCancelamento')->findOneBy(array('codigo' => $motivo));
	
	$oOrg->setIdentificacao("CANCELADO_FMT:".$oOrg->getCodigo());
	$oOrg->setCodStatus($oOrgStatus);
	$oOrg->setCodMotivoCancelamento($oMotivo);
	$oOrg->setDataCancelamento(new DateTime(now));
	$oOrg->setObservacaoCancelamento($obs);
	
	$em->persist($oOrg);
	
	/***** Flush *****/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Ops! Encontramos um problema, mas já estamos trabalhando para solucionar. Tente novamente em instantes e caso o problema continue entre contato com o nosso suporte.");
	}	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Formatura cancelada com sucesso!")));
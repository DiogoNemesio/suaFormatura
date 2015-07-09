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

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificações
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
	}elseif ($oOrg->getCodStatus()->getCodigo() == 3){
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("A formatura já está cancelada!"))));
		$err = 1;
	}

	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codOrganizacao' => $codOrganizacao));

	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Cancelar acesso de todos os usuários
	#################################################################################
	/*** Cancelarsuario - Organizacao ***/
	$oUsuOrgStatus	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => C));
	
	/*** Cancelar Usuario - Organizacao e Convite ***/
	for ($i = 0; $i < sizeof($oUsuOrg); $i++) {
		
		//Cancelar Usuario
		if ($oUsuOrg[$i]->getCodStatus()->getCodigo() != C){
			$oUsuOrg[$i]->setCodStatus($oUsuOrgStatus);
			$oUsuOrg[$i]->setDataCancelamento(new \DateTime());
			$em->persist($oUsuOrg[$i]);
		}
		$log->debug($oUsuOrg[$i]->getCodUsuario()->getCodigo());
		// Cancelar convite
		$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $oUsuOrg[$i]->getCodUsuario()->getCodigo() , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
		if($oConvite){
			$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => C));
		
			for ($j = 0; $j < sizeof($oConvite); $j++) {
				$oConvite[$j]->setCodStatus($oConviteStatus);
				$oConvite[$j]->setDataCancelamento(new \DateTime());
				$em->persist($oConvite[$j]);
			}
		}

	}	
	/*** Cancelar Organizacao ***/
	$oOrgStatus		= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => 3));
	$oOrg->setCodStatus($oOrgStatus);
	$em->persist($oOrg);
	
	$em->flush();
	$em->clear();
	/***** Flush *
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Erro excluir o formando. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	
****/
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Formatura excluída com sucesso!")));
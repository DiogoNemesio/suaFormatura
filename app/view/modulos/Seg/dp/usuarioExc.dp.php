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
if (isset($_POST['codUsuario'])) 		$codUsuario			= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['codOrganizacao']))	$codOrganizacao		= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Verificações
#################################################################################

try {
	if (!isset($codUsuario) || (!$codUsuario)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_USUARIO"))));
		$err = 1;
	}
	
	/*** Verificar se o usuario existe ***/
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrando"))));
		$err = 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuAdmVal	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuAdmVal) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuAdmVal->getCodStatus()->getCodigo() == 'C'){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Este usuário já está cancelado!"))));
			$err = 1;
		}
	}
	
	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Remover usuario
	#################################################################################
	
	$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codUsuario' => $codUsuario));

	if ($oUsuario->getCodStatus()->getCodigo() == P){
		if (sizeof($oUsuAdm) == 1 && $oUsuAdm[0]->getCodOrganizacao()->getCodigo() == $codOrganizacao && $oUsuAdm[0]->getCodStatus()->getCodigo() == P){
			
			/*** Exclusão dos telefone ***/
			$oTel		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $codUsuario));
			for ($i = 0; $i < sizeof($oTel); $i++) {
				$em->remove($oTel[$i]);
			}
			
			/*** Exclusão da associação ***/
			$em->remove($oUsuAdm[0]);
			
			/*** Exclusão do convite ***/
			$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario));
			for ($i = 0; $i < sizeof($oConvite); $i++) {
				$em->remove($oConvite[$i]);
			}
			
			/*** Exclusão do usuário ***/
			$em->remove($oUsuario);
		}else{
			$aUsuAdm 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
			$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
			$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
			
			if ($aUsuAdm){
				$aUsuAdm->setCodStatus($oStatus);
				$aUsuAdm->setDataCancelamento(new \DateTime());
				$em->persist($aUsuAdm);
			}
			
			if($oConvite){
				$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => C));
			
				for ($i = 0; $i < sizeof($oConvite); $i++) {
					$oConvite[$i]->setCodStatus($oConviteStatus);
					$oConvite[$i]->setDataCancelamento(new \DateTime());
					$em->persist($oConvite[$i]);
				}
			}
			
		}
	}else{
		
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
		$aUsuAdm 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
		$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
		
		if ($aUsuAdm){
			$aUsuAdm->setCodStatus($oStatus);
			$aUsuAdm->setDataCancelamento(new \DateTime());
			$em->persist($aUsuAdm);
		}
		
		if($oConvite){
			$oConviteStatus  = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => C));
				
			for ($i = 0; $i < sizeof($oConvite); $i++) {
				$oConvite[$i]->setCodStatus($oConviteStatus);
				$oConvite[$i]->setDataCancelamento(new \DateTime());
				$em->persist($oConvite[$i]);
			}
		}		
	}

	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'."Usu&aacute;rio exclu&Iacute;do com sucesso!");
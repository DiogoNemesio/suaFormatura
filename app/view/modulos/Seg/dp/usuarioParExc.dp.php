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
$codOrganizacao		= $system->getCodOrganizacao();
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
	$oUsu	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsu) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrando"))));
		$err = 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuOrg->getCodStatus()->getCodigo() == 'C'){
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
	
	$oUsuario			= new \Zage\Seg\Usuario();
	$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codUsuario' => $codUsuario));
	
	if ($oUsu->getCodStatus()->getCodigo() == P){
		if (sizeof($oUsuAdm) == 1 && $oUsuAdm[0]->getCodOrganizacao()->getCodigo() == $codOrganizacao && $oUsuAdm[0]->getCodStatus()->getCodigo() == P){
			/*** Excluir usuario ***/
			$oUsuario->excluirCompleto($oUsu, $oUsuOrg);
			
			/*** Exclusão do convite ***/
			$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario));
			for ($i = 0; $i < sizeof($oConvite); $i++) {
				$em->remove($oConvite[$i]);
			}
			
		}else{
			/*** Cancelar usuario ***/
			$oUsuario->cancelar($oUsu, $oUsuOrg);
			
			/*** Cancelar associação formatura ***/
			$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
			$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsu->getCodigo());
			
			for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
				try {
					$fmtUsuOrg[$i]->setCodStatus($oStatus);
					$fmtUsuOrg[$i]->setDataCancelamento(new \DateTime());
					$em->persist($fmtUsuOrg[$i]);
				} catch (\Exception $e) {
					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
					exit;
				}
			}
			
			/*** Cancelar convite ***/
			$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
			
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
		/*** Cancelar usuario ***/
		$oUsuario->cancelar($oUsu, $oUsuOrg);
		
		/*** Cancelar associação formatura ***/
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
		$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsu->getCodigo());
		for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
			try {
				$fmtUsuOrg[$i]->setCodStatus($oStatus);
				$fmtUsuOrg[$i]->setDataCancelamento(new \DateTime());
				$em->persist($fmtUsuOrg[$i]);
			} catch (\Exception $e) {
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
				exit;
			}
		}

		/*** Cancelar convite ***/
		$oConvite	= $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario , 'codOrganizacaoOrigem' => $codOrganizacao, 'codStatus' => A));
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


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário excluído com sucesso!")));
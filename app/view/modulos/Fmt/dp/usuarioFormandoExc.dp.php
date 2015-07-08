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
## Excluir usuario(formando) e cliente
#################################################################################

	$oUsuario			= new \Zage\Seg\Usuario();	
	
	/***********************
	* Excluir/Cancelar
	***********************/
	$oCli = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsu->getCpf(),'codOrganizacao' => $codOrganizacao));
	
	if ($oCli){
		$oCliPagar 		= $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codPessoa' => $oCli->getCodigo()));
		$oCliReceber 	= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codPessoa' => $oCli->getCodigo()));
	}	

	if ($oCliPagar || $oCliReceber){
		//Cancelar o usuário
		$oUsuario->cancelar($oUsu, $oUsuOrg);
		//Inativar a Pessoa do cliente
		\Zage\Fin\Pessoa::inativa($oCli->getCodigo());			
	}else{
		$oUsuAdm		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findBy(array('codUsuario' => $codUsuario));
		if ($oUsu->getCodStatus()->getCodigo() == P){
			if (sizeof($oUsuAdm) == 1 && $oUsuAdm[0]->getCodOrganizacao()->getCodigo() == $codOrganizacao && $oUsuAdm[0]->getCodStatus()->getCodigo() == P){
				/*** Excluir usuario ***/
				$oUsuario->excluirCompleto($oUsu, $oUsuOrg);
				/*** Excluir cliente ***/
				\Zage\Fin\Pessoa::exclui($oCli->getCodigo());
				/*** Exclusão do convite ***/
				$oConvite = $em->getRepository('Entidades\ZgsegConvite')->findBy(array('codUsuarioDestino' => $codUsuario));
				for ($i = 0; $i < sizeof($oConvite); $i++) {
					$em->remove($oConvite[$i]);
				}
			}else{
				/*** Cancelar usuario ***/
				$oUsuario->cancelar($oUsu, $oUsuOrg);
				/*** Cancelar cliente ***/
				\Zage\Fin\Pessoa::inativa($oCli->getCodigo());	
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
			/*** Cancelar cliente ***/
			\Zage\Fin\Pessoa::inativa($oCli->getCodigo());
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
	}
	
	/***** Flush *****/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao excluir o formando:". $e->getTraceAsString());
		throw new \Exception("Erro excluir o formando. Uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Formando excluído com sucesso!")));
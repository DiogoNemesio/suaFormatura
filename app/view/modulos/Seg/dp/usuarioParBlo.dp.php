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
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Usuário não encontrando"))));
		$err = 1;
	}
	
	/*** Verificar se a organização tem associação com o usuario ***/
	$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $codUsuario , 'codOrganizacao' => $codOrganizacao));
	
	if (!$oUsuOrg) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Esta operação não pode ser concluída, porque não existe uma associação entre o usuário e a organização."))));
		$err = 1;
	}else{
		if ($oUsuOrg->getCodStatus()->getCodigo() != B && $oUsuOrg->getCodStatus()->getCodigo() != A ){		
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Está operação não pode ser concluída, porque a associação entre a organização e o usuário não está ativa."))));
			$err = 1;				
		}
	}
	
	if ($err) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## Bloquear usuario
	#################################################################################
	if ($oUsuOrg->getCodStatus()->getCodigo() == A){
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'B'));
		
		//Associação - Organizacao
		$oUsuOrg->setCodStatus($oStatus);
		$oUsuOrg->setDataBloqueio(new \DateTime());
		$em->persist($oUsuOrg);
		
		//Associação - Formaturas
		$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsuario->getCodigo(),$codOrganizacao);
		for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
			if ($fmtUsuOrg[$i]->getCodStatus()->getCodigo() == A) {
				try {
					$fmtUsuOrg[$i]->setCodStatus($oStatus);
					$fmtUsuOrg[$i]->setDataBloqueio(new \DateTime());
					$em->persist($fmtUsuOrg[$i]);
				} catch (\Exception $e) {
					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
					exit;
				}
			}
		}
		
		$mensagem = 'Usuário bloqueado com sucesso!';
		
	}elseif ($oUsuOrg->getCodStatus()->getCodigo() == B){
		$oStatus 	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'A'));
		
		//Associação - Organizacao
		$oUsuOrg->setCodStatus($oStatus);
		$oUsuOrg->setDataBloqueio(null);
		$em->persist($oUsuOrg);
		
		//Associação - Formaturas
		$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsuario->getCodigo(),$codOrganizacao);
		for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
			if ($fmtUsuOrg[$i]->getCodStatus()->getCodigo() == B) {
				try {
					$fmtUsuOrg[$i]->setCodStatus($oStatus);
					$fmtUsuOrg[$i]->setDataBloqueio(null);
					$em->persist($fmtUsuOrg[$i]);
				} catch (\Exception $e) {
					echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
					exit;
				}
			}
		}
		
		$mensagem = 'Usuário desbloqueado com sucesso!';
	}
	
	#################################################################################
	## Salvar alterações
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans($mensagem)));
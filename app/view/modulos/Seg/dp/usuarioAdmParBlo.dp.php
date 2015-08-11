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
		
		$oUsuOrg->setCodStatus($oStatus);
		$oUsuOrg->setDataBloqueio(new \DateTime());
		$em->persist($oUsuOrg);
		
		//Associação - Formaturas
		$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsuario->getCodigo(), $codOrganizacao);
		for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
			$log->debug('Entrei');
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
	
	$em->flush();

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	die('1'.\Zage\App\Util::encodeUrl('||'));
	exit;
}


echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans($mensagem)));
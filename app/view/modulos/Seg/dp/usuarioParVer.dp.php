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
if (isset($_POST['perfil']))			$codPerfil			= \Zage\App\Util::antiInjection($_POST['perfil']);

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
		$err	= 1;
	}
	
	if (!isset($codOrganizacao) || (!$codOrganizacao)) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Parâmetro não informado : COD_ORGANIZACAO"))));
		$err	= 1;
	}
	
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));

	if (!$oUsuario) {
		die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("USUÁRIO NÃO ENCONTRADO"))));
		$err	= 1;
	}
	
	if ($err != null) {
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
		exit;
	}
	
	#################################################################################
	## ASSOCIAR ORGANIZACAO - USUÁRIO
	#################################################################################
	$oUsuOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));

	if ($oUsuOrg){
		if ($oUsuario->getCodStatus()->getCodigo() == A && $oUsuOrg->getCodStatus()->getCodigo() == A){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Este usuário já está associado a organização!"))));
			$err	= 1;
		}elseif ($oUsuOrg->getCodStatus()->getCodigo() == P){
			die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans("Estamos aguardando a confirmação de cadastro deste usuário!"))));
			$err	= 1;
		}elseif($oUsuOrg->getCodStatus()->getCodigo() == C){
			$oUsuOrg->setDataCancelamento(null);
		} 
	}
	
	if (!$oUsuOrg){
		$enviarEmail		= true;
		$associado 			= false;
		$oUsuOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
	}else{
		if ($oUsuOrg->getCodStatus()->getCodigo() == P || $oUsuOrg->getCodStatus()->getCodigo() == C){
			$enviarEmail		= true;
		}
	}
	
	$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	$oUsuOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	
	$oUsuOrg->setCodUsuario($oUsuario);
	$oUsuOrg->setCodOrganizacao($oOrg);
	$oUsuOrg->setCodPerfil($oPerfil);
	$oUsuOrg->setCodStatus($oUsuOrgStatus);
	
	$em->persist($oUsuOrg);
	
	#################################################################################
	## CRIAR CONVITE
	#################################################################################
	if ($enviarEmail) {
		$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
		$convite		= new \Zage\Seg\Convite();
		$convite->setCodOrganizacaoOrigem($oOrg);
		$convite->setCodUsuarioDestino($oUsuario);
		$convite->setCodStatus($oConviteStatus);
		$convite->salvar();
	}
	
	#################################################################################
	## Salvar as informações
	#################################################################################
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}
	
	#################################################################################
	## Criar notificação
	#################################################################################
	
	if ($enviarEmail) {
	
		$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
		if ($oUsuario->getCodStatus()->getCodigo() == P) {
			$assunto			= "Confirmação de cadastro";
			$template			= 'USUARIO_CADASTRO';
			$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
			$texto = 'Você foi adionado a empresa <b>'.$oOrg->getNome().'</b>. Para concluir o seu cadastro é necessário confimar seus dados.';
		}else{
			$assunto			= "Associação a uma nova empresa";
			$template			= 'USUARIO_CADASTRO';
			$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
			$texto = 'Identificamos que você já é usuário do portal SUAFORMATURA.COM. Confirme seu cadastro para acessar tudo sobre sua nova empresa <b>'.$oOrg->getNome().'</b>.';
		}
		
		//$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => $template));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
		$notificacao->setAssunto($assunto);
		//$notificacao->setCodRemetente($oRemetente);
		
		$notificacao->associaUsuario($oUsuario->getCodigo());
		
		$notificacao->enviaEmail();
		//$notificacao->enviaSistema();
		//$notificacao->setEmail("daniel.cassela@usinacaete.com"); # Se quiser mandar com cópia
		$notificacao->setCodTemplate($template);
		$notificacao->adicionaVariavel('ID', $id);
		$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
		$notificacao->adicionaVariavel("ASSUNTO", $assunto);
		$notificacao->adicionaVariavel("NOME", $oUsuario->getNome());
		$notificacao->adicionaVariavel("TEXTO", $texto);
		$notificacao->salva();
		
		#################################################################################
		## Salvar notificação
		#################################################################################
		try {
			$em->flush();
			$em->clear();
		} catch (Exception $e) {
			$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
			throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
		}
	}

} catch (\Exception $e) {
	die ('1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage())));
	exit;
}

echo '0'.\Zage\App\Util::encodeUrl('||'.htmlentities($tr->trans('Usuário associado com sucesso!')));
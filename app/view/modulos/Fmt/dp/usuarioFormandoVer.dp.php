<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;


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
	
	/***********************
	 * Ativar organização (formatura)
	 ***********************/
	$numFormando = \Zage\Fmt\Formatura::getNumFormandos($system->getCodOrganizacao());
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	
	if ($numFormando == 0){
		$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => "A"));
		$oOrg->setCodStatus($oCodStatus);
		$oOrg->setDataAtivacao(new DateTime(now));
	
		$em->persist($oOrg);
	}	
	
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
	
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	$oUsuOrgStatus  	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	
	$oUsuOrg->setCodUsuario($oUsuario);
	$oUsuOrg->setCodOrganizacao($oOrg);
	$oUsuOrg->setCodPerfil($oPerfil);
	$oUsuOrg->setCodStatus($oUsuOrgStatus);
	
	$em->persist($oUsuOrg);
	
	/***********************
	 * Salavar cliente
	 ***********************/
	$oCliente = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsuario->getCpf() , 'codOrganizacao' => $codOrganizacao));
	
	if(!$oCliente){
		$oCliente = new \Entidades\ZgfinPessoa();
		$oCliente->setDataCadastro(new DateTime(now));
		$oCliente->setObservacao('Importado do cadastro de formando.');
	}
	
	$clienteTipo = $em->getRepository('Entidades\ZgfinPessoaTipo')->findOneBy(array('codigo' => O));
	
	$oCliente->setCodOrganizacao($oOrg);
	$oCliente->setNome($oUsuario->getNome());
	$oCliente->setCgc($oUsuario->getCpf());
	$oCliente->setEmail($oUsuario->getUsuario());
	$oCliente->setCodTipoPessoa($clienteTipo);
	$oCliente->setIndContribuinte(0);
	$oCliente->setIndCliente(1);
	$oCliente->setIndFornecedor(1);
	$oCliente->setIndTransportadora(0);
	$oCliente->setIndEstrangeiro(0);
	$oCliente->setIndAtivo(1);
	$oCliente->setCodSexo($oSexo);
	
	$em->persist($oCliente);
	
	//ENDEREÇO
	if ($oUsuario->getCodLogradouro()){
		
		$oClienteEnd = $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $oCliente->getCodigo()));
		$oEndTipo	 = $em->getRepository('Entidades\ZgfinEnderecoTipo')->findOneBy(array('codigo' => C));
		$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $oUsuario->getCodLogradouro()));
		
		if (!$oClienteEnd){
			$oClienteEnd = new \Entidades\ZgfinPessoaEndereco();
		}
		
		$oClienteEnd->setCodPessoa($oCliente);
		$oClienteEnd->setCodTipoEndereco($oEndTipo);
		$oClienteEnd->setCodLogradouro($oCodLogradouro);
		$oClienteEnd->setCep($oUsuario->getCep());
		$oClienteEnd->setEndereco($oUsuario->getEndereco());
		$oClienteEnd->setBairro($oUsuario->getBairro());
		$oClienteEnd->setNumero($oUsuario->getNumero());
		$oClienteEnd->setComplemento($oUsuario->getComplemento());
		
		$em->persist($oClienteEnd);
	}
	
	//TELEFONE
	$oTel = $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codProprietario' => $oUsuario->getCodigo()));
	if ($oTel){
		
		$telefone 		= array();
		$codTipoTel 	= array();
		$codTelefone 	= array();
		
		for ($i = 0; $i < sizeof($oTel); $i++) {
			$telefone[$i] 		= $oTel[$i]->getTelefone();
			$codTipoTel[$i] 	= $oTel[$i]->getCodTipoTelefone()->getCodigo();
			$codTelefone[$i] 	= $oTel[$i]->getCodigo();
		}
		
		$oCliTel			= new \Zage\App\Telefone();
		$oCliTel->_setEntidadeTel('Entidades\ZgfinPessoaTelefone');
		$oCliTel->_setCodProp($oCliente);
		$oCliTel->_setTelefone($telefone);
		$oCliTel->_setCodTipoTel($codTipoTel);
		$oCliTel->_setCodTelefone($codTelefone);
		
		$retorno	= $oCliTel->salvar();
	}
	
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
		throw new \Exception("Ops!! Não conseguimos realizar a operaçao. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
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
			$texto = 'Você foi adionado a formatura <b>'.$oOrg->getNome().'</b>. Confirme seu cadastro para acessar tudo sobre sua formatura.'; 
		}else{
			$assunto			= "Associação a uma nova formatura";
			$template			= 'USUARIO_CADASTRO';
			$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
			$texto = 'Identificamos que você já é usuário do portal SUAFORMATURA.COM. Confirme seu cadastro para acessar tudo sobre sua nova formatura <b>'.$oOrg->getNome().'</b>.';
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
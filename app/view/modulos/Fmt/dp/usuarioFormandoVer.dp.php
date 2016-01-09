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
	
	/***********************
	 * Ativar organização (formatura)
	 ***********************/
	$numFormando = \Zage\Fmt\Formatura::getNumFormandos($system->getCodOrganizacao());
	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	
	if ($numFormando == 0){
		//ATIVAR A FORMATURA
		$oCodStatus			= $em->getRepository('Entidades\ZgadmOrganizacaoStatusTipo')->findOneBy(array('codigo' => "A"));
		$oOrg->setCodStatus($oCodStatus);
		$oOrg->setDataAtivacao(new DateTime(now));
	
		$em->persist($oOrg);
		
		//CRIAR CENTRO DE CUSTO NA EMPRESA QUE ADMINISTRA
		$oOrgAdm	= $em->getRepository('Entidades\ZgadmOrganizacaoAdm')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
		
		if ($oOrgAdm){
			$oCentroTipo	= $em->getRepository('Entidades\ZgfinCentroCustoTipo')->findOneBy(array('codigo' => "F"));
			$oCentro		= new \Entidades\ZgfinCentroCusto();
				
			$oCentro->setCodOrganizacao($oOrgAdm->getCodOrganizacaoPai());
			$oCentro->setCodTipoCentroCusto($oCentroTipo);
			$oCentro->setDescricao("FMT:(".$oOrg->getCodigo().")".$oOrg->getNome());
			$oCentro->setIndCredito(1);
			$oCentro->setIndDebito(1);
			$oCentro->setIndAtivo(1);
				
			$em->persist($oCentro);
		}
		 
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
	$oPessoa = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsuario->getCpf()));
	
	if(!$oPessoa){
		$oPessoa = new \Entidades\ZgfinPessoa();
		$oPessoa->setDataCadastro(new DateTime(now));
		$oPessoa->setObservacao('Importado do cadastro de formando.');
	}
	
	$clienteTipo = $em->getRepository('Entidades\ZgfinPessoaTipo')->findOneBy(array('codigo' => "F"));
	
	$oPessoa->setNome($oUsuario->getNome());
	$oPessoa->setFantasia($oUsuario->getNome());
	$oPessoa->setCgc($oUsuario->getCpf());
	$oPessoa->setRg($oUsuario->getRg());
	$oPessoa->setDataNascimento($oUsuario->getDataNascimento());
	$oPessoa->setEmail($oUsuario->getUsuario());
	$oPessoa->setCodTipoPessoa($clienteTipo);
	$oPessoa->setIndContribuinte(0);
	$oPessoa->setIndEstrangeiro(0);
	$oPessoa->setCodSexo($oSexo);
	
	$em->persist($oPessoa);
	
	/***********************
	 * Salvar PESSOA_ORGANIZACAO
	 ***********************/
	$oPessoaOrg	= $em->getRepository('Entidades\ZgfinPessoaOrganizacao')->findOneBy(array('codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
	if (!$oPessoaOrg) {
		$oPessoaOrg	= new \Entidades\ZgfinPessoaOrganizacao();
		$oPessoaOrg->setDataCadastro(new DateTime(now));
	}
	
	$oPessoaOrg->setCodPessoa($oPessoa);
	$oPessoaOrg->setCodOrganizacao($oOrg);
	$oPessoaOrg->setIndAtivo(1);
	$oPessoaOrg->setIndCliente(1);
	$oPessoaOrg->setIndFornecedor(1);
	$oPessoaOrg->setIndTransportadora(0);
	$oPessoaOrg->setIndFormando(1);
	$oPessoaOrg->setIndContribuinte(0);
	
	$em->persist($oPessoaOrg);
	
	/***********************
	* Endereço pessoa
	***********************/
	if ($oUsuario->getCodLogradouro()){
		
		$oPessoaEnd = $em->getRepository('Entidades\ZgfinPessoaEnderecoOrganizacao')->findOneBy(array('codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
		$oEndTipo	 = $em->getRepository('Entidades\ZgfinEnderecoTipo')->findOneBy(array('codigo' => "F"));
		
		if (!$oPessoaEnd){
			$oPessoaEnd = new \Entidades\ZgfinPessoaEnderecoOrganizacao();
		}
		
		$oPessoaEnd->setCodOrganizacao($oOrg);
		$oPessoaEnd->setCodPessoa($oPessoa);
		$oPessoaEnd->setCodTipoEndereco($oEndTipo);
		$oPessoaEnd->setCodLogradouro($oUsuario->getCodLogradouro());
		$oPessoaEnd->setCep($oUsuario->getCep());
		$oPessoaEnd->setEndereco($oUsuario->getEndereco());
		$oPessoaEnd->setBairro($oUsuario->getBairro());
		$oPessoaEnd->setNumero($oUsuario->getNumero());
		$oPessoaEnd->setComplemento($oUsuario->getComplemento());
		$oPessoaEnd->setIndEndCorreto($oUsuario->getIndEndCorreto());
		
		$em->persist($oPessoaEnd);
	}
	
	/***********************
	* Telefone pessoa
	***********************/
	$telefones		= $em->getRepository('Entidades\ZgfinPessoaTelefoneOrganizacao')->findBy(array('codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo()));
 	
 	// Exclusão
 	for ($i = 0; $i < sizeof($telefones); $i++) {
 		if (!in_array($telefones[$i]->getTelefone(), $telefone)) {
 			try {
 				$em->remove($telefones[$i]);
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o telefone: ".$telefones[$i]->getTelefone()." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 		}
 	
 	}
 	
 	// Criação e alteração
 	for ($i = 0; $i < sizeof($telefone); $i++) {
 		$infoTel		= $em->getRepository('Entidades\ZgfinPessoaTelefoneOrganizacao')->findOneBy(array('codPessoa' => $oPessoa->getCodigo() , 'codOrganizacao' => $oOrg->getCodigo() , 'telefone' => $telefone[$i]));
 		if (!$infoTel) {
 			$infoTel		= new \Entidades\ZgfinPessoaTelefoneOrganizacao();
 		}
 		
 		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i]) {
 			
 			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
 			
 			$infoTel->setCodPessoa($oPessoa);
 			$infoTel->setCodOrganizacao($oOrg);
 			$infoTel->setCodTipoTelefone($oTipoTel);
 			$infoTel->setTelefone($telefone[$i]);
 		 	
 			try {
 				$em->persist($infoTel);
 			} catch (\Exception $e) {
 				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível cadastrar o telefone: ".$telefone[$i]." Erro: ".$e->getMessage());
 				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
 				exit;
 			}
 		}
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
	$em->flush();
	$em->clear();
	/**
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operaçao. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}
	**/
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
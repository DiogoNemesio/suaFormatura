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
$codOrganizacao	= $system->getCodOrganizacao();
if (isset($_POST['codUsuario'])) 		$codUsuario		= \Zage\App\Util::antiInjection($_POST['codUsuario']);
if (isset($_POST['email'])) 			$usuario		= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['dataNasc'])) 			$dataNasc		= \Zage\App\Util::antiInjection($_POST['dataNasc']);
if (isset($_POST['rg']))	 			$rg				= \Zage\App\Util::antiInjection($_POST['rg']);
if (isset($_POST['apelido']))			$apelido		= \Zage\App\Util::antiInjection($_POST['apelido']);
if (isset($_POST['sexo'])) 				$sexo			= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['cpf'])) 				$cpf			= \Zage\App\Util::antiInjection($_POST['cpf']);
if (isset($_POST['perfil'])) 			$codPerfil		= \Zage\App\Util::antiInjection($_POST['perfil']);

if (isset($_POST['codLogradouro']))		$codLogradouro	= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['endCorreto']))		$endCorreto		= \Zage\App\Util::antiInjection($_POST['endCorreto']);
if (isset($_POST['descLogradouro']))	$descLogradouro	= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))			$bairro			= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))		$complemento	= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['cep']))				$cep			= \Zage\App\Util::antiInjection($_POST['cep']);

if (isset($_POST['codTipoTel']))		$codTipoTel			= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone		= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone			= $_POST['telefone'];
if (!isset($codTipoTel))				$codTipoTel			= array();
if (!isset($codTelefone))				$codTelefone		= array();
if (!isset($telefone))					$telefone			= array();

if (isset($_POST['acesso'])) 			$acesso			= \Zage\App\Util::antiInjection($_POST['acesso']);
if (!isset($acesso))					$acesso			= array();


#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################


if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
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
	
	/*********************** 
	 * Salvar usuário 
	 ***********************/
	$oUsuario			= new \Zage\Seg\Usuario();
	
	/*********************** 
	 * Resgatar os objetos de relacionamento 
	 ***********************/
	$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oSexo				= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	/*********************** 
	 * Salvar os dados do usuário 
	 ***********************/
	$oUsuario->setUsuario($usuario);
	$oUsuario->setNome($nome);
	$oUsuario->setRg($rg);
	$oUsuario->setDataNascimento($dataNasc);
	$oUsuario->setApelido($apelido);
	$oUsuario->setCpf($cpf);
	$oUsuario->setSexo($oSexo);
	$oUsuario->setCodLogradouro($oCodLogradouro);
	$oUsuario->setIndEndCorreto($endCorreto);
	$oUsuario->setCep($cep);
	$oUsuario->setEndereco($descLogradouro);
	$oUsuario->setBairro($bairro);
	$oUsuario->setNumero($numero);
	$oUsuario->setComplemento($complemento);
	
	//Resgate de variáveis
	$oUsuario->_setCodUsuario($codUsuario);
	$oUsuario->_setCodOrganizacao($codOrganizacao);
	
	//Associação
	$oUsuario->_setPerfil($codPerfil);
	
	//Endereço
	$oUsuario->_setIndEndObrigatorio(false);
	$endObrigatorio = false;
	
	//Telefone
	$oUsuario->_setEntidadeTel('Entidades\ZgsegUsuarioTelefone');
	$oUsuario->_setTelefone($telefone);
	$oUsuario->_setCodTipoTel($codTipoTel);
	$oUsuario->_setCodTelefone($codTelefone);
	
	$retorno	= $oUsuario->salvar();
	
	if ($retorno && is_string($retorno)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$retorno);
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($retorno));
		exit;
	}
	
	/***********************
	* Salavar cliente
	***********************/
	$oCliente = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsuario->_getUsuario()->getCpf() , 'codOrganizacao' => $codOrganizacao));
	
	if(!$oCliente){
		$oCliente = new \Entidades\ZgfinPessoa();
		$oCliente->setDataCadastro(new DateTime(now));
		$oCliente->setObservacao('Importado do cadastro de formando.');
	}
	
	$clienteTipo = $em->getRepository('Entidades\ZgfinPessoaTipo')->findOneBy(array('codigo' => O));
	
	$oCliente->setCodOrganizacao($oOrg);
	$oCliente->setNome($oUsuario->_getUsuario()->getNome());
	$oCliente->setCgc($oUsuario->_getUsuario()->getCpf());
	$oCliente->setRg($oUsuario->_getUsuario()->getRg());
	$oCliente->setDataNascimento($oUsuario->_getUsuario()->getDataNascimento());
	$oCliente->setEmail($oUsuario->_getUsuario()->getUsuario());
	$oCliente->setCodTipoPessoa($clienteTipo);
	$oCliente->setIndContribuinte(0);
	$oCliente->setIndCliente(1);
	$oCliente->setIndFornecedor(1);
	$oCliente->setIndTransportadora(0);
	$oCliente->setIndEstrangeiro(0);
	$oCliente->setIndAtivo(1);
	$oCliente->setCodSexo($oSexo);
	
	$em->persist($oCliente);
	
	//ENDEREÇO CLIENTE
	if ($codLogradouro){
		$oClienteEnd = $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $oCliente->getCodigo()));
		$oEndTipo	 = $em->getRepository('Entidades\ZgfinEnderecoTipo')->findOneBy(array('codigo' => C));
		
		if (!$oClienteEnd){
			$oClienteEnd = new \Entidades\ZgfinPessoaEndereco();
		}
		
		$oClienteEnd->setCodPessoa($oCliente);
		$oClienteEnd->setCodTipoEndereco($oEndTipo);
		$oClienteEnd->setCodLogradouro($oCodLogradouro);
		$oClienteEnd->setCep($oUsuario->_getUsuario()->getCep());
		$oClienteEnd->setEndereco($oUsuario->_getUsuario()->getEndereco());
		$oClienteEnd->setBairro($oUsuario->_getUsuario()->getBairro());
		$oClienteEnd->setNumero($oUsuario->_getUsuario()->getNumero());
		$oClienteEnd->setComplemento($oUsuario->_getUsuario()->getComplemento());
		$oClienteEnd->setIndEndCorreto($oUsuario->_getUsuario()->getIndEndCorreto());
		
		$em->persist($oClienteEnd);
	}
	
	//Telefone
	$oCliTel			= new \Zage\App\Telefone();
	$oCliTel->_setEntidadeTel('Entidades\ZgfinPessoaTelefone');
	$oCliTel->_setCodProp($oCliente);
	$oCliTel->_setTelefone($telefone);
	$oCliTel->_setCodTipoTel($codTipoTel);
	$oCliTel->_setCodTelefone($codTelefone);
	
	$retorno	= $oCliTel->salvar();
	
	/*********************** 
	 * Cria convite
	 ***********************/
	if ($oUsuario->_getEnviarEmail() == true) {
		$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
		$convite		= new \Zage\Seg\Convite();
		$convite->setCodOrganizacaoOrigem($oOrg);
		$convite->setCodUsuarioDestino($oUsuario->_getUsuario());
		$convite->setCodStatus($oConviteStatus);
		$convite->salvar();
	}
	
	/*********************** 
	 * Commit (salvar)
	 ***********************/
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Ops!! Não conseguimos realizar a operação. Caso o problema continue entre em contato com o suporte do portal SUAFORMATURA.COM");
	}

	#################################################################################
	## Enviar notificação
	#################################################################################
	//Verificar se é pra enviar notificacao
	if ($oUsuario->_getEnviarEmail() == true && $oUsuario->_getCodigo()) {
	
		$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuario->_getUsuOrg()->getCodigo().'&_cdu02='.$oUsuario->_getUsuario()->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
		if ($oUsuario->_getUsuario()->getCodStatus()->getCodigo() == P) {
			$assunto			= "Confirmação de cadastro";
			$nome				= $oUsuario->getNome();
			$texto = 'Sua conta foi criada e associada a turma <b>'.$oOrg->getNome().'</b>. Confirme seu cadastro no link abaixo e acesse tudo sobre sua formatura.';
			$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
		}
	
		//$oRemetente		= $em->getReference('\Entidades\ZgsegUsuario',$system->getCodUsuario());
		$template		= $em->getRepository('\Entidades\ZgappNotificacaoTemplate')->findOneBy(array('template' => 'USUARIO_CADASTRO'));
		$notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
		$notificacao->setAssunto($assunto);
		//$notificacao->setCodRemetente($oRemetente);
	
		$notificacao->associaUsuario($oUsuario->_getCodigo());
	
		$notificacao->enviaEmail();
		//$notificacao->enviaSistema();
		//$notificacao->setEmail("daniel.cassela@usinacaete.com"); # Se quiser mandar com cópia
		$notificacao->setCodTemplate($template);
		$notificacao->adicionaVariavel('ID', $id);
		$notificacao->adicionaVariavel("CONFIRM_URL", $confirmUrl);
		$notificacao->adicionaVariavel("ASSUNTO", $assunto);
		$notificacao->adicionaVariavel("NOME", $nome);
		$notificacao->adicionaVariavel("TEXTO", $texto);
		$notificacao->salva();
		/**
		 //NOTIFICAÇÃO POR WHATSAPP
		 $notificacao	= new \Zage\App\Notificacao(\Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO, \Zage\App\Notificacao::TIPO_DEST_USUARIO);
		 $notificacao->setAssunto($assunto);
		 //$notificacao->setCodUsuario($oRemetente);
		 $notificacao->associaUsuario($oUsuario->_getCodigo());
	
		 $notificacao->enviaWa();
		 $notificacao->setMensagem("Olá".$nome.", você foi adiconar a formatura".$oOrg->getNome()." Para acessar o portal da sua formatura é necessário que confirme o recebimento de confirmação de cadastro no seu email.
	
		 www.suaformatura.com/".$oOrg->getIdentificacao()."");
		 $notificacao->salva();
		**/
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
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Formando salvo com sucesso."));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->_getCodigo());

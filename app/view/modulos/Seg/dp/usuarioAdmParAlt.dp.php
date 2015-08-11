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
if (isset($_POST['codOrganizacao'])) 	$codOrganizacao	= \Zage\App\Util::antiInjection($_POST['codOrganizacao']);
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
#################################################################################
## Salvar no banco
#################################################################################
try {

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
	
	#################################################################################
	## Salvar os acessos a organizações(formaturas)
	#################################################################################
	$oPerfil	= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	
	//Retirar acesso
	$oUsuOrgStatusCan  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'C'));
	$fmtUsuOrg		= \Zage\Fmt\Organizacao::listaFmtUsuOrg($oUsuario->_getUsuario()->getCodigo(),$codOrganizacao);
	for ($i = 0; $i < sizeof($fmtUsuOrg); $i++) {
		if (!in_array($fmtUsuOrg[$i]->getCodOrganizacao()->getCodigo(), $acesso)) {
			try {
				$fmtUsuOrg[$i]->setCodStatus($oUsuOrgStatusCan);
				$fmtUsuOrg[$i]->setCodPerfil($oPerfil);
				$em->persist($fmtUsuOrg[$i]);
			} catch (\Exception $e) {
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível excluir da lista de carteiras o valor: ".$infoCarteiras[$i]->getCodCarteira()->getCodigo()." Erro: ".$e->getMessage()));
				exit;
			}
		}
	}
	//Atribuir acesso
	for ($i = 0; $i < sizeof($acesso); $i++) {
		$oValor		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->_getUsuario()->getCodigo(), 'codOrganizacao' => $acesso[$i]));
		if (!$oValor) {
			$oValor		= new \Entidades\ZgsegUsuarioOrganizacao();
		}	
		
		$oFmt		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $acesso[$i]));
		$oValor->setCodUsuario($oUsuario->_getUsuario());
		$oValor->setCodOrganizacao($oFmt);
		$oValor->setCodPerfil($oPerfil);
		$oValor->setCodStatus($oUsuario->_getUsuOrg()->getCodStatus());
		
		try {
			$em->persist($oValor);
		} catch (\Exception $e) {
			echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities("Não foi possível cadastrar o valor: ".$acesso[$i]." Erro: ".$e->getMessage()));
			exit;
		}
	}
	
	#################################################################################
	## Cria o convite
	#################################################################################
	if ($oUsuario->_getEnviarEmail() == true) {
		$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
		$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
		$convite		= new \Zage\Seg\Convite();
		$convite->setCodOrganizacaoOrigem($oOrg);
		$convite->setCodUsuarioDestino($oUsuario->_getUsuario());
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
	## Enviar notificação
	#################################################################################
	//Verificar se é pra enviar notificacao
	if ($oUsuario->_getEnviarEmail() == true && $oUsuario->_getCodigo()) {
	
		$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuario->_getUsuOrg()->getCodigo().'&_cdu02='.$oUsuario->_getUsuario()->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
		if ($oUsuario->_getUsuario()->getCodStatus()->getCodigo() == P) {
			//$tpl->load(MOD_PATH . "/Seg/html/usuarioCadEmail.html");
			$assunto			= "Confirmação de cadastro";
			$nome				= $oUsuario->getNome();
			$texto = 'Você foi adionado a empresa <b>'.$oOrg->getNome().'</b>. Para concluir o seu cadastro é necessário confimar seus dados.';
			$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
		}else{
			$assunto			= "Associação a uma nova empresa";
			$template			= 'USUARIO_CADASTRO';
			$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
			$texto = 'Identificamos que você já é usuário do portal SUAFORMATURA.COM. Confirme seu cadastro para acessar tudo sobre sua nova formatura <b>'.$oOrg->getNome().'</b>.';
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
$log->debug($oUsuario->_getUsuario()->getCodigo());
$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->_getUsuario()->getCodigo());

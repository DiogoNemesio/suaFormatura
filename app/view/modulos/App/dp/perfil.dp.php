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
if (isset($_POST['_mudarUser'])) 		$_mudarUser		= \Zage\App\Util::antiInjection($_POST['_mudarUser']);

if (isset($_POST['usuario'])) 			$usuario		= \Zage\App\Util::antiInjection($_POST['usuario']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['apelido'])) 			$apelido		= \Zage\App\Util::antiInjection($_POST['apelido']);
if (isset($_POST['cpf'])) 				$cpf			= \Zage\App\Util::antiInjection($_POST['cpf']);
if (isset($_POST['sexo'])) 				$sexo			= \Zage\App\Util::antiInjection($_POST['sexo']);
if (isset($_POST['avatar'])) 			$avatar			= \Zage\App\Util::antiInjection($_POST['avatar']);
if (isset($_POST['indTrocarSenha']))	$indTrocarSenha	= \Zage\App\Util::antiInjection($_POST['indTrocarSenha']);
/** Endereco **/
if (isset($_POST['codLogradouro']))		$codLogradouro	= \Zage\App\Util::antiInjection($_POST['codLogradouro']);
if (isset($_POST['descLogradouro']))	$descLogradouro	= \Zage\App\Util::antiInjection($_POST['descLogradouro']);
if (isset($_POST['bairro']))			$bairro			= \Zage\App\Util::antiInjection($_POST['bairro']);
if (isset($_POST['complemento']))		$complemento	= \Zage\App\Util::antiInjection($_POST['complemento']);
if (isset($_POST['numero']))			$numero			= \Zage\App\Util::antiInjection($_POST['numero']);
if (isset($_POST['cep']))				$cep			= \Zage\App\Util::antiInjection($_POST['cep']);
/** Contato **/
if (isset($_POST['codTipoTel']))		$codTipoTel		= $_POST['codTipoTel'];
if (isset($_POST['codTelefone']))		$codTelefone	= $_POST['codTelefone'];
if (isset($_POST['telefone']))			$telefone		= $_POST['telefone'];

if (!isset($codTipoTel))				$codTipoTel		= array();
if (!isset($codTelefone))				$codTelefone	= array();
if (!isset($telefone))					$telefone		= array();
#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/** Nome **/
if (!isset($nome) || (empty($nome)) || (strlen($nome) < 4)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Nome inválido"));
	$err	= 1;
}

if (!isset($avatar)) {
	$avatar	= null;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Cancelar solicitacao de alteracao de email
#################################################################################
if($_mudarUser == -1){
	#################################################################################
	## Verifica se o registro já existe no banco
	#################################################################################
	$oHistEmail		= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codStatus' => 'A', 'codUsuario' => $system->getCodUsuario()));
	$oStatus		= $em->getRepository('Entidades\ZgsegHistEmailStatus')->findOneBy(array('codigo' => 'C'));
	
	$oHistEmail->setCodStatus($oStatus);
	
	$em->persist($oHistEmail);
	$em->flush();
	$em->detach($oHistEmail);
	
	$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Solicitação cancelada com sucesso !!!"));
	echo '0'.\Zage\App\Util::encodeUrl('|'.$oHistEmail->getCodigo());
}
else if($_mudarUser == 0){
	#################################################################################
	## Salvar no banco
	#################################################################################
	try {
		
		$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
		$oSexo			= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
		$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
		
		$oUsuario->setNome($nome);
		$oUsuario->setApelido($apelido);
		$oUsuario->setCpf($cpf);
		$oUsuario->setSexo($oSexo);
		$oUsuario->setCodLogradouro($oLogradouro);
		$oUsuario->setCep($cep);
	 	$oUsuario->setEndereco($descLogradouro);
	 	$oUsuario->setBairro($bairro);
	 	$oUsuario->setComplemento($complemento);
	 	$oUsuario->setNumero($numero);
		
		if (isset($avatar) && (!empty($avatar))) {
			$oAvatar	= $em->getRepository('Entidades\ZgsegAvatar')->findOneBy(array('codigo' => $avatar));
			$oUsuario->setAvatar($oAvatar);
		}
	
		$em->persist($oUsuario);
		$em->flush();
		//$em->detach($oUsuario);
		
		#################################################################################
		## Contato
		#################################################################################
		$telefones = $em->getRepository ( 'Entidades\ZgsegUsuarioTelefone' )->findBy ( array (
				'codProprietario' => $system->getCodUsuario() 
		) );
		
		################################################################################
		# Exclusão
		################################################################################
		for($i = 0; $i < sizeof ( $telefones ); $i ++) {
			if (! in_array ( $telefones [$i]->getCodigo (), $codTelefone )) {
				try {
					$em->remove ( $telefones [$i] );
					$em->flush ();
				} catch ( \Exception $e ) {
					$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível excluir o telefone: " . $telefones [$i]->getTelefone () . " Erro: " . $e->getMessage () );
					echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
					exit ();
				}
			}
		}
		
		################################################################################
		# Criação / Alteração
		################################################################################
		for($i = 0; $i < sizeof ( $codTelefone ); $i ++) {
			$infoTel = $em->getRepository ( 'Entidades\ZgsegUsuarioTelefone' )->findOneBy ( array (
					'codigo' => $codTelefone [$i],
					'codProprietario' => $oUsuario->getCodigo () 
			) );
			
			if (! $infoTel) {
				$infoTel = new \Entidades\ZgsegUsuarioTelefone ();
			}
			
			if(!$telefone [$i]){
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Campo Telefone precisa ser preenchido !!!"));
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
				exit;
			}
			
			
			if ($infoTel->getCodTipoTelefone () !== $codTipoTel [$i] || $infoTel->getTelefone () !== $telefone [$i]) {
				
				$oTipoTel = $em->getRepository ( 'Entidades\ZgappTelefoneTipo' )->findOneBy ( array (
						'codigo' => $codTipoTel [$i] 
				) );
				
				$infoTel->setCodProprietario($oUsuario);
				$infoTel->setCodTipoTelefone ( $oTipoTel );
				$infoTel->setTelefone ( $telefone [$i] );
				
				try {
					$em->persist ( $infoTel );
					$em->flush ();
					$em->detach ( $infoTel );
				} catch ( \Exception $e ) {
					$system->criaAviso ( \Zage\App\Aviso\Tipo::ERRO, "Não foi possível cadastrar o telefone: " . $telefone [$i] . " Erro: " . $e->getMessage () );
					echo '1' . \Zage\App\Util::encodeUrl ( '||' . htmlentities ( $e->getMessage () ) );
					exit ();
				}
			}
		}
	
	} catch (\Exception $e) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
	
	
	$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
	echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());
}else{
	#################################################################################
	## Salvar no banco
	#################################################################################
	try {
		#################################################################################
		## Verifica se o registro já existe no banco
		#################################################################################
		if (!empty($codUsuario)) {
			$oHistEmail		= $em->getRepository('Entidades\ZgsegUsuarioHistEmail')->findOneBy(array('codUsuario' => $system->getCodUsuario()));
			if (!$oHistEmail)	$oHistEmail	= new \Entidades\ZgsegUsuarioHistEmail();
		}else{
			$oHistEmail	= new \Entidades\ZgsegUsuarioHistEmail();
		}
		
		$oUsuario		= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $system->getCodUsuario()));
		$oStatus		= $em->getRepository('Entidades\ZgsegHistEmailStatus')->findOneBy(array('codigo' => 'A'));
		
		$oHistEmail->setCodUsuario($oUsuario);
		$oHistEmail->setEmailAnterior($oUsuario->getUsuario());
		$oHistEmail->setEmailNovo($usuario);
		$oHistEmail->setDataAlteracao(new \DateTime("now"));
		//$oHistEmail->setDataConfirmacaoAnterior(null);
		//$oHistEmail->setDataConfirmacaoNovo(null);
		$oHistEmail->setSenhaAlteracao(\Zage\Seg\Perfil::_geraSenha());
		$oHistEmail->setIndConfirmadoAnterior(0);
		$oHistEmail->setIndConfirmadoNovo(0);
		//$oHistEmail->setIpConfirmacaoAnterior(null);
		//$oHistEmail->setIpConfirmacaoNovo(null);
		$oHistEmail->setCodStatus($oStatus);
		
		$em->persist($oHistEmail);
		$em->flush();
		$em->detach($oHistEmail);
		
	} catch (\Exception $e) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
		echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
		exit;
	}
	
	#################################################################################
	## Carregando o template html do email
	#################################################################################
	$tpl		= new \Zage\App\Template();
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oHistEmail->getCodigo().'&_cdu02='.$oHistEmail->getSenhaAlteracao().'&_cdu03='.$oHistEmail->getEmailAnterior().'&_cdu04=A');

	$tpl->load(MOD_PATH . "/App/html/perfilConfirmEmail.html");
	$assunto			= "Alteração de email";
	$nome				= $oUsuario->getNome();
	$texto				= "Seu email foi alterado, mas ainda precisa ser confirmado. Para isso, clique no link abaixo:";
	$confirmUrl			= ROOT_URL . "/App/u02.php?cid=".$cid;

	$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('ID'					,$id);
	$tpl->set('CONFIRM_URL'			,$confirmUrl);
	$tpl->set('ASSUNTO'				,$assunto);
	$tpl->set('TEXTO'				,$texto);
	$tpl->set('NOME'				,$nome);
	$tpl->set('URL_ORG'				,$oOrg->getNome());
	#################################################################################
	## Criar os objeto do email ,transporte e validador
	#################################################################################
	$mail 			= \Zage\App\Mail::getMail();
	$transport 		= \Zage\App\Mail::getTransport();
	$validator 		= new \Zend\Validator\EmailAddress();
	$htmlMail 		= new MimePart($tpl->getHtml());
	$htmlMail->type = "text/html";
	$body 			= new MimeMessage();
	
	#################################################################################
	## Definir o conteúdo do e-mail
	#################################################################################
	$body->setParts(array($htmlMail));
	$mail->setBody($body);
	$mail->setSubject("<ZageMail> ".$assunto);
	
	#################################################################################
	## Definir os destinatários
	#################################################################################
	$mail->addTo($oHistEmail->getEmailAnterior());
	
	#################################################################################
	## Salvar as informações e enviar o e-mail
	#################################################################################
	try {
		$transport->send($mail);
	} catch (Exception $e) {
		$log->debug("Erro ao enviar o e-mail:". $e->getTraceAsString());
		throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
	}
	
	$system->criaAviso(\Zage\App\Aviso\Tipo::ALERTA,$tr->trans("Para que ocorra a mudança, foi enviado um email de confirmação para o antigo email !!!"));
	echo '0'.\Zage\App\Util::encodeUrl('|'.$oHistEmail->getCodigo());
}


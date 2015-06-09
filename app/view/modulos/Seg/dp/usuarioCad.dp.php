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
if (isset($_POST['email'])) 			$usuario		= \Zage\App\Util::antiInjection($_POST['email']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
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
/** Nome **/
if (isset($nome) || !empty($nome)) {
	if (strlen($nome) < 5){
		if(strlen($nome) == 0){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Nome deve ser preenchido!"));
			$err	= 1;
		}else{
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome muito pequeno, informe o nome completo !!"));
			$err	= 1;
		}
	}elseif (strlen($nome) > 60){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome tem limite de 60 caracteres !!"));
		$err	= 1;
	}
}

/** Usuário (email) **/
if (isset($usuario) || !empty($usuario)) {
	if (strlen($usuario) == 0){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Email deve ser preenchido!"));
		$err	= 1;
	}elseif(\Zage\App\Util::validarEMail($usuario) == false){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email inválido"));
		$err	= 1;
	}
}else{
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Email deve ser preenchido!"));
	$err	= 1;
}

/** Perfil **/
if (!isset($codPerfil) || empty($codPerfil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Perfil deve ser informado!"));
	$err	= 1;
}

/** Organização **/
if (!isset($codOrganizacao) || empty($codOrganizacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Organização deve ser informada!"));
	$err	= 1;
}

if (isset($codLogradouro) && (!empty($codLogradouro))){
	if (!isset($cep) || (empty($cep))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CEP deve ser preenchido!"));
		$err	= 1;
	}

	if (!isset($descLogradouro) || (empty($descLogradouro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Logradouro deve ser preenchido!"));
		$err	= 1;
	}

	if (!isset($bairro) || (empty($bairro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Bairro deve ser preenchido!"));
		$err	= 1;
	}

	//Verificar o endereço informado é corresponte a base dos correios
	if (isset($endCorreto) && (!empty($endCorreto))) {
		$endCorreto	= 1;
	}else{
		$oLogradouro	= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));

		if (($oLogradouro->getDescricao() != $descLogradouro) || ($oLogradouro->getCodBairro()->getDescricao() != $bairro)){
			$endCorreto	= 0;
		}else{
			$endCorreto	= 1;
		}
	}
}else{
	$endCorreto = null; //Se não houver o codLogradouro o indicador deve ser nulo
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {

	#################################################################################
	## Verificar se o usuário já existe
	#################################################################################
	$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $usuario));
	
	
	if (!$oUsuario) {
		$novoUsuario	= true;
		
		#################################################################################
		## Criar o usuário com o status pendente
		#################################################################################
		$oUsuario			= new \Entidades\ZgsegUsuario();
		$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
		$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
		$oSexo				= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
		
		$oUsuario->setUsuario($usuario);
		$oUsuario->setNome($nome);
		$oUsuario->setApelido($apelido);
		$oUsuario->setCpf($cpf);
		$oUsuario->setSexo($oSexo);
		$oUsuario->setCodStatus($oStatus);
		
		$oUsuario->setCodLogradouro($oCodLogradouro);
		$oUsuario->setIndEndCorreto($endCorreto);
		$oUsuario->setCep($cep);
		$oUsuario->setEndereco($descLogradouro);
		$oUsuario->setBairro($bairro);
		$oUsuario->setNumero($numero);
		$oUsuario->setComplemento($complemento);
		
		$em->persist($oUsuario);
		
	}else{
		$novoUsuario	= false;
		#################################################################################
		## Verificar se o usuário já está associado a organização
		#################################################################################
		$oUsuOrg	= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));
		
		if ($oUsuOrg) {
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email já associado a organização"));
			die ('1'.\Zage\App\Util::encodeUrl('||'));
		}
	}
	
	
	#################################################################################
	## Usuário - Organização
	#################################################################################
	$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	
	$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
	
	$oUsuarioOrg->setCodUsuario($oUsuario);
	$oUsuarioOrg->setCodOrganizacao($oOrg);
	$oUsuarioOrg->setCodPerfil($oPerfil);
	$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);
	
	$em->persist($oUsuarioOrg);
	
	#################################################################################
	## Telefones
	#################################################################################		
	/***** Criação *****/
	for ($i = 0; $i < sizeof($codTelefone); $i++) {
		$infoTel		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findOneBy(array('codigo' => $codTelefone[$i] , 'codUsuario' => $oUsuario->getCodigo()));
	
		if (!$infoTel) {
			$infoTel		= new \Entidades\ZgsegUsuarioTelefone();
		}
			
		if ($infoTel->getCodTipoTelefone() !== $codTipoTel[$i] || $infoTel->getTelefone() !== $telefone[$i]) {
	
			$oTipoTel	= $em->getRepository('Entidades\ZgappTelefoneTipo')->findOneBy(array('codigo' => $codTipoTel[$i]));
	
			$infoTel->setCodUsuario($oUsuario);
			$infoTel->setCodTipoTelefone($oTipoTel);
			$infoTel->setTelefone($telefone[$i]);
				
			$em->persist($infoTel);
		}
	}
	
	#################################################################################
	## Cria o convite
	#################################################################################
	$convite		= new \Zage\Seg\Convite();
	$convite->setCodOrganizacaoOrigem($oOrg);
	$convite->setCodUsuarioDestino($oUsuario);
	$convite->salvar();

	
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
	## Carregando o template html do email
	#################################################################################
	$tpl		= new \Zage\App\Template();
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuarioOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
	if ($novoUsuario) {
		$tpl->load(MOD_PATH . "/Seg/html/usuarioCadEmail.html");
		$assunto			= "Cadatro de usuário";
		$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
	}else{
		$tpl->load(MOD_PATH . "/Seg/html/usuarioCadAssocEmail.html");
		$assunto			= "Associação a empresa";
		$confirmUrl			= ROOT_URL . "/Seg//u02.php?cid=".$cid;
	}
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('ID'					,$id);
	$tpl->set('CONFIRM_URL'			,$confirmUrl);
	$tpl->set('ASSUNTO'				,$assunto);
	
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
	$mail->addTo($usuario);
	
	#################################################################################
	## Salvar as informações e enviar o e-mail
	#################################################################################
	try {
		$transport->send($mail);
	} catch (Exception $e) {
		$log->debug("Erro ao enviar o e-mail:". $e->getTraceAsString());
		throw new \Exception("Erro ao enviar o email, a mensagem foi para o log dos administradores, entre em contato para mais detalhes !!!");
	}
	

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

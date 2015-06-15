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
/** Organização **/
if (!isset($codOrganizacao) || empty($codOrganizacao)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Organização deve ser informada!"));
	$err	= 1;
}

/** Usuário (email) **/
if (!isset($usuario) || empty($usuario)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O email deve ser preenchido!"));
	$err	= 1;
}elseif (strlen($usuario) > 200){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O email não deve conter mais de 200 caracteres!"));
	$err	= 1;
}

if(\Zage\App\Util::validarEMail($usuario) == false){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Email inválido"));
	$err	= 1;
}else{
	$oUsuario = $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('usuario' => $usuario));
	if($oUsuario != null && ($oUsuario->getCodigo() != $codUsuario)){
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um usuário cadastrado com este EMAIL! Por favor,  verifique os dados informados."));
		$err	= 1;
	}
}

/** CPF **/
$valCgc			= new \Zage\App\Validador\Cpf();
if (empty($cpf)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CPF deve ser preenchido!"));
	$err	= 1;
}else{
	if ($valCgc->isValid($cpf) == false) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("CPF inválido!"));
		$err	= 1;
	}else{
		$oUsuario	= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('cpf' => cpf));
		if($oUsuario != null && ($oUsuario->getCodigo() != $codUsuario)){
			$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Já existe um usuário cadastrado com este CPF! Por favor,  verifique os dados informados."));
			$err	= 1;
		}
	}
}

/** Nome **/
if (!isset($nome) || empty($nome)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome deve ser preenchido!"));
	$err	= 1;
}elseif (strlen($nome) < 5){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("Nome muito pequeno, informe o nome completo!"));
	$err	= 1;
}elseif (strlen($nome) > 100){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome não deve conter mais de 100 caracteres!"));
	$err	= 1;
}

/** Apelido **/
if (!isset($apelido) || empty($apelido)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O apelido deve ser preenchido!"));
	$err	= 1;
}elseif (strlen($apelido) > 60){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O apelido não deve conter mais de 60 caracteres!"));
	$err	= 1;
}

/** Perfil **/
if (!isset($codPerfil) || empty($codPerfil)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O perfil deve ser preenchido!"));
	$err	= 1;
}

/** Sexo **/
if (!isset($sexo) || empty($sexo)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O sexo deve ser preenchido!"));
	$err	= 1;
}

/** ENDEREÇO **/
if (isset($codLogradouro) && (!empty($codLogradouro))){
	
	/******* CEP *********/
	if (!isset($cep) || (empty($cep))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CEP deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($cep)) && (strlen($cep) > 8)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O CEP não deve conter mais de 8 caracteres!"));
		$err	= 1;
	}

	/******* LOGRADOURO *********/
	if (!isset($descLogradouro) || (empty($descLogradouro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Logradouro deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($descLogradouro)) && (strlen($descLogradouro) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O logradouro não deve conter mais de 100 caracteres!"));
		$err	= 1;
	}
	
	/******* BAIRRO *********/
	if (!isset($bairro) || (empty($bairro))) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O Bairro deve ser preenchido!"));
		$err	= 1;
	}elseif ((!empty($bairro)) && (strlen($bairro) > 60)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O bairro não deve conter mais de 60 caracteres!"));
		$err	= 1;
	}
	
	/******* NÚMERO *********/
	if ((!empty($numero)) && (strlen($numero) > 10)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O número não deve conter mais de 10 caracteres!"));
		$err	= 1;
	}
	
	/******* COMPLEMENTO *********/
	if ((!empty($complemento)) && (strlen($complemento) > 100)) {
		$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O complemento do endereço não deve conter mais de 100 caracteres!"));
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
		$enviarEmail	= true;
		
		#################################################################################
		## Criar o usuário com o status pendente
		#################################################################################
		$oUsuario			= new \Entidades\ZgsegUsuario();
		$oStatus			= $em->getRepository('Entidades\ZgsegUsuarioStatusTipo')->findOneBy(array('codigo' => 'P'));
		
		#################################################################################
		## Ajustar alguns campos para um novo usuário
		## Não alterar o status caso o usuário já exista
		#################################################################################
		$oUsuario->setCodStatus($oStatus);
		$oUsuario->setUsuario($usuario);
		
		
	}else{
		$novoUsuario	= false;
		
		if ($oUsuario->getCodStatus()->getCodigo() == "A") {
			$enviarEmail	= false;
		}else{
			$enviarEmail	= true;
		}
	}
	
	#################################################################################
	## Resgatar os objetos de relacionamento
	#################################################################################
	$oCodLogradouro		= $em->getRepository('Entidades\ZgadmLogradouro')->findOneBy(array('codigo' => $codLogradouro));
	$oSexo				= $em->getRepository('Entidades\ZgsegSexoTipo')->findOneBy(array('codigo' => $sexo));
	
	#################################################################################
	## Salvar os dados do usuário
	#################################################################################
	$oUsuario->setNome($nome);
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
	
	#################################################################################
	## Colocar na fila para execução
	#################################################################################
	$em->persist($oUsuario);
	
	#################################################################################
	## Telefones / Contato
	#################################################################################
	$telefones		= $em->getRepository('Entidades\ZgsegUsuarioTelefone')->findBy(array('codUsuario' => $oUsuario->getCodigo()));
	
	#################################################################################
	## Exclusão
	#################################################################################
	for ($i = 0; $i < sizeof($telefones); $i++) {
		if (!in_array($telefones[$i]->getCodigo(), $codTelefone)) {
			try {
				$em->remove($telefones[$i]);
			} catch (\Exception $e) {
				$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,"Não foi possível excluir o telefone: ".$telefones[$i]->getTelefone()." Erro: ".$e->getMessage());
				echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
				exit;
			}
		}
	
	}
	
	#################################################################################
	## Criação / Alteração
	#################################################################################
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
	## Verificar se o usuário já está associado a organização
	#################################################################################
	if ($novoUsuario) {
		$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
		$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
	}else{
		$oUsuarioOrg		= $em->getRepository('Entidades\ZgsegUsuarioOrganizacao')->findOneBy(array('codUsuario' => $oUsuario->getCodigo(), 'codOrganizacao' => $codOrganizacao));
		if (!$oUsuarioOrg)	{
			$oUsuarioOrg		= new \Entidades\ZgsegUsuarioOrganizacao();
			$oUsuarioOrgStatus  = $em->getRepository('Entidades\ZgsegUsuarioOrganizacaoStatus')->findOneBy(array('codigo' => 'P'));
		}else{
			$oUsuarioOrgStatus  = $oUsuarioOrg->getCodStatus();
		}
	}
	
	#################################################################################
	## Usuário - Organização
	#################################################################################
	$oOrg				= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	$oPerfil			= $em->getRepository('Entidades\ZgsegPerfil')->findOneBy(array('codigo' => $codPerfil));
	
	$oUsuarioOrg->setCodUsuario($oUsuario);
	$oUsuarioOrg->setCodOrganizacao($oOrg);
	$oUsuarioOrg->setCodPerfil($oPerfil);
	$oUsuarioOrg->setCodStatus($oUsuarioOrgStatus);
	
	#################################################################################
	## Colocar na fila para execução
	#################################################################################
	$em->persist($oUsuarioOrg);
	
	#################################################################################
	## Cria o convite
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
	
	if ($enviarEmail) {
	
		#################################################################################
		## Carregando o template html do email
		#################################################################################
		$tpl		= new \Zage\App\Template();
		$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuarioOrg->getCodigo().'&_cdu02='.$oUsuario->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
		if ($novoUsuario) {
			$tpl->load(MOD_PATH . "/Seg/html/usuarioCadEmail.html");
			$assunto			= "Cadatro de usuário";
			$nome				= $oUsuario->getNome();
			$texto				= "Sua conta já está criada, mas ainda precisa ser confirmada. Para isso, clique no link abaixo:";
			$confirmUrl			= ROOT_URL . "/Seg/u01.php?cid=".$cid;
		}else{
			$tpl->load(MOD_PATH . "/Seg/html/usuarioCadAssocEmail.html");
			$assunto			= "Associação a empresa";
			$confirmUrl			= ROOT_URL . "/Seg/u02.php?cid=".$cid;
		}
		
		#################################################################################
		## Define os valores das variáveis
		#################################################################################
		$tpl->set('ID'					,$id);
		$tpl->set('CONFIRM_URL'			,$confirmUrl);
		$tpl->set('ASSUNTO'				,$assunto);
		$tpl->set('NOME'				,$nome);
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
	}

} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->getCodigo());

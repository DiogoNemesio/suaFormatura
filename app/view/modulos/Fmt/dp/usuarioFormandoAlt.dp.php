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
	
	$oOrg	= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
	
	/***********************
	* Salavar cliente
	***********************/
	$oCliente = $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('cgc' => $oUsuario->_getUsuario()->getCpf() , 'codOrganizacao' => $codOrganizacao));
	
	if(!$oCliente){
		$oCliente = new \Entidades\ZgfinPessoa();
		$oCliente->setDataCadastro(new DateTime(now));
		$oCliente->setObservacao('Importado do cadastro de formando.');
	}
	
	$clienteTipo = $em->getRepository('Entidades\ZgfinPessoaTipo')->findOneBy(array('codigo' => F));
	
	$oCliente->setCodOrganizacao($oOrg);
	$oCliente->setNome($oUsuario->_getUsuario()->getNome());
	$oCliente->setCgc($oUsuario->_getUsuario()->getCpf());
	$oCliente->setEmail($oUsuario->_getUsuario()->getUsuario());
	$oCliente->setCodTipoPessoa($clienteTipo);
	$oCliente->setIndContribuinte(0);
	$oCliente->setIndCliente(1);
	$oCliente->setIndFornecedor(0);
	$oCliente->setIndTransportadora(0);
	$oCliente->setIndEstrangeiro(0);
	$oCliente->setIndAtivo(1);
	$oCliente->setCodSexo($oSexo);
	
	$em->persist($oCliente);
	
	//ENDEREÇO CLIENTE
	if ($endObrigatorio == true){
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
	 * Cria o convite 
	 ***********************/
	if ($oUsuario->_getEnviarEmail() == true) {
		$oConviteStatus = $em->getRepository('Entidades\ZgsegConviteStatus')->findOneBy(array('codigo' => A));
		$convite		= new \Zage\Seg\Convite();
		$convite->setCodOrganizacaoOrigem($oOrg);
		$convite->setCodUsuarioDestino($oUsuario->_getUsuario());
		$convite->setCodStatus($oConviteStatus);
		$convite->salvar();
	}
	$em->flush();
	$em->clear();
	/********** Salvar as informações ******
	try {
		$em->flush();
		$em->clear();
	} catch (Exception $e) {
		$log->debug("Erro ao salvar o usuário:". $e->getTraceAsString());
		throw new \Exception("Erro ao salvar o usuário, uma mensagem de depuração foi salva em log, entre em contato com os administradores do sistema !!!");
	}	
***/
} catch (\Exception $e) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$e->getMessage());
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($e->getMessage()));
	exit;
}

if ($oUsuario->_getEnviarEmail() == true) {
	
	#################################################################################
	## Carregando o template html do email
	#################################################################################
	$tpl		= new \Zage\App\Template();
	$cid 		= \Zage\App\Util::encodeUrl('_cdu01='.$oUsuario->_getUsuOrg()->getCodigo().'&_cdu02='.$oUsuario->_getUsuario()->getCodigo().'&_cdu03='.$codOrganizacao.'&_cdu04='.$convite->_getCodigo().'&_cdsenha='.$convite->getSenha());
	if ($oUsuario->_getUsuario()->getCodStatus()->getCodigo() == P) {
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


$system->criaAviso(\Zage\App\Aviso\Tipo::INFO,$tr->trans("Informações salvas com sucesso"));
echo '0'.\Zage\App\Util::encodeUrl('|'.$oUsuario->_getCodigo());

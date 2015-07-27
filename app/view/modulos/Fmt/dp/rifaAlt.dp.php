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
if (isset($_POST['codRifa'])) 			$codRifa		= \Zage\App\Util::antiInjection($_POST['codRifa']);
if (isset($_POST['nome'])) 				$nome			= \Zage\App\Util::antiInjection($_POST['nome']);
if (isset($_POST['premio']))			$premio			= \Zage\App\Util::antiInjection($_POST['premio']);
if (isset($_POST['custo']))				$custo			= \Zage\App\Util::antiInjection($_POST['custo']);
if (isset($_POST['valor'])) 			$valor			= \Zage\App\Util::antiInjection($_POST['valor']);
if (isset($_POST['qtdeObrigatorio']))	$qtdeObri		= \Zage\App\Util::antiInjection($_POST['qtdeObrigatorio']);

if (isset($_POST['localSorteio']))		$local			= \Zage\App\Util::antiInjection($_POST['localSorteio']);
if (isset($_POST['dataSorteio']))		$data			= \Zage\App\Util::antiInjection($_POST['dataSorteio']);

if (isset($_POST['numUsuAtivo']))		$numUsuAtivo	= \Zage\App\Util::antiInjection($_POST['numUsuAtivo']);

#################################################################################
## Limpar a variável de erro
#################################################################################
$err	= false;

#################################################################################
## Fazer validação dos campos
#################################################################################
/******* Verificar se existe formandos ativos *********/
if ($numUsuAtivo == 0){
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("A rifa não pode ser criada pois não existe formando ativo na formatura!"));
	$err	= 1;
}

/******* Nome *********/
if (!isset($nome) || (empty($nome))) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome da rifa deve ser preenchido!"));
	$err	= 1;
}elseif ((!empty($nome)) && (strlen($nome) > 100)) {
	$system->criaAviso(\Zage\App\Aviso\Tipo::ERRO,$tr->trans("O nome da rifa não deve conter mais de 100 caracteres!"));
	$err	= 1;
}

if ($err != null) {
	echo '1'.\Zage\App\Util::encodeUrl('||'.htmlentities($err));
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
try {
	
	/***********************
	 * Verificar se a rifa já existe
	 ***********************/
	if (isset($codRifa) && (!empty($codRifa))){
 		$oRifa	= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
 		if (!$oRifa) {
 			$oRifa	= new \Entidades\ZgadmOrganizacao();
 			$oRifa->setDataCadastro(new \DateTime("now"));
 		}
 	}else{
 		$oRifa	= new \Entidades\ZgfmtRifa();
 		$oRifa->setDataCadastro(new \DateTime("now"));
 	}
	
	/*********************** 
	 * Resgatar os objetos de relacionamento 
	 ***********************/
	$oCodOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
	
	/*********************** 
	 * Salvar os dados da rifa
	 ***********************/
	$oRifa->setCodOrganizacao($oCodOrg);
	$oRifa->setNome($nome);
	$oRifa->setPremio($premio);
	$oRifa->setCpf($cpf);
	$oRifa->setQtdeObrigatorio($qtdeObri);
	$oRifa->setValorUnitario($valor);
	$oRifa->setCep($cep);
	$oRifa->setDataSorteio($data);
	$oRifa->setLocalSorteio($local);
	$oRifa->setIndSorteioEletronico($indSorteioEletronico);
	
	
	
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

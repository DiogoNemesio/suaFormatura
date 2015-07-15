<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'includeNoAuth.php');
}else{
	define('DOC_ROOT', realpath(dirname( __FILE__ ) . '/../') . "/" );
	include_once(DOC_ROOT . 'includeNoAuth.php');
}

use \Zend\Mail;
use \Zend\Mail\Message;
use \Zend\Mime\Message as MimeMessage;
use \Zend\Mime\Part as MimePart;
Use \Zend\Mime;

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log,$db;

#################################################################################
## Lista as notificações não enviadas
#################################################################################
$notificacoes	= \Zage\App\Notificacao::listaNaoEnviadas();

#################################################################################
## Loop para envio das notificações
#################################################################################
for ($i = 0; $i < sizeof($notificacoes); $i++) {

	#################################################################################
	## Monta a mensagem
	#################################################################################
	$codTipoMens		= $notificacoes[$i]->getCodTipoMensagem()->getCodigo();
	$assunto			= $notificacoes[$i]->getAssunto();
	$data				= $notificacoes[$i]->getData()->format($system->config["data"]["datetimeFormat"]);
	
	if ($codTipoMens	== "TX") {
		$mensagem		= $notificacoes[$i]->getMensagem();
	}elseif ($codTipoMens	== "H") {
		$mensagem		= $notificacoes[$i]->getMensagem();
	}elseif ($codTipoMens	== "TP") {
	
		#################################################################################
		## Verificar se o template foi informado
		#################################################################################
		if (!$notificacoes[$i]->getCodTemplate()) continue;
		
		#################################################################################
		## Resgata as informações do template
		#################################################################################
		$template		= $notificacoes[$i]->getCodTemplate();
		
		#################################################################################
		## Verificar se o template existe
		#################################################################################
		if (!file_exists(TPL_PATH . '/' . $template->getCaminho())) continue;
		
		#################################################################################
		## Carregando o template html
		#################################################################################
		$tpl	= new \Zage\App\Template();
		$tpl->load(TPL_PATH . '/' . $template->getCaminho());
		
		#################################################################################
		## Atribui as variáveis do template
		#################################################################################
		$variaveis		= $em->getRepository('\Entidades\ZgappNotificacaoVariavel')->findBy(array('codNotificacao' => $notificacoes[$i]->getCodigo()));
		for ($v = 0; $v < sizeof($variaveis); $v++) {
			$tpl->set($variaveis[$v]->getVariavel(), $variaveis[$v]->getValor());
		}
		
		#################################################################################
		## Por fim exibir a página HTML
		#################################################################################
		$mensagem	= $tpl->getHtml();
		
	
	}else{
		continue;
	}
	

	#################################################################################
	## Criar os objeto do email ,transporte e validador
	#################################################################################
	$mail 			= \Zage\App\Mail::getMail();
	$transport 		= \Zage\App\Mail::getTransport();
	$validator 		= new \Zend\Validator\EmailAddress();
	$htmlMail 		= new MimePart($mensagem);
	$htmlMail->type = "text/html";
	$body 			= new MimeMessage();
		
	#################################################################################
	## Definir o conteúdo do e-mail
	#################################################################################
	$body->setParts(array($htmlMail));
	$mail->setBody($body);
	$mail->setSubject("<ZageNotificação> ".$assunto);
	
	#################################################################################
	## Controlar a quantidade de emails a enviar
	#################################################################################
	$numEmails	= 0;
	$logDest	= array();
	
	#################################################################################
	## Resgata a lista de usuários que receberão a notificação
	#################################################################################
	$usuarios	= $em->getRepository('\Entidades\ZgappNotificacaoUsuario')->findBy(array('codNotificacao' => $notificacoes[$i]->getCodigo()));
	for ($j = 0; $j < sizeof($usuarios); $j++) {
		
		
		#################################################################################
		## Verifica se é para enviar e-mail
		#################################################################################
		if ($notificacoes[$i]->getIndViaEmail())	{
			
			#################################################################################
			## Cria o log de envio
			#################################################################################
			$mailLog		= new \Entidades\ZgappNotificacaoLog();
			$oFormaEnvio	= $em->getReference('\Entidades\ZgappNotificacaoFormaEnvio', "E");
			$mailLog->setCodFormaEnvio($oFormaEnvio);
			$mailLog->setCodNotificacao($notificacoes[$i]);
				
			#################################################################################
			## Associa os destinatários
			#################################################################################
			$mailLogDest	= new \Entidades\ZgappNotificacaoLogDest();
			$oUsu			= $em->getReference('\Entidades\ZgsegUsuario', $usuarios[$j]->getCodUsuario()->getCodigo());
			$mailLogDest->setCodLog($mailLog);
			$mailLogDest->setCodDestinatario($oUsu);
			$em->persist($mailLogDest);
				
			#################################################################################
			## Definir os destinatários
			#################################################################################
			$mail->addBcc($usuarios[$j]->getCodUsuario()->getUsuario());
			$numEmails++;
			
		}	
		
		#################################################################################
		## Verifica se é para enviar via whatsapp
		#################################################################################
		if ($notificacoes[$i]->getIndViaWa())	{
			
			#################################################################################
			## Não enviar templates via whatsapp
			#################################################################################
			if ($notificacoes[$i]->getCodTipoMensagem()->getCodigo() == "TP") {
				continue;
			}
			
		}
	}
	
	
	#################################################################################
	## Salvar as informações e enviar o e-mail
	#################################################################################
	if ($numEmails > 0) {
		try {

			$mailLog->setDataEnvio(new \DateTime("now"));
			$mailLog->setIndErro(0);
			$em->persist($mailLog);
			
			$transport->send($mail);
			
		} catch (Exception $e) {
			$log->err("Erro ao enviar o e-mail:". $e->getTraceAsString());
			$mailLog->setErro($e->getTraceAsString());
			$mailLog->setIndErro(1);
			$em->persist($mailLog);
		}
	}
	
	#################################################################################
	## Alterar a flag de processada
	#################################################################################
	$notificacoes[$i]->setIndProcessada(1);
	$em->persist($notificacoes[$i]);
	
	
}

#################################################################################
## Salva as modificações
#################################################################################
try {
	$em->flush();
	$em->clear();
	
} catch (Exception $e) {
	$log->err("Erro ao salvar as modificações no envio das mensagens: ".$e->getMessage());
}


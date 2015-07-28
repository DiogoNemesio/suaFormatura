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
global $em,$system,$tr,$log;

#################################################################################
## Lista as notificações não enviadas
#################################################################################
$notificacoes	= \Zage\App\Notificacao::listaNaoEnviadas();

#################################################################################
## Inicializa o array de Chips, que serão usados para enviar as mensagens
#################################################################################
$chips 			= array();

#################################################################################
## Loop para envio das notificações
#################################################################################
for ($i = 0; $i < sizeof($notificacoes); $i++) {

	$log->debug("Processar notificação: ".$notificacoes[$i]->getAssunto());
	
	#################################################################################
	## Monta a mensagem
	#################################################################################
	$codTipoMens		= $notificacoes[$i]->getCodTipoMensagem()->getCodigo();
	$codTipoDest		= $notificacoes[$i]->getCodTipoDestinatario()->getCodigo();
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
	## Criar os objeto de acordo com a via da notificação
	#################################################################################
	if ($notificacoes[$i]->getIndViaEmail())	{
		
		#################################################################################
		## Criar os objeto do email ,transporte e validador
		#################################################################################
		$mail 			= \Zage\App\Mail::getMail();
		$transport 		= \Zage\App\Mail::getTransport();
		$validator 		= new \Zend\Validator\EmailAddress();
		$htmlMail 		= new MimePart($mensagem);
		$htmlMail->type = "text/html";
		$body 			= new MimeMessage();
		$bodyArray		= array();
		$bodyArray[]	= $htmlMail;
			
		
		#################################################################################
		## Colocar os anexos, caso existam
		#################################################################################
		$anexos		= $em->getRepository('\Entidades\ZgappNotificacaoAnexo')->findBy(array('codNotificacao' => $notificacoes[$i]->getCodigo()));
		for ($a = 0; $a < sizeof($anexos); $a++) {
			$log->debug("Anexando o arquivo: ".$anexos[$a]->getNome());
			$attachment 				= new Mime\Part($anexos[$a]->getAnexo());
			$attachment->type 			= 'application/octet-stream';
			$attachment->filename 		= $anexos[$a]->getNome();
			$attachment->disposition 	= Mime\Mime::DISPOSITION_ATTACHMENT;
			$attachment->encoding 		= Mime\Mime::ENCODING_BASE64;
			$bodyArray[]	= $attachment;
		}
		
		#################################################################################
		## Definir o conteúdo do e-mail
		#################################################################################
		$body->setParts($bodyArray);
		$mail->setBody($body);
		$mail->setSubject("<ZageNotificação> ".$assunto);
		
		#################################################################################
		## Controlar a quantidade de emails a enviar
		#################################################################################
		$numEmails	= 0;
	}
		
	$log->debug("Listar usuarios da notificação: ".$notificacoes[$i]->getAssunto());

	#################################################################################
	## Verificar o tipo de destinatário 
	#################################################################################
	if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
		$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoPessoa')->findBy(array('codNotificacao' => $notificacoes[$i]->getCodigo()));
	}else{
		$destinatarios	= $em->getRepository('\Entidades\ZgappNotificacaoUsuario')->findBy(array('codNotificacao' => $notificacoes[$i]->getCodigo()));		
	}
	
	#################################################################################
	## Resgata a lista de usuários/Pessoas que receberão a notificação
	#################################################################################
	for ($j = 0; $j < sizeof($destinatarios); $j++) {
		
		if ($codTipoDest	== \Zage\App\Notificacao::TIPO_DEST_PESSOA) {
			$nomeDest		= $destinatarios[$j]->getCodPessoa()->getNome();
			$codDest		= $destinatarios[$j]->getCodPessoa()->getCodigo();
			$emailDest		= $destinatarios[$j]->getCodPessoa()->getEmail(); 
		}else{
			$nomeDest		= $destinatarios[$j]->getCodUsuario()->getNome();
			$codDest		= $destinatarios[$j]->getCodUsuario()->getCodigo();
			$emailDest		= $destinatarios[$j]->getCodUsuario()->getUsuario();
		}
		
		$log->debug("Usuario/Pessoa que será notificado[a]: ".$nomeDest);
		
		#################################################################################
		## Verifica se é para enviar e-mail
		#################################################################################
		if ($notificacoes[$i]->getIndViaEmail())	{
		
			#################################################################################
			## Valida o e-mail
			#################################################################################
			if (!$validator->isValid($emailDest)) {
				continue;
			}
			
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
			$oUsu			= $em->getReference('\Entidades\ZgsegUsuario', $codDest);
			$mailLogDest->setCodLog($mailLog);
			$mailLogDest->setCodDestinatario($oUsu);
			$em->persist($mailLogDest);
				
			#################################################################################
			## Definir os destinatários
			#################################################################################
			if (sizeof($destinatarios) > 1) {
				$mail->addBcc($emailDest);
				if ($notificacoes[$i]->getEmail() && ($validator->isValid($notificacoes[$i]->getEmail())) ) $mail->addBcc($notificacoes[$i]->getEmail());
			}else{
				$mail->addTo($emailDest);
				if ($notificacoes[$i]->getEmail() && ($validator->isValid($notificacoes[$i]->getEmail())) ) $mail->addCc($notificacoes[$i]->getEmail());
			}
			
			$numEmails++;
			
		}	
		
		#################################################################################
		## Verifica se é para enviar via whatsapp
		#################################################################################
		if ($notificacoes[$i]->getIndViaWa())	{
			
			$log->debug("Envia wa para notificacao: ".$notificacoes[$i]->getAssunto());
			
			#################################################################################
			## Não enviar templates via whatsapp
			#################################################################################
			if ($notificacoes[$i]->getCodTipoMensagem()->getCodigo() == "TP") {
				continue;
			}
			
			
			#################################################################################
			## Busca o Chip que a mensagem será enviada
			#################################################################################
			$c	= \Zage\Wap\Chip::buscaChipUsuario($codDest);

			#################################################################################
			## Caso não tenha chips disponíveis, não tentar enviar a mensagem
			#################################################################################
			if (!$c) continue;
			
			$log->debug("Chip selecionado: ".$c->getLogin());

			#################################################################################
			## Instancia a classe para envio
			#################################################################################
			$chip		= new \Zage\Wap\Chip();
			$chip->_setCodigo($c->getCodigo());
			
			#################################################################################
			## Converte o número do celular para o formato do whatsapp
			#################################################################################
			$celulares	= \Zage\Wap\Chip::buscaNumeroComWa($codDest);
			if (!$celulares || sizeof($celulares) ==  0)	continue;
			
			try {
				
				if (!isset($chips[$c->getCodigo()])) {
					$log->debug("Tentando conexão com WA!!! ");
					$chip->conectar();
					$log->debug("Conexão ao wa feita com sucesso !!! ");
					
					$chips[$c->getCodigo()]	= $chip;
					
				}
				
				for ($n = 0; $n < sizeof($celulares); $n++) {
					$log->debug("Concertendo o número : ".$celulares[$n]->getTelefone());
					//$waNumber	= $chip->_convertCellToWaNumber($celulares[$n]->getTelefone());
					$waNumber	= $celulares[$n]->getWaLogin();
					$log->debug("Enviando wa para o número: ".$waNumber);
					$chips[$c->getCodigo()]->w->sendMessage($waNumber, $mensagem);
				}
				
					
			} catch (Exception $e) {
				$log->err("Mensagem wa não enviada, por problema no chip: ".$chip->getLogin()." -> ". $e->getMessage());
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


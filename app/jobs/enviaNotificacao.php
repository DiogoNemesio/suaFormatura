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
## Loop para envio das notificações
#################################################################################
for ($i = 0; $i < sizeof($notificacoes); $i++) {

	$log->debug("Processar notificação: ".$notificacoes[$i]->getAssunto());

	#################################################################################
	## controle de processamento
	#################################################################################
	$okE		= 1;
	$okW		= 1;
	
	#################################################################################
	## Chama as rotinas de envio
	#################################################################################
	if ($notificacoes[$i]->getIndViaEmail()) 		$okE	= \Zage\App\Notificacao::_notificaEmail($notificacoes[$i]->getCodigo());
	if ($notificacoes[$i]->getIndViaWa()) 			$okW	= \Zage\App\Notificacao::_notificaWa($notificacoes[$i]->getCodigo());
	
	$ok		= ($okE == 1 && $okW == 1) ? 1 : 0;
	
	#################################################################################
	## Controle de processamento
	#################################################################################
	$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $notificacoes[$i]->getCodigo())); 
	$indProcessada		= ($ok == 1) ? 1 : 0;
	$notificacao->setIndProcessada($indProcessada);
	$em->persist($notificacao);
	$em->flush();
	$em->detach($notificacao);
}

<?php

namespace Zage\App;

/**
 * Gerenciar Erros
 *
 * @package \Zage\App\Erro
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Erro {
	
	/**
	 * Construtor privado
	 */
	private function __construct() {
	}
	
	/**
	 * Essa função irá mostrar uma mensagem de erro padrão e fazer log da mnesagem original
	 * 
	 * @param string $errstr        	
	 */
	public static function halt($mensagem = null,$arquivo = null, $linha = null) {
		/**
		 * Definindo Variáveis globais
		 */
		global $system,$log,$tr;

		
		$msg1		= 'Tivemos problemas para processar sua solicitação';
		$msg2		= 'Mas já estamos trabalhando para solucionar';
		$msg3		= 'Entretanto, tente uma das alternativas abaixo';
		$msg4		= 'Tente novamente dentro de alguns instantes';
		$msg5		= 'Nos envie mais informações a respeito do problema';
		$msgVoltar	= 'Voltar';
		$msgInicio	= 'Início';
		$msgMen		= 'Mensagem';
		$msgFile	= 'Arquivo';
		$msgLine	= 'Linha';
		
		
		if (isset($tr) && is_object($tr)) {
			$msg1		= $tr->trans($msg1);
			$msg2		= $tr->trans($msg2);
			$msg3		= $tr->trans($msg3);
			$msg4		= $tr->trans($msg4);
			$msg5		= $tr->trans($msg5);
			$msgVoltar	= $tr->trans($msgVoltar);
			$msgInicio	= $tr->trans($msgInicio);
			$msgMen		= $tr->trans($msgMen);
			$msgFile	= $tr->trans($msgFile);
			$msgLine	= $tr->trans($msgLine);
		}
		
		$tpl	= new \Zage\App\Template();
		$tpl->load(HTML_PATH . "/Erro.html");
		$tpl->set('MSG_1',$msg1);
		$tpl->set('MSG_2',$msg2);
		$tpl->set('MSG_3',$msg3);
		$tpl->set('MSG_4',$msg4);
		$tpl->set('MSG_5',$msg5);
		$tpl->set('MSG_VOLTAR',$msgVoltar);
		$tpl->set('MSG_INICIO',$msgInicio);
		
		if (isset($system->config["debug"]) && $system->config["debug"] == 1) {
			$mMsg		= $msgMen . ":&nbsp;".$mensagem;
			$mArquivo	= ($arquivo == null) ? $arquivo : $msgFile.": ".$arquivo;
			$mLinha		= ($linha == null) ? $linha : $msgLine.": ".$linha;
		}else{
			$mMsg		= null;
			$mArquivo	= null;
			$mLinha		= null;
		}
		
		$tpl->set('MSG_ARQUIVO',$mArquivo);
		$tpl->set('MSG_LINHA',$mLinha);
		$tpl->set('MSG_ERRO',$mMsg);

		
		if (isset ( $system ) && (is_object ( $system ))) {
			$log->err( $mensagem );
			
			/** Enviar email **/
		}
		$tpl->show ();
		exit;
	}


	/**
	 * Essa função irá mostrar uma mensagem de erro padrão e fazer log da mnesagem original
	 *
	 * @param string $errstr
	 */
	public static function externalHalt($mensagem = null,$arquivo = null, $linha = null) {
		/**
		 * Definindo Variáveis globais
		 */
		global $system,$log,$tr;
	
	
		$msg1		= 'Tivemos problemas para processar sua solicitação';
		$msg2		= 'Mas já estamos trabalhando para solucionar';
		$msg3		= 'Entretanto, tente uma das alternativas abaixo';
		$msg4		= 'Tente novamente dentro de alguns instantes';
		$msg5		= 'Nos envie mais informações a respeito do problema';
		$msgVoltar	= 'Voltar';
		$msgInicio	= 'Início';
		$msgMen		= 'Mensagem';
		$msgFile	= 'Arquivo';
		$msgLine	= 'Linha';
	
	
		if (isset($tr) && is_object($tr)) {
			$msg1		= $tr->trans($msg1);
			$msg2		= $tr->trans($msg2);
			$msg3		= $tr->trans($msg3);
			$msg4		= $tr->trans($msg4);
			$msg5		= $tr->trans($msg5);
			$msgVoltar	= $tr->trans($msgVoltar);
			$msgInicio	= $tr->trans($msgInicio);
			$msgMen		= $tr->trans($msgMen);
			$msgFile	= $tr->trans($msgFile);
			$msgLine	= $tr->trans($msgLine);
		}
	
		$tpl	= new \Zage\App\Template();
		$tpl->load(HTML_PATH . "/ErroExterno.html");
		$tpl->set('MSG_1',$msg1);
		$tpl->set('MSG_2',$msg2);
		$tpl->set('MSG_3',$msg3);
		$tpl->set('MSG_4',$msg4);
		$tpl->set('MSG_5',$msg5);
		$tpl->set('MSG_VOLTAR',$msgVoltar);
		$tpl->set('MSG_INICIO',$msgInicio);
	
		if (isset($system->config["debug"]) && $system->config["debug"] == 1) {
			$mMsg		= $msgMen . ":&nbsp;".$mensagem;
			$mArquivo	= ($arquivo == null) ? $arquivo : $msgFile.": ".$arquivo;
			$mLinha		= ($linha == null) ? $linha : $msgLine.": ".$linha;
		}else{
			$mMsg		= null;
			$mArquivo	= null;
			$mLinha		= null;
		}
	
		$tpl->set('MSG_ARQUIVO',$mArquivo);
		$tpl->set('MSG_LINHA',$mLinha);
		$tpl->set('MSG_ERRO',$mMsg);
	
	
		if (isset ( $system ) && (is_object ( $system ))) {
			$log->err( $mensagem );
				
			/** Enviar email **/
		}
		$tpl->show ();
		exit;
	}
	


}
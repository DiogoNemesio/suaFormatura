<?php
namespace Zage\App;

/**
 * Implementação de notificações
 * 
 * @package: Notificacao
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class Notificacao extends \Entidades\ZgappNotificacao {
	
	
	const TIPO_MENSAGEM_TEXTO 		= 'TX';
	const TIPO_MENSAGEM_HTML 		= 'H';
	const TIPO_MENSAGEM_TEMPLATE 	= 'TP';
	const TIPO_MENSAGEM_AUDIO 		= 'A';
	
	const TIPO_DEST_USUARIO 		= 'U';
	const TIPO_DEST_ORGANIZACAO		= 'O';
	
	
	/**
	 * Array de variáveis / valores
	 * @var array
	 */
	private $variaveis;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	
	/**
	 * Cria uma nova notificação
	 * @param string $tipoMensagem
	 * @param string $tipoDestinatario
	 */
	public function __construct($tipoMensagem,$tipoDestinatario) {
		
		#################################################################################
		## Valida o tipo de Mensagem
		#################################################################################
		switch ($tipoMensagem) {
			case \Zage\App\Notificacao::TIPO_MENSAGEM_AUDIO:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_HTML:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_TEMPLATE:
			case \Zage\App\Notificacao::TIPO_MENSAGEM_TEXTO:
				break;
			default:
				throw new \Exception('Tipo de mensagem desconhecido');
				break;
		}
		
		#################################################################################
		## Valida o tipo de Destinatário
		#################################################################################
		switch ($tipoMensagem) {
			case \Zage\App\Notificacao::TIPO_DEST_USUARIO:
			case \Zage\App\Notificacao::TIPO_DEST_ORGANIZACAO:
				break;
			default:
				throw new \Exception('Tipo de destinatário desconhecido');
				break;
		}
		
		
		
	}
	
	
	public function salva() {
		
	}
	
	public function adicionaVariavel() {
		
	}
	
}
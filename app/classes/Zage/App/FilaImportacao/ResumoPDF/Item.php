<?php
namespace Zage\App\FilaImportacao\ResumoPDF;

/**
 * @package: \Zage\App\FilaImportacao\ResumoPDF\Item
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar o Item do Resumo do PDF
 */

abstract class Item {

	/**
	 * Tipos de Item
	 */
	const TIPO_ERRO		= "Erro";
	const TIPO_AVISO	= "Aviso";
	
	/**
	 * Posição
	 * @var number
	 */
	private $posicao;
	
	/**
	 * Número da linha
	 *
	 * @var integer
	 */
	private $linha;
	
	/**
	 * Tipo do Registro
	 * @var string
	 */
	private $tipoRegistro;
	
	/**
	 * Mensagem de erro
	 * @var string
	 */
	private $mensagem;
	
	/**
	 * Tipo do Item
	 * @var string
	 */
	private $tipo;
	
	/**
	 * Número de vezes que a mensagem ocorreu
	 */
	private $quantidade;
	
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Inicializa a quantidade de mensagens
		#################################################################################
		$this->quantidade	= 1;
	}
	
	/**
	 * @return the $posicao
	 */
	public function getPosicao() {
		return $this->posicao;
	}

	/**
	 * @param number $posicao
	 */
	public function setPosicao($posicao) {
		$this->posicao = $posicao;
	}
	
	/**
	 * @return the $linha
	 */
	public function getLinha() {
		return $this->linha;
	}

	/**
	 * @param number $linha
	 */
	public function setLinha($linha) {
		$this->linha = $linha;
	}

	/**
	 * @return the $tipoRegistro
	 */
	public function getTipoRegistro() {
		return $this->tipoRegistro;
	}

	/**
	 * @param string $tipoRegistro
	 */
	public function setTipoRegistro($tipoRegistro) {
		$this->tipoRegistro = $tipoRegistro;
	}

	/**
	 * @return the $mensagem
	 */
	public function getMensagem() {
		return $this->mensagem;
	}

	/**
	 * @param string $mensagem
	 */
	public function setMensagem($mensagem) {
		$this->mensagem = $mensagem;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getTipo() {
		return $this->tipo;
	}
	
	/**
	 *
	 * @param string $tipo        	
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}
	
	/**
	 * @return the string
	 */
	public function getQuantidade() {
		return $this->quantidade;
	}
	
	/**
	 * Aumentar o número de vezes que a mensagem ocorreu
	 */
	public function aumentaQuantidade() {
		$this->quantidade++;
	}

}

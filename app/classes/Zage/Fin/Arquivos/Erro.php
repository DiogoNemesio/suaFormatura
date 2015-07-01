<?php
namespace Zage\Fin\Arquivos;

/**
 * @package: \Zage\Fin\Arquivos\Erro
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar os erros
 */

class Erro  {
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
	 * Ordem
	 * @var number
	 */
	private $ordem;
	
	/**
	 * Mensagem de erro
	 * @var string
	 */
	private $mensagem;
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
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
	 * @return the $ordem
	 */
	public function getOrdem() {
		return $this->ordem;
	}

	/**
	 * @param number $ordem
	 */
	public function setOrdem($ordem) {
		$this->ordem = $ordem;
	}


	
	
	
}

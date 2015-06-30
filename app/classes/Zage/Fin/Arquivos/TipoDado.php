<?php
namespace Zage\Fin\Arquivos;

/**
 * @package: Zage\Fin\Arquivos\TipoDado
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Classe abstrata de tipo de dados de um campo
 */

abstract class TipoDado extends \Entidades\ZgfinArquivoCampoFormato {

	/**
	 * Tamanho do Tipo de Dado
	 *
	 * @var string
	 */
	private $tamanho;
	
	/**
	 * Valor da Informação
	 *
	 * @var string
	 */
	private $valor;
	
	/**
	 * Mensagem de tipo de dado inválido
	 *
	 * @var string
	 */
	private $mensagemInvalido;
	
	/**
	 * Caracteres especiais 
	 *
	 * @var string
	 */
	private $ce = array("!","@","#","$","%","&","*","(",")","-","+","=","{","}","[","]","<",">",":","?",",",".",";","/","\\","'","_");
	
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
	}
	
	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param string $tamanho
	 */
	public function setTamanho($tamanho) {
		$this->tamanho = $tamanho;
	}

	/**
	 * @return the $valor
	 */
	public function getValor() {
		return $this->valor;
	}

	/**
	 * @param string $valor
	 */
	public function setValor($valor) {
		$this->valor = $valor;
	}

	/**
	 * @param string $mensagemInvalido
	 */
	protected function setMensagemInvalido($mensagemInvalido) {
		$this->mensagemInvalido = $mensagemInvalido;
	}

	/**
	 * Função para retornar a mensagem de Tipo de dados inválido
	 * @return the $mensagemInvalido
	 */
	public function getMensagemInvalido() {
		return ($this->mensagemInvalido);	
	}
	
	/**
	 * Validação comum a todos os tipos de dados
	 */
	protected function _validacaoComum() {
		global $system, $log;
		
		#################################################################################
		## Verifica se o tamanho foi definido
		#################################################################################
		if ($this->getTamanho() == null) {
			return false;
		}
		
		#################################################################################
		## Verifica se o valor foi definido
		#################################################################################
		if ($this->getValor() == null) {
			return false;
		}
		
		#################################################################################
		## Verifica se o valor está no tamanho definido
		#################################################################################
		if (($this->getTamanho() != "V") && ($this->getTamanho() != mb_strlen($this->getValor(),$system->config["database"]["charset"])) ) {
			return false;
		}

		return true;
		
	}
	

	/**
	 * Função de validação do tipo de dado
	 */
	public abstract function validar();
	
	/**
	 * Função de ajuste de tamanho do campo
	 */
	public abstract function completar();
	
	
	/**
	 * Monta o regex de Caracteres especiais
	 */
	protected function _getCERegex() {
		$str	= "";
		foreach ($this->ce as $ce) {
			$str .= "\\".$ce."|";
		}
		return(substr($str,0,-1));
	}
	

}

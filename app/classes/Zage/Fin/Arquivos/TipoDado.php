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
	 * @return the $tamanho
	 */
	public function getTamanho() {
		return $this->tamanho;
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
	 * Função de resgatar o valor sem a formatação
	 */
	public abstract function getCleanVal();
	
	/**
	 * Função de ajuste de tamanho do campo
	 */
	
	/**
	 * Função para completar a string
	 */
	public function completar() {
		global $system;
		
		#################################################################################
		## Retornar se o tamanho for variável
		#################################################################################
		if ($this->getTamanho() == "V") return;
		
		#################################################################################
		## Verifica o Caractere de preenchimento, retornar exceção se não definido
		#################################################################################
		$char	= $this->getCharPreenchimento();
		if (mb_strlen($char,$system->config["database"]["charset"]) == 0) {
			throw new \Exception('Character de preenchimento não definido');
		}
		
		#################################################################################
		## Completar a string de acordo com o alinhamento
		#################################################################################
		for ($i = mb_strlen($this->getValor(),$system->config["database"]["charset"]); $i < $this->getTamanho(); $i++) {
			if ($this->getAlinhamento() == "D") {
				$this->setValor($char . $this->getValor());
			}else{
				$this->setValor($this->getValor() . $char);
			}
		}
		
		#################################################################################
		## Verificar se a string está maior que o tamanho definido pra ela 
		#################################################################################
		$dif	= mb_strlen($this->getValor(),$system->config["database"]["charset"]) - $this->getTamanho() ;
		if ($dif > 0) {
			if ($this->getAlinhamento() == "D") {
				$this->setValor(substr($this->getValor(),$dif, $this->getTamanho()));					
			}else{
				$this->setValor(substr($this->getValor(),0, $this->getTamanho()));
			}
		}
	}
	
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

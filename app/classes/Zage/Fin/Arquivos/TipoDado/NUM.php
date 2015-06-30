<?php
namespace Zage\Fin\Arquivos\TipoDado;

/**
 * @package: Zage\Fin\Arquivos\TipoDado\NUM
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os tipos de dado do tipo N
 */

class NUM extends Zage\Fin\Arquivos\TipoDado {

	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Define o nome do Tipo de Dado
		#################################################################################
		$this->setNome("N");
		
		#################################################################################
		## Inicializa os atributos
		#################################################################################
		$this->setTamanho(null);
		$this->setValor(null);
		$this->setMensagemInvalido("Campo deve ser numérico de 0 a 9");
	
	}
	
	
	#################################################################################
	## Função de validação
	#################################################################################
	public function validar() {
		
		#################################################################################
		## Não validar se o campo não for mandatório e o valor for nulo 
		#################################################################################
		if (str_ireplace(" ", "", $this->getValor())  == "") return true;
		
		#################################################################################
		## Faz a validação comum a todos os campos
		#################################################################################
		if ($this->_validacaoComum() == false) return false;
				
		#################################################################################
		## Verifica se o valor so tem os caracteres desse tipo de dados
		#################################################################################
		if (!preg_match("/^[0-9]+$/", $this->getValor()) ) {
			return false;
		}
		
		return true;
	}
	
	#################################################################################
	## Função de validação
	#################################################################################
	public function completar() {
		if ($this->getTamanho() == "V") return;
		
		for ($i = strlen($this->getValor()); $i < $this->getTamanho(); $i++) {
			$this->setValor("0" . $this->getValor());
		}
		if (strlen($this->getValor()) > $this->getTamanho()) {
			$this->setValor(substr($this->getValor(),0, $this->getTamanho()));
		}
	}
}

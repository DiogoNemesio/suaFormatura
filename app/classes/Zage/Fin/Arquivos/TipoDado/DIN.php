<?php
namespace Zage\Fin\Arquivos\TipoDado;

/**
 * @package: Zage\Fin\Arquivos\TipoDado\DIN
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os tipos de dado do tipo N
 */

class DIN extends \Zage\Fin\Arquivos\TipoDado {
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Inicializa os atributos
		#################################################################################
		$this->setNome("Dinheiro");
		$this->setAlinhamento("D");
		$this->setCharPreenchimento("0");
		$this->setNumCasasDecimais(2);
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
	## Função de retornar o valor limpo de formatação
	#################################################################################
	public function getCleanVal() {
		return (\Zage\App\Util::to_float((int) $this->getValor())/100);
	}
	
	
}

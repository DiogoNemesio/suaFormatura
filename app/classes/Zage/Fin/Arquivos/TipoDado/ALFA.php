<?php
namespace Zage\Fin\Arquivos\TipoDado;

/**
 * @package: Zage\Fin\Arquivos\TipoDado\ALFA
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os tipos de dado do tipo N
 */

class ALFA extends \Zage\Fin\Arquivos\TipoDado {

	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Inicializa os atributos
		#################################################################################
		$this->setNome("AlfaNumérico");
		$this->setAlinhamento("E");
		$this->setCharPreenchimento(" ");
		$this->setNumCasasDecimais(null);
		$this->setTamanho(null);
		$this->setValor(null);
		$this->setMensagemInvalido("Campo deve ser Alfabético de A à Z, maiúsculas e minúsculas, brancos, números de 0 a 9 e caracteres especiais");
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
		$ces	= $this->_getCERegex();
		if (!preg_match("/^[a-zA-Z0-9(".$ces.")\s]+$/", $this->getValor()) ) {
			return false;
		}
		return true;
		
		return true;
	}
	
}

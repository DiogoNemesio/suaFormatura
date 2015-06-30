<?php
namespace Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro;

/**
 * @package: Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro\R0
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar o Tipo de Registro 0
 */

class R0 extends \Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro {
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Define o tipo do registro e o tamanho
		#################################################################################
		$this->setTipoArquivo("BOL_T40");
		$this->setTipoRegistro("0");
		$this->setConfigFromDB();
		
		#################################################################################
		## Carregar definição dos campos
		#################################################################################
		$this->carregarCampos();
	}
	

}

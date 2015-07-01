<?php
namespace Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro;

/**
 * @package: Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro\R1
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar o Tipo de Registro 1
 */

class R1 extends \Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro {
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Define o tipo do registro e o tamanho
		#################################################################################
		$this->setTipoArquivo("C400");
		$this->setCodLayout("BOL_T40");
		$this->setTipoRegistro("1");
		$this->setConfigFromDB();
		
		#################################################################################
		## Carregar definição dos campos
		#################################################################################
		$this->carregarCampos();
	}
	

}

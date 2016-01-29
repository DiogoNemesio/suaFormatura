<?php
namespace Zage\Fin\Arquivos\Layout\BOL_T240\TipoRegistro;

/**
 * @package: Zage\Fin\Arquivos\Layout\BOL_T240\TipoRegistro\R3
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar o Tipo de Registro 1
 */

class R3U extends \Zage\Fin\Arquivos\Layout\BOL_T240\TipoRegistro {
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Chama o construtor da classe mãe
		#################################################################################
		parent::__construct();
		
		#################################################################################
		## Define o tipo do registro e o tamanho
		#################################################################################
		$this->setTipoArquivo("C240");
		$this->setCodLayout("BOL_T240");
		$this->setTipoRegistro("3");
		$this->setCodSegmento("U");
		$this->setConfigFromDB();
		
		#################################################################################
		## Carregar definição dos campos
		#################################################################################
		$this->carregarCampos();
	}
	

}

<?php
namespace Zage\App\FilaImportacao\ResumoPDF\Item;

/**
 * @package: \Zage\App\FilaImportacao\ResumoPDF\Item\Mensagem
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar as mensagens de fila de importação
 */

class Mensagem extends \Zage\App\FilaImportacao\ResumoPDF\Item {

	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		
		#################################################################################
		## Chama o construtor da classe mãe
		#################################################################################
		parent::__construct();
		
		#################################################################################
		## Define o tipo
		#################################################################################
		$this->setTipo(parent::TIPO_MENSAGEM);
	}
	
}

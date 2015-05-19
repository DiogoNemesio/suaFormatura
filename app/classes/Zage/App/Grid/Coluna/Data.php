<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Data
 *
 * @package Data
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Data extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_DATA );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$html = $valor;
		return ($html);
	}
}

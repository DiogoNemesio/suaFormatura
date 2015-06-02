<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo DataHora
 *
 * @package DataHora
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class DataHora extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_DATAHORA );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$html = $valor;
		return ($html);
	}
}

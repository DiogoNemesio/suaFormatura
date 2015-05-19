<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Moeda
 *
 * @package Moeda
 *          @created 23/11/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Moeda extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_MOEDA );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$html = $valor;
		return ($html);
	}
}

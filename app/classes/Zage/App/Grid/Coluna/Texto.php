<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Texto
 *
 * @package Texto
 * @created 20/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Texto extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_TEXTO );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		if ($this->getMascara()) {
			$html = \Zage\App\Mascara::tipo($this->getMascara())->aplicaMascara($valor);
		}else{
			$html = $valor;
		}
		
		return ($html);
	}
}

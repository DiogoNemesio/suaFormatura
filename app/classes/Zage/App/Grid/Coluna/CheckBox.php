<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo ícone
 * 
 * @package CheckBox
 * @created 25/02/2015
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class CheckBox extends \Zage\App\Grid\Coluna {
	
	/**
	 * Modelo
	 *
	 * @var string
	 */
	private $modelo;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_CHECKBOX );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($nome,$valor) {
		$html = '<label class="position-relative"><input type="checkbox" name="'.$nome.'[]" value="'.$valor.'" class="ace" /><span class="lbl"></span></label>';
		return ($html);
	}
	
}

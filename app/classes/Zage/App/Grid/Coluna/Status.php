<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Status
 *
 * @package Status
 *          @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Status extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_STATUS );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		global $tr;
		
		if (empty($valor)) {
			//$html	= $tr->trans("DESATIVADO");
			
			$html = '<a data-toggle="tooltip" data-placement="top"  data-trigger="click hover" data-animation="true" data-title="'.$tr->trans("DESATIVADO (A)").'" title="'.$tr->trans("DESATIVADO (A)").'"><i class="fa fa-square-o bigger-130 grey"></i></a>';
		}else{
			//$html	= $tr->trans("ATIVO");
			
			$html = '<a data-toggle="tooltip" data-placement="top"  data-trigger="click hover" data-animation="true" data-title="'.$tr->trans("ATIVO (A)").'" title="'.$tr->trans("ATIVO (A)").'"><i class="fa fa-check-square-o bigger-130 green"></i></a>';
		}
		return ($html);
	}
}

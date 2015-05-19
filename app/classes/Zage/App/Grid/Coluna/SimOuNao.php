<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo SimOuNao
 *
 * @package Status
 *          @created 31/08/2014
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class SimOuNao extends \Zage\App\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_SIMOUNAO );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		global $tr;
		
		
		if (empty($valor)) {
			$texto	= "NÃO";
			$icone	= "fa fa-thumbs-down";
			$cor	= "red";
		}else{
			$texto	= "SIM";
			$icone	= "fa fa-thumbs-up";
			$cor	= "green";
		}
		
		$html = '<a data-toggle="tooltip" data-placement="top"  data-trigger="click hover" data-animation="true" data-title="'.$tr->trans($texto).'" title="'.$tr->trans($texto).'"><i class="'.$icone.' bigger-130 '.$cor.'"></i></a>';
		return ($html);
	}
}

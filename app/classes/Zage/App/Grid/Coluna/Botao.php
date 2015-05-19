<?php

namespace Zage\App\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Botão
 *
 * @package Botao
 *          @created 19/06/2013
 * @author Daniel Henrique Cassela
 * @version GIT: $Id$ 2.0.1
 *         
 */
class Botao extends \Zage\App\Grid\Coluna {
	
	/**
	 * Modelos existentes
	 */
	const MOD_ADD = 1;
	const MOD_EDIT = 2;
	const MOD_REMOVE = 3;
	const MOD_CANCEL = 4;
	
	/**
	 * url
	 *
	 * @var string
	 */
	private $url;
	
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
		
		$this->setTipo ( \Zage\App\Grid\Tipo::TP_BOTAO );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor,$url = null) {
		global $tr; 
		if (empty($url)) $url = (( $this->getUrl() ) ? "#" : $this->getUrl());
		
		if (substr($url,0,10) == "javascript") {
			$href	= $url;
		}else{
			$href	= "javascript:zgLoadUrl('" . $url . "');";
		}
		
		switch ($this->getModelo ()) {
			case self::MOD_ADD :
				$nome 	= $tr->trans('Adicionar');
				$classe = 'btn-info';
				$icone	= '<i class="fa fa-plus bigger-130"></i>';
				break;
			case self::MOD_EDIT :
				$nome 	= $tr->trans('Editar');
				$classe = 'btn-info';
				$icone	= '<i class="fa fa-edit bigger-130"></i>';
				break;
			case self::MOD_REMOVE :
				$nome 	= $tr->trans('Excluir');
				$classe = 'btn-danger red';
				$icone	= '<i class="fa fa-trash-o bigger-130 red"></i>';
				break;
			case self::MOD_CANCEL :
				$nome 	= $tr->trans('Cancelar');
				$classe = 'btn-danger red';
				$icone	= '<i class="fa fa-ban bigger-130 red"></i>';
				break;
		}
		$html = '<a href="'.$href.'" data-toggle="tooltip" data-trigger="click hover" data-animation="true" data-title="' . $nome . '">' . $icone . '</a>';
		//$html = '<a href="' . $url . '"><button class="btn btn-xs ' . $classe . '" type="button">' . $icone . '</button></a>';
		return ($html);
	}
	
	/**
	 *
	 * @return the $url
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 *
	 * @param string $url        	
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 *
	 * @return the $modelo
	 */
	public function getModelo() {
		return $this->modelo;
	}
	
	/**
	 *
	 * @param string $modelo        	
	 */
	public function setModelo($modelo) {
		$this->modelo = $modelo;
	}
}

<?php

namespace Zage\App\Menu;

/**
 * Gerenciar os menus do tipo 2 (menu Lateral esquerda)
 *
 * @package \Zage\App\Menu\Tipo2
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */ 
class Tipo2 extends \Zage\App\Menu\Tipo {
	
	/**
	 * Construtor
	 * 
	 * @param string $tipo        	
	 * @return void
	 */
	public function __construct($tipo) {
		parent::__construct($tipo);
	}
	
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	protected function iniciaHtml() {
		$this->html	.= '<div class="main-container" id="main-container">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'<script type="text/javascript">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2)."try{ace.settings.check('main-container' , 'fixed')}catch(e){}".\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'</script>'.\Zage\App\ZWS::NL;
	}
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	protected function finalizaHtml() {
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'</a>'.\Zage\App\ZWS::NL;
		$this->html	.= '</div>'.\Zage\App\ZWS::NL;
	}
	
	/**
	 * Inicia o código html do menu lateral
	 */
	private function iniciaMenuLateral() {
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<script type="text/javascript">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3)."try{ace.settings.check('sidebar' , 'fixed')}catch(e){}".\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</script>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div class="sidebar-shortcuts" id="sidebar-shortcuts">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<button class="btn btn-success"><i class="ace-icon fa fa-search"></i></button>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<button class="btn btn-info"><i class="ace-icon fa fa-yelp"></i></button>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<button class="btn btn-warning"><i class="ace-icon fa fa-group"></i></button>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<button class="btn btn-danger" data-rel="tooltip" title="Configurações"><i class="ace-icon fa fa-cog"></i></button>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<span class="btn btn-success"></span>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<span class="btn btn-info"></span>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<span class="btn btn-warning"></span>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<span class="btn btn-danger"></span>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div><!-- #sidebar-shortcuts -->'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<ul class="nav nav-list">'.\Zage\App\ZWS::NL;
	}
	
	/**
	 * Finaliza o código html do menu lateral
	 */
	private function finalizaMenuLateral() {
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</ul>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<script type="text/javascript">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4)."try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}".\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</script>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
	}
	
	/**
	 * Gerar o código html do menu
	 * @return void
	 */
	protected function geraHtml() {

		/**
		 * Inicializa o código HTML
		 */
		$this->iniciaMenuPadrao();
		$this->finalizaMenuPadrao();
		$this->iniciaHtml();
		$this->iniciaMenuLateral();
		
		if ($this->_array) {
			foreach ($this->_array as $codigo => $array) {
				$this->geraHtmlItem($codigo,$array);
			}
		}
		
		$this->finalizaMenuLateral();
		$this->criaDivCentral();
		//$this->criaMenuConfig();
		
		/**
		 * Finaliza o código HTML
		 */
		$this->finalizaHtml();
		
		
	}
	
}

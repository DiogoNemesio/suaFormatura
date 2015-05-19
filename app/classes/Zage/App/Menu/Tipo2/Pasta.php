<?php

namespace Zage\App\Menu\Tipo2;

/**
 * Gerenciar as pastas do Menu (Sub-Menu) 
 *
 * @package Pasta
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Pasta {
	
	/**
	 * Codigo da Pasta
	 * @var string
	 */
	private $codigo;
	
	/**
	 * Nome da pasta
	 * @var string
	 */
	private $nome;
	
	/**
	 * Icone da pasta
	 */
	private $icone;
	
	/**
	 * Codigo do Item Pai (superior)
	 * @var Integer
	 */
	private $itemPai;
		
	/**
	 * Nivel do pasta
	 * @var Integer
	 */
	private $nivel;
	
	
	/**
	 * Construtor
	 * 
	 * @return void
	 */
	public function __construct() {
		
	}
	
	/**
	 * Gera o código html 
	 * @return void
	 */
	public function geraHtml() {
		global $tr;
		if ($this->getIcone() != null) {
			$menuIcone	= '<i class="menu-icon '.$this->getIcone().'"></i>';
		}else{
			$menuIcone	= '';
		}

		$html  = str_repeat(\Zage\App\DBApp::TAB,5).'<a id="pasta_a_'.$this->getCodigo().'" href="#" class="dropdown-toggle">'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,6).$menuIcone.'<span class="menu-text">'.$tr->trans(trim($this->getNome())).'</span>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,6).'<b class="arrow fa fa-angle-down"></b>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,5).'</a>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,5).'<b class="arrow"></b>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,5).'<ul class="submenu">'.\Zage\App\DBApp::NL;

		return $html;
	}
	
	/**
	 * Abrir/Inicia a tag html do item
	 * @param string $codigo
	 * @return string
	 */
	public function abrirTag() {
		return str_repeat(\Zage\App\DBApp::TAB,4).'<li class="hover">'.\Zage\App\DBApp::NL;
	}
	
	/**
	 * Fechar a tag do código html do item
	 * @param String $codigo
	 * @return string
	 */
	public function fecharTag() {
		$html = str_repeat(\Zage\App\DBApp::TAB,5).'</ul>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,4).'</li>'.\Zage\App\DBApp::NL;
		return $html;

	}
	
	/**
	 * @return the $codigo
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param field_type $codigo
	 */
	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}

	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param field_type $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * @return the $icone
	 */
	public function getIcone() {
		return $this->icone;
	}

	/**
	 * @param field_type $icone
	 */
	public function setIcone($icone) {
		$this->icone = $icone;
	}

	/**
	 * @return the $itemPai
	 */
	public function getItemPai() {
		return $this->itemPai;
	}

	/**
	 * @param field_type $itemPai
	 */
	public function setItemPai($itemPai) {
		$this->itemPai = $itemPai;
	}

	/**
	 * @return the $nivel
	 */
	public function getNivel() {
		return $this->nivel;
	}

	/**
	 * @param field_type $nivel
	 */
	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}
	
}

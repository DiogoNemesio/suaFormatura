<?php

namespace Zage\App\Menu\Tipo1;

/**
 * Gerenciar os links do Menu 
 *
 * @package Link
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Link {
	
	/**
	 * Codigo da Pasta
	 * @var String
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
	 * @var integer
	 */
	private $itemPai;
	
	/**
	 * Url do Link
	 * @var string
	 */
	private $url;
	
	
	/**
	 * Descrição do Link
	 * @var string
	 */
	private $descricao;
	
	/**
	 * Nivel da pasta
	 * @var integer
	 */
	private $nivel;
	
	/**
	 * Target (Local onde será mostrado o link)
	 * @var string
	 */
	private $target;
	
	/**
	 * Id
	 * @var string
	 */
	private $id;
	
	
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
		if ($this->getURL()) {
			//$link 	= ' href="'.$this->getURL().'" target="'.$this->getTarget().'"';
			$link 	= ' href="javascript:zgLoadMenu(\''.$this->getURL().'\',\''.$this->getId().'\');"';
		}else{
			$link	= ' href="#"';
		}
		
		if ($this->getIcone()) {
			$icone	= '<i class="menu-icon fa fa-caret-right"></i><i class="'.$this->getIcone().'"></i><span>&nbsp;</span>';
		}else{
			$icone = '<i class="menu-icon fa fa-caret-right"></i>';
		}
		
		$html  = str_repeat(\Zage\App\DBApp::TAB,5).'<li id="link_li_'.$this->getCodigo().'">'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,6).'<a id="link_a_'.$this->getCodigo().'" '.$link.' >'.$icone.$tr->trans(trim($this->getNome())).'</a>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,6).'<b class="arrow"></b>'.\Zage\App\DBApp::NL;
		$html .= str_repeat(\Zage\App\DBApp::TAB,5).'</li>'.\Zage\App\DBApp::NL;
		
		return $html;
				
	}
	
	
	/**
	 * @return the $codigo
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param number $codigo
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
	 * @param string $nome
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
	 * @param number $itemPai
	 */
	public function setItemPai($itemPai) {
		$this->itemPai = $itemPai;
	}

	/**
	 * @return the $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return the $descricao
	 */
	public function getDescricao() {
		return $this->descricao;
	}

	/**
	 * @param string $descricao
	 */
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	/**
	 * @return the $nivel
	 */
	public function getNivel() {
		return $this->nivel;
	}

	/**
	 * @param number $nivel
	 */
	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}

	/**
	 * @return the $target
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param string $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}

	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


}

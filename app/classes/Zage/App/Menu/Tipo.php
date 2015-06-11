<?php

namespace Zage\App\Menu;

/**
 * Gerenciar os menus
 *
 * @package \Zage\App\Menu\Tipo
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */ 
abstract class Tipo {
	
	/**
	 * Tipos de menu
	 */
	const TIPO1 = "Tipo1";
	const TIPO2 = "Tipo2";
	const TIPO3 = "Tipo3";
	
	/**
	 * Tipo do Menu
	 * @var string
	 */
	protected $tipo;
	
	/**
	 * Array de itens do Menu
	 * @var array
	 */
	protected $itens;
	
	/**
	 * Array dos itens fixos
	 * @var array
	 */
	protected $fixedItens;
	
	/**
	 * Código html do menu
	 * @var string
	 */
	protected $html;
	
	/**
	 * Href Padrão onde os menus serão abertos
	 * @var string
	 */
	protected $target;
	
	/**
	 * Url de gerencia de menu tipo2
	 * @var string
	 */
	protected $url;
	
	/**
	 * Array com a ordem correta dos itens do menu
	 * @var array
	 */
	protected $_array;
	
	
	/**
	 * Construtor
	 * 
	 * @param string $tipo        	
	 * @return void
	 */
	public function __construct($tipo) {

		/**
		 * Define o tipo do Menu
		 */
		switch ($tipo) {
			case self::TIPO1:
			case self::TIPO2:
				$this->setTipo($tipo);
				break;
			default:
				\Zage\App\Erro::halt('Tipo de Menu desconhecido !!!');
		}
		
		/**
		 * Inicializa o array de itens
		 */
		$this->itens	= array();
		
	}
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	protected abstract function iniciaHtml();

	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	protected abstract function finalizaHtml();
	
	/**
	 * Gerar o código html do menu
	 * @return void
	 */
	protected abstract function geraHtml();
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	protected function iniciaMenuPadrao() {
		global $system,$_emp;
	
		$this->html	.= '<div class="navbar navbar-default" role="navigation" id="navbar">'.\Zage\App\ZWS::NL;
		$this->html	.= \Zage\App\ZWS::TAB.'<script type="text/javascript">try{ace.settings.check(\'navbar\' , \'fixed\')}catch(e){}</script>'.\Zage\App\ZWS::NL;
		$this->html	.= \Zage\App\ZWS::TAB.'<div class="navbar-container" id="navbar-container">'.\Zage\App\ZWS::NL;
		$this->html	.= \Zage\App\ZWS::TAB.'<!-- #section:basics/sidebar.mobile.toggle -->'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,1).'<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler">'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,2).'<span class="sr-only">Toggle sidebar</span>'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,2).'<span class="icon-bar"></span>'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,2).'<span class="icon-bar"></span>'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,2).'<span class="icon-bar"></span>'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,1).'</button>'.\Zage\App\ZWS::NL;
		
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,1).'<div class="navbar-header pull-left" >'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<a class="navbar-brand" target="_blank" href="#"></a>'.\Zage\App\ZWS::NL;
		$this->html .= str_repeat(\Zage\App\ZWS::TAB,1).'</div>'.\Zage\App\ZWS::NL;
	
	}
	
	/**
	 * Monta as notificações
	 */
	protected function finalizaMenuPadrao() {
		global $system,$_user,$_emp,$tr,$_org;
	
		$this->html .= \Zage\App\ZWS::TAB.'<div class="navbar-buttons navbar-header pull-right" role="navigation">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<ul class="nav ace-nav">'.\Zage\App\ZWS::NL;
		
		/** Montar o combo de Organização **/
		if (is_object($_org)) {
			$codOrg				= $_org->getCodigo();
			$ident				= $_org->getIdentificacao();
		}else{
			$codOrg				= "!";
			$ident				= "Nenhuma organização";
		}
		
		$numMaxOrg			= (int) \Zage\Adm\Parametro::getValor('APP_NUM_MAX_EMPRESA_SEL');
		if (!$numMaxOrg)	$numMaxOrg = 10;
		
		$organizacoes	= \Zage\Seg\Usuario::listaOrganizacaoAcesso($system->getCodUsuario());
		$numOrg			= sizeof($organizacoes);
				
		
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<li class="grey">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-building-o"></i>&nbsp;'.$ident.'</a>'.\Zage\App\ZWS::NL;
		if ($numOrg > 0) {
			$this->html .= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">'.\Zage\App\ZWS::NL;
			$this->html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="dropdown-header"><i class="ace-icon fa fa-building-o"></i>Selecione o Condomínio</li>'.\Zage\App\ZWS::NL;
				
			if ($numOrg > $numMaxOrg) {
				$t	= $numMaxOrg;
			}else{
				$t	= $numOrg;
			}
			for ($i = 0; $i < $t; $i++) {
				if ($organizacoes[$i]->getCodigo() == $system->getCodOrganizacao()) {
					$icone		= "ace-icon fa fa-circle";
					$sel 		= '<span class="pull-right"><i class="ace-icon fa fa-check"></i></span>';
					$nome		= "<b>".$organizacoes[$i]->getCodigo() . ' - '.$organizacoes[$i]->getIdentificacao()."</b>";
				}else{
					$icone		= "ace-icon fa fa-circle-o";
					$sel 		= null;
					$nome		= $organizacoes[$i]->getCodigo(). ' - '.$organizacoes[$i]->getIdentificacao();
				}
		
				$url 	= ROOT_URL. "/index.php?zid=".\Zage\App\Util::encodeUrl('_codOrganizacao='.$organizacoes[$i]->getCodigo());
		
		
				$this->html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li><a href="'.$url.'"><div class="clearfix"><span class="pull-left"><i class="ace-icon '.$icone.'"></i>&nbsp;'.$nome.'</span>'.$sel.'</div></a></li>'.\Zage\App\ZWS::NL;
			}
				
			if ($t < $numOrg) {
				$this->html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="dropdown-footer"><a href="javascript:zgAbreModal(\''.ROOT_URL.'/Fmt/mudaOrganizacao.php\');">Mostrar todos<i class="ace-icon fa fa-arrow-right"></i></a></li>'.\Zage\App\ZWS::NL;
			}
				
			$this->html .= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
		}
		
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</li>'.\Zage\App\ZWS::NL;
	
		/** Resgata o parâmetro de tempo de Atualização da notificação **/
		$timeout	= (int) \Zage\Adm\Parametro::getValor('TEMPO_ATUALIZA_NOTIFICACAO');
		if (!$timeout)	$timeout	= 5000;
		
		/** Criar o espaço das notificações **/
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<li class="purple" id="zgNotificacoesID">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</li>'.\Zage\App\ZWS::NL;
		
			
		/** Criar o espaço das Mensagens **/
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<li class="green" id="zgMensagensID">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</li>'.\Zage\App\ZWS::NL;
		
		
		/** Carrega as informações do usuário **/
		$msg	= ($_user->getSexo() == "F") ? "Bem Vinda" : "Bem Vindo"; 
		$avatar	= ($_user->getAvatar()) ? $_user->getAvatar()->getLink() : IMG_URL."/avatars/usuarioGenerico.png";
		
		
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<li class="light-blue">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<a data-toggle="dropdown" href="#" class="dropdown-toggle"><img class="nav-user-photo" src="'.$avatar.'" alt="'.$_user->getNome().'" /><span class="user-info"><small>'.$msg.',</small>'.$_user->getNome().'</span><i class="ace-icon fa fa-caret-down"></i></a>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">'.\Zage\App\ZWS::NL;
		
		if ($this->fixedItens) {
			foreach ($this->fixedItens as $menu) {
				$id			= $this->geraId($menu->getUrl(),$menu->getCodigo(),$menu->getIcone());
				$href		= 'javascript:zgLoadMenu(\''.$menu->getUrl().'\',\''.$id.'\');';
				$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<li><a href="'.$href.'" ><i class="ace-icon '.$menu->getIcone().'"></i>'.$menu->getNome().'</a></li>'.\Zage\App\ZWS::NL;
			}
		}
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="divider"></li>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<li><a href="'.ROOT_URL.'/Seg/logoff.php" target="_top" ><i class="ace-icon fa fa-off"></i>Sair</a></li>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</li>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</ul>'.\Zage\App\ZWS::NL;
		$this->html	.= \Zage\App\ZWS::TAB.'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= '</div><!-- /#ace-settings-container -->'.\Zage\App\ZWS::NL;
		$this->html	.= '<script type="text/javascript">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'$(\'#zgNotificacoesID\').load("'.ROOT_URL.'/App/notificacao.php");'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'$(\'#zgMensagensID\').load("'.ROOT_URL.'/App/mensagem.php");'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'var auto_refresh = setInterval('.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'function () {'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'$(\'#zgNotificacoesID\').load("'.ROOT_URL.'/App/notificacao.php");'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'$(\'#zgMensagensID\').load("'.ROOT_URL.'/App/mensagem.php");'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'}, '.$timeout.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).');'.\Zage\App\ZWS::NL;
		$this->html	.= '</script>'.\Zage\App\ZWS::NL;
		
		$this->html	.= '</div><!-- /.navbar-container -->'.\Zage\App\ZWS::NL;
		
	}
	
	/**
	 * Cria o menu de personalização do sistema
	 */
	protected function criaMenuConfig() {
		$this->html	.= '<div class="ace-settings-container" id="ace-settings-container">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<i class="fa fa-cog bigger-150"></i>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'<div class="ace-settings-box" id="ace-settings-box">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="pull-left">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<select id="skin-colorpicker" class="hide">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<option data-skin="default" value="#438EB9">#438EB9</option>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<option data-skin="skin-1" value="#222A2D">#222A2D</option>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<option data-skin="skin-2" value="#C6487E">#C6487E</option>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'</select>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<span>&nbsp; Selecione o Estilo</span>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<label class="lbl" for="ace-settings-navbar"> Fixar Barra de Nav.</label>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<label class="lbl" for="ace-settings-sidebar"> Fixar Barra Lateral </label>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<label class="lbl" for="ace-settings-breadcrumbs">Fixar Mapa </label>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<label class="lbl" for="ace-settings-rtl"> Inverter posição (rtl)</label>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<label class="lbl" for="ace-settings-add-container">Colocar <b>.container</b></label>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,1).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= '</div>'.\Zage\App\ZWS::NL;
	}
	
	/**
	 * Cria o Iframe Central
	 */
	protected function criaDivCentral() {
		global $system;
	
		/** Define a classe do Div principal **/
		switch ($this->getTipo()) {
			case self::TIPO1:
				$classe			= 'main-content';
				$usaBreadcrumb	= true; 
				break;
			case self::TIPO2:
				$classe			= 'main-content';
				$usaBreadcrumb	= false; 
				break;
			default:
				$classe			= 'main-content';
				$usaBreadcrumb	= true; 
				break;
		}

		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,2).'<div class="'.$classe.'">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="main-content-inner">'.\Zage\App\ZWS::NL;
		if ($usaBreadcrumb) {
			$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="breadcrumbs" id="breadcrumbs">'.\Zage\App\ZWS::NL;
			$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<script type="text/javascript">'.\Zage\App\ZWS::NL;
			$this->html	.= str_repeat(\Zage\App\ZWS::TAB,5)."try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}".\Zage\App\ZWS::NL;
			$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'</script>'.\Zage\App\ZWS::NL;
			$this->html .= \Zage\App\suaFormatura::geraLocalizacao(null);
			$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		}
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div id="zgSpinnerID" class="hidden" style="width: 100%; height: 100%; position: fixed; display: block; opacity: 0.7; background-color: #fff; z-index: 99; text-align: center;">'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<i class="fa fa-spinner fa-spin fa-3x"></i>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'</div>'.\Zage\App\ZWS::NL;
		$this->html	.= str_repeat(\Zage\App\ZWS::TAB,3).'<div class="page-content" name="'.$system->getDivCentral().'" id="'.$system->getDivCentral().'ID"></div>'.\Zage\App\ZWS::NL;
	}
	
	
	
	/**
	 * Adiciona uma pasta ao menu
	 * @param integer $codigo
	 * @param string $nome
	 * @param string $icone
	 * @param integer $itemPai
	 */
	public function adicionaPasta($codigo,$nome,$icone,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
		
		/**
		 * Verifica se o item Pai existe
		 */
		if ($this->getTipo() !== self::TIPO3) {
			if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
				die('Item Pai inexistente ('.$itemPai.')');
			}
		}
		
		/**
		 * Cria a pasta
		 */
		$classe					= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Pasta";
		$this->itens[$codigo]	= new $classe;
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setNome($nome);
		$this->itens[$codigo]->setIcone($icone);
		$this->itens[$codigo]->setitemPai($itemPai);
		
		if ($this->getTipo() == self::TIPO3) {
			$this->itens[$codigo]->setUrl($this->getUrl());
		}
	}
	
	/**
	 * Adiciona um Link ao menu
	 * @param integer $codigo
	 * @param string $nome
	 * @param string $icone
	 * @param string $url
	 * @param string $descricao
	 * @param integer $itemPai
	 */
	public function adicionaLink($codigo,$nome,$icone,$url,$descricao,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
	
		/**
		 * Verifica se o item Pai existe
		 */
		if ($this->getTipo() !== self::TIPO3) {
			if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
				die('Item Pai inexistente ('.$itemPai.')');
			}
		}
	
		/**
		 * Cria o link
		 */
		$classe					= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Link";
		$this->itens[$codigo]	= new $classe;
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setNome($nome);
		$this->itens[$codigo]->setIcone($icone);
		$this->itens[$codigo]->setUrl($this->montaUrl($url, $codigo, $icone));
		$this->itens[$codigo]->setDescricao($descricao);
		$this->itens[$codigo]->setItemPai($itemPai);
		$this->itens[$codigo]->setTarget($this->getTarget());
		$this->itens[$codigo]->setId(self::geraId($url, $codigo,$icone));
	}
	
	
	/**
	 * Adiciona um Link ao menu
	 * @param integer $codigo
	 * @param integer $itemPai
	 */
	public function adicionaSeparador($codigo,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
	
		/**
		 * Verifica se o item Pai existe
		 */
		if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
			die('Item Pai inexistente ('.$itemPai.')');
		}
	
		/**
		 * Cria o Separador
		 */
		$classe					= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Separador";
		$this->itens[$codigo]	= new $classe;
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setItemPai($itemPai);
	
	}
	
	/**
	 * Adiciona um Link Fixo ao menu
	 * @param integer $codigo
	 * @param string $nome
	 * @param string $icone
	 * @param string $url
	 * @param string $descricao
	 */
	public function adicionaLinkFixo($codigo,$nome,$icone,$url,$descricao) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItemFixo($codigo) == true) {
			die('Código Fixo já existente ('.$codigo.')');
		}
	
		/**
		 * Cria o link
		 */
		$classe					= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Link";
		$this->fixedItens[$codigo]	= new $classe;
		$this->fixedItens[$codigo]->setCodigo($codigo);
		$this->fixedItens[$codigo]->setNome($nome);
		$this->fixedItens[$codigo]->setIcone($icone);
		$this->fixedItens[$codigo]->setUrl($this->montaUrl($url, $codigo, $icone));
		$this->fixedItens[$codigo]->setDescricao($descricao);
		$this->fixedItens[$codigo]->setTarget($this->getTarget());
		$this->fixedItens[$codigo]->setId(self::geraId($url, $codigo, $icone));
	}
	
	
	/**
	 * Verifica se existe o item informado
	 * @param integer $codigo
	 * @return boolean
	 */
	protected function existeItem($codigo) {
		if (!$this->itens) return false;
		if (array_key_exists($codigo, $this->itens)) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Verifica se existe o item informado
	 * @param integer $codigo
	 * @return boolean
	 */
	protected function existeItemFixo($codigo) {
		if (!$this->fixedItens) return false;
		if (array_key_exists($codigo, $this->fixedItens)) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Montar a URL de um Menu
	 *
	 * @param String $link
	 * @param String $codItem
	 */
	public static function montaUrl($link,$codItem,$icone = null) {
		$id		= self::geraId($link, $codItem, $icone);
		$url	= $link."?id=".$id;
		return ($url);
	}
	
	/**
	 * Montar a URL Completa de um Menu
	 *
	 * @param String $link
	 * @param String $codItem
	 */
	public static function montaUrlCompleta($codMenu) {
		global $em,$log;
		
		$info 		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array("codigo" => $codMenu));
		
		if (!$info) {
			return "#";
		}
		
		$modulo = (!$info->getCodModulo()) ? "Ext" : $info->getCodModulo()->getApelido();
		
		if ($modulo == "Ext"){
			$url	= $info->getLink();
		}else{
			$url	= ROOT_URL . "/$modulo/" . self::montaUrl($info->getLink(), $info->getCodigo(),$info->getIcone());
		}
		return ($url);
	}
	
	
	/**
	 * Montar a URL de um Menu
	 *
	 * @param String $link
	 * @param String $codItem
	 */
	public static function geraId($link,$codItem,$icone = null) {
		/**
		 * verifica se a url já tem alguma variável
		 **/
		if (strpos($link,'?') !== false) {
			$vars	= '&'.substr(strstr($link, '?'),1);
			$link	= substr($link,0,strpos($link, '?'));
		}else{
			$vars	= '';
		}
			
		$id		= \Zage\App\Util::encodeUrl("_codMenu_=".$codItem."&_icone_=".$icone.$vars);
		return ($id);
	}
	
	/** 
	 * Gera o array na ordem correta de nível e ordem
	 */
	protected function geraArray() {
		global $nivel,$nivelMax;

		/**
		 * Define os contadores para não deixar acontecer uma recursividade
		 */
		$nivel			= 0;
		$nivelMax		= 500;
		
		/** 
		 * Primeiro percorre o nível 0, os itens que não tem pai 
		 **/
		foreach ($this->itens as $codigo => $obj) {
			if ($this->getTipo() == self::TIPO3) {
				$this->_array[$codigo]	= array();
			}else{
				if ($obj->getItemPai() == null) {
					$this->_array[$codigo]	= array();
				}
			}
		}
		
		if ($this->_array) {
			/** 
			 * Encontrar os filhos 
			 **/
			foreach ($this->_array as $codigo => $array) {
				$this->descobreMenuFilhos($this->_array[$codigo], $codigo);			
			}
		
			/** 
			 * Definir os níveis dos menus 
			 **/
			foreach ($this->_array as $codigo => $array) {
				$this->itens[$codigo]->setNivel(0);
				$this->defineNivel($this->_array[$codigo],1);
			}
		
		}
		
		//print_r($this->_array);
	} 
	
	/**
	 * Descobre os filhos do $item no $this->_array e coloca em $array
	 * @param array $array
	 * @param string $item
	 */
	protected function descobreMenuFilhos(&$array,$item) {
		global $nivel,$nivelMax;
		$nivel++;
		foreach ($this->itens as $codigo => $obj) {
			if ($obj->getItemPai() == $item) {
				$array[$codigo] = array();
				$this->descobreMenuFilhos($array[$codigo], $codigo);
			}
			if ($nivel > $nivelMax) die('Recursividade encontrada em :'.__FUNCTION__);
		}
		
	}
	
	/**
	 * Definir o nível dos menus
	 * @param array $array
	 * @param Integer $nivel
	 */
	protected function defineNivel(&$array,$nivel) {
		foreach ($array as $cod => $arr) {
			$this->itens[$cod]->setNivel($nivel);
			if (!empty($arr)) {
				$this->defineNivel($array[$cod], $nivel+1);
			}
		}
	}
	
	/**
	 * Gerar o código html do menu
	 * @return void
	 */
	protected function geraHtmlItem($codigo,$array) {
		global $log;
		$clPasta	= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Pasta";
		$clLink		= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Link";
		$clSep		= "\\Zage\\App\\Menu\\".$this->getTipo()."\\Separador";
		
		if ($this->itens[$codigo] instanceof $clPasta) {
			$this->html .= $this->itens[$codigo]->abrirTag();
			$this->html .= $this->itens[$codigo]->geraHtml();
			if (!empty($array)) {
				foreach ($array as $cod => $arr) {
					$this->geraHtmlItem($cod, $arr);
				}
			}
			$this->html .= $this->itens[$codigo]->fecharTag();
		}elseif ($this->itens[$codigo] instanceof $clLink) {
			$this->html .= $this->itens[$codigo]->geraHtml();
		}elseif ($this->itens[$codigo] instanceof $clSep) {
			$this->html .= $this->itens[$codigo]->geraHtml();
		}
		
	}
	
	/**
	 * Resgatar um array com a árvore completa de um menu
	 */
	public static function getArrayArvore($codMenu) {
		global $em,$log;
		
		$array		= array();
		$info 		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array("codigo" => $codMenu));
		
		if (!$info) return $array;
		
		$codMenuPai	= $info->getCodMenuPai() ? $info->getCodMenuPai()->getCodigo() : null;
		$array[]	= $info->getCodigo();
		 
		while ($codMenuPai != '') {
			$info 		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array("codigo" => $codMenuPai));
			$codMenuPai	= $info->getCodMenuPai() ? $info->getCodMenuPai()->getCodigo() : null;
			$array[]	= $info->getCodigo();
			if (!$info) return (array_reverse($array));
		}
		 
		return (array_reverse($array));
	}
	
	
	/**
	 * Resgatar um array com a árvore completa de um menu com a Url
	 */
	public static function getArrayArvoreUrl($codMenu) {
		global $em,$log;
		 
		$array		= array();
		$info 		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array("codigo" => $codMenu));
		 
		if (!$info) return ($array);

		$codMenuPai					= $info->getCodMenuPai() ? $info->getCodMenuPai()->getCodigo() : null;
		$array[$info->getCodigo()]	= $info;
		
		while ($codMenuPai != '') {
			$info 		= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array("codigo" => $codMenuPai));
			$codMenuPai	= $info->getCodMenuPai() ? $info->getCodMenuPai()->getCodigo() : null;
			$array[$info->getCodigo()]	= $info;
			if (!$info) return (array_reverse($array));
		}
		 
		return (array_reverse($array));
	}
	
	
	
	/**
	 * @return the $tipo
	 */
	protected function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param string $tipo
	 */
	protected function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	/**
	 * @return the $html
	 */
	public function getHtml() {
		$this->geraArray();
		$this->geraHtml();
		return $this->html;
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

}

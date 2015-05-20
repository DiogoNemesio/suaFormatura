<?php
namespace Zage\App;

/**
 * Implementação do DBApp
 * 
 * @package: System
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class suaFormatura extends \Zage\App\ZWS {
	
	/**
	 * Objeto que irá guardar a Instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Código da empresa selecionada
	 *
	 * @var int
	 */
	private $codEmpresa;

	/**
	 * Código da Matriz
	 *
	 * @var int
	 */
	private $codMatriz;
	
	/**
	 * Código da linguagem padrão (Internacionalização)
	 *
	 * @var int
	 */
	private $codLang;
	
	/**
	 * Código da Organização
	 *
	 * @var int
	 */
	private $codOrganizacao;
	
	/**
	 * Configurações do Dynamic Html Load
	 */
	private $dynHtmlLoad;
	
	/**
	 * Url do site inicial
	 * @var string
	 */
	private $homeUrl;
	
	/**
	 * Nome do iframe central
	 * @var string
	 */
	private $divCentral;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	protected function __construct() {
	/**
	 * Verificar função inicializaSistema() *
	 */
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			$c = __CLASS__;
			self::$instance = new $c ();
		}
		
		return self::$instance;
	}
	
	/**
	 * Refazer a função para não permitir a clonagem deste objeto.
	 */
	public function __clone() {
		\Zage\App\Erro::halt ( 'Não é permitido clonar ' );
	}
	
    
	/**
	 * Inicializar o sistema
	 * @return void
	 */
    public function inicializaSistema () {
    	global $log,$db;
    	
    	/** Chama o construtor da classe mae **/
		parent::__construct();	
    	
		$log->debug(__CLASS__.": nova Instância");

		/** Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail) **/
		$this->mail->setSubject('.:: Erro no sistema ::.');
		
    }

    /**
     * Definir o Html Load
     *
     * @param string $valor
     */
    public function setDynHtmlLoad($valor) {
    	$this->dynHtmlLoad	= $valor;
    }
    
    /**
     * Resgatar o Html Load
     *
     * @return string
     */
    public function getDynHtmlLoad() {
    	return ($this->dynHtmlLoad);
    }
    
	/**
	 * @return the $homeUrl
	 */
	public function getHomeUrl() {
		return $this->homeUrl;
	}

	/**
	 * @param string $homeUrl
	 */
	public function setHomeUrl($homeUrl) {
		$this->homeUrl = $homeUrl;
	}

	/**
	 * @return the $divCentral
	 */
	public function getDivCentral() {
		return $this->divCentral;
	}

	/**
	 * @param string $divCentral
	 */
	public function setDivCentral($divCentral) {
		$this->divCentral = $divCentral;
	}

    /**
     * Gerar o html da localização do Menu
     *
     * @return string
     */
    public static function geraLocalizacao($codMenu) {
    	global $system,$log,$em,$_emp,$_user;
    	
    	$aLocal         = \Zage\App\Menu\Tipo::getArrayArvoreUrl($codMenu);
    	$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));

    	$html			= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="breadcrumb">'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,5).'<li>'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,6).'<i class="ace-icon fa fa-home home-icon"></i><a href="'.$system->getHomeUrl().'" target="_top">Início</a>'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,5).'</li>'.\Zage\App\ZWS::NL;
    	$total			= sizeof($aLocal);
    	
    	foreach ($aLocal as $menu) {
    		if ($menu->getLink() != null) {
    			$url 	= \Zage\App\Menu\Tipo::montaUrlCompleta($menu->getCodigo());
       		} else{
    			$url	= "#";
    		}
    		
    		if ($codMenu == $menu->getCodigo()) {
    			$class	= "active";
    		}else{
    			$class  = null;
    		}
    		
    		$id 	= \Zage\App\Menu\Tipo::geraId($menu->getLink(), $menu->getCodigo());
    		
    		$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="'.$class.'"><a href="javascript:zgLoadUrl(\''.$url.'\',\''.$id.'\');">'.$menu->getNome().'</a></li>'.\Zage\App\ZWS::NL;
    	
    	}

    	$html	.= str_repeat(\Zage\App\ZWS::TAB,4)."</ul>".\Zage\App\ZWS::NL;
	    $html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<div style="margin-right:-8px;" class="pull-right">'.\Zage\App\ZWS::NL;
	     
    	if (($info) && !$info->getCodModulo()) {
	    	$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<div class="pull-right"><h5 class="lighter">[&nbsp;'.$info->getCodModulo()->getApelido(). "&nbsp;]".'&nbsp;<a href="#"><img width="16" height="16" src="'.ICON_URL. '/'. $info->getCodModulo()->getIcone().'" /></a></h5></div>'.\Zage\App\ZWS::NL;
    	//}elseif ($_user->getUltModuloAcesso()) {
    	//	$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<div class="pull-right"><h5 class="lighter">[&nbsp;'.$_user->getUltModuloAcesso()->getApelido(). "&nbsp;]".'&nbsp;<a href="#"><img width="16" height="16" src="'.ICON_URL. '/'. $_user->getUltModuloAcesso()->getIcone().'" /></a></h5></div>'.\Zage\App\ZWS::NL;
    	}
	    $html	.= str_repeat(\Zage\App\ZWS::TAB,6).'<div style="position: relative;margin-right:-18px;" class="nav-search minimized pull-right" data-rel="tooltip" title="Mudar de Módulo"><form action="'.ROOT_URL.'?id=" method="POST" class="form-search"><span class="input-icon"><input type="text" autocomplete="off" class="input-xs nav-search-input" placeholder="Módulo" name="modApelido" maxlength="3" /><i class="ace-icon fa fa-th nav-search-icon" style="cursor: pointer;" onclick="javascript:zgLoadUrl(\''.ROOT_URL.'/App/modulo.php?id=\');"></i></span></form></div>'.\Zage\App\ZWS::NL;
    	$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'</div> '.\Zage\App\ZWS::NL;
    	 
    	return ($html);
    }
    
    /**
     * Gerar o html da localização do Menu, sem os links (somente visualização)
     *
     * @return string
     */
    public static function mostraLocalizacao($codMenu) {
    	global $system,$log,$em,$_emp,$_user,$tr;
    	 
    	$aLocal         = \Zage\App\Menu\Tipo::getArrayArvoreUrl($codMenu);
    	$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $codMenu));
    
    	$html			= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="breadcrumb">'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,5).'<li>'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,6).'<i class="ace-icon fa fa-home home-icon"></i><a href="#" target="_top">'.$tr->trans("Início").'</a>'.\Zage\App\ZWS::NL;
    	$html			.= str_repeat(\Zage\App\ZWS::TAB,5).'</li>'.\Zage\App\ZWS::NL;
    	$total			= sizeof($aLocal);
    	 
    	foreach ($aLocal as $menu) {
   			$url	= "#";
    
    		if ($codMenu == $menu->getCodigo()) {
    			$class	= "active";
    		}else{
    			$class  = null;
    		}
    
    		$id 	= \Zage\App\Menu\Tipo::geraId($menu->getLink(), $menu->getCodigo());
    
    		$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="'.$class.'"><a href="#">'.$menu->getNome().'</a></li>'.\Zage\App\ZWS::NL;
    		 
    	}
    
    	$html	.= str_repeat(\Zage\App\ZWS::TAB,4)."</ul>".\Zage\App\ZWS::NL;
    	return ($html);
    }
    
    
    /**
     * Gerar o html da Combo
     *
     * @return string
     */
	public function geraHtmlCombo($array,$codigo,$valor,$codigoSel = null,$valorDefault = null) {
		global $system;
	
		$accessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
	
		$html   = '';
		if ($valorDefault !== null) {
			($codigoSel == null) ? $selected = "selected=\"true\"" : $selected = "";
			$html    .= "<option $selected value=\"\">".$valorDefault."</option>";
		}
		$i = 0;
		foreach ($array as $info) {
			
			$comboValue	= $accessor->getValue($info, $codigo);
			
			if ( (is_array($codigoSel)) && (in_array($comboValue, $codigoSel))) {
				$selected = "selected=\"selected\"";
			}elseif ($codigoSel !== null) {
				($codigoSel == $accessor->getValue($info, $codigo)) ? $selected = "selected=\"true\"" : $selected = "";
			}else{
				if (($i == 0) && ($valorDefault === null) && !(is_array($codigoSel))) {
					$selected = "selected=\"true\"";
				}else{
					$selected = "";
				}
			}
			$html .= "<option value=\"".$comboValue."\" $selected>".$accessor->getValue($info, $valor).'</option>';
			$i++;
		}
		return ($html);
	}
            
    /**
     * Gerar o html da Combo do tipo Sim ou Não
     *
     * @return string
     */
    public function geraHtmlComboSN($codigoSel = null,$valorDefault = null) {
    	global $system,$tr;
    	
    	$array = array("0" => $tr->trans("Não"), "1" => $tr->trans("Sim"));
    
    	$html   = '';
    	if ($valorDefault !== null) {
    		($codigoSel == null) ? $selected = "selected=\"true\"" : $selected = "";
    		$html    .= "<option $selected value=\"\">".$valorDefault."</option>";
    	}
    	$i = 0;
    	foreach ($array as $val => $info) {
    		if ($codigoSel !== null) {
    			($codigoSel == $val) ? $selected = "selected=\"true\"" : $selected = "";
    		}else{
    			if (($i == 0) && ($valorDefault === null)) {
    				$selected = "selected=\"true\"";
    			}else{
    				$selected = "";
    			}
    		}
    		$html .= "<option value=\"".$val."\" $selected>".$info.'</option>';
    		$i++;
    	}
    	return ($html);
    }
    
    /**
     * Resgatar o html padrão
     * @return string
     */
    public function loadHtml() {
    	return $this->getDynHtmlLoad();
    }
    
    /**
     * Selecionar a empresa
     * @param number $codEmpresa
     */
    public function selecionaEmpresa($codEmpresa) {
    	global $log,$em,$_user,$_emp;
    	
    	if ($codEmpresa) {
    		
    		/** Verifica se a empresa existe **/
    		$_emp	= $em->getRepository('Entidades\ZgadmEmpresa')->findOneBy(array('codOrganizacao' => $this->getCodOrganizacao(), 'codigo' => $codEmpresa));
    		
    		if ($_emp) {
    			$log->debug('Empresa selecionada: '.$codEmpresa);
    			$_user->setUltEmpresaAcesso($_emp);
    			$em->persist($_user);
    			$em->flush();
    			$em->detach($_user);
    			$this->setCodEmpresa($codEmpresa);
    			
    			if ($_emp->getCodMatriz()) {
    				$this->setCodMatriz($_emp->getCodMatriz()->getCodigo());
    			}else{
    				$this->setCodMatriz($codEmpresa);
    			}
    			
    		}
    	}
    }
    
    /**
     * Selecionar um módulo
     * @param number $codModulo
     */
    public function selecionaModulo($codModulo) {
    	global $log,$em,$_user,$_mod;
    	
    	if (self::temPermissaoNoModulo($codModulo) == false) return false;
    	
    	$_mod		= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array ('codigo' => $codModulo));
    	
    	if ($_mod) {
    		//$log->debug("Usuário do módulog: ".$_user->getUsuario());
    		$_user->setUltModuloAcesso($_mod);
    		$em->persist($_user);
    		$em->flush();
    		$em->detach($_user);
    	}
    }
    
    /**
     * Verifica se o usuário tem permissão no menu
     */
    public function checaPermissao($codMenu) {
    	if ($this->temPermissaoNoMenu($codMenu) == false) \Zage\App\Erro::halt('Sem Permissão no Menu !!!');
    }
    
    /**
     * Verifica se o usuário tem permissão no menu
     */
    public function temPermissaoNoMenu($codMenu) {
    	global $em,$system;
    	
    	$info	= $em->getRepository('Entidades\ZgappMenu')->find($codMenu);
    	
    	if (!$info) return false;
    	
    	if ($info->getIndFixo() == '1') return true;
    	
    	$qb 	= $em->createQueryBuilder();
    	 
    	$qb->select($qb->expr()->count('m.codigo'))
    	->from('\Entidades\ZgappMenu','m')
    	->leftJoin('\Entidades\ZgappMenuPerfil'			,'mp'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo 		= mp.codMenu')
    	->leftJoin('\Entidades\ZgsegUsuarioEmpresa'		,'ue'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'ue.codPerfil 	= mp.codPerfil')
    	->leftJoin('\Entidades\ZgsegUsuario'			,'u'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 		= ue.codUsuario')
    	->where($qb->expr()->andX(
    		$qb->expr()->eq('u.codigo'	, ':codUsuario'),
    		$qb->expr()->eq('m.codigo'	, ':codMenu')
    	))
    	->setParameter('codUsuario'	, $system->getCodUsuario())
    	->setParameter('codMenu'	, $codMenu);
    	 
    	$query 	= $qb->getQuery();
    	$return = $query->getSingleScalarResult();
    	 
    	if ($return > 0) return true;
    	
    	return false;
    }
    
    /**
     * Verifica se o usuário tem permissão de acesso ao módulo
     */
    public function temPermissaoNoModulo($codModulo) {
    	global $em,$system;
    	 
    	$info	= $em->getRepository('Entidades\ZgappModulo')->findOneBy(array('codigo' => $codModulo));
    	 
    	if (!$info) return false;
    	 
    	$qb 	= $em->createQueryBuilder();

    	$qb->select($qb->expr()->count('m.codigo'))
    	->from('\Entidades\ZgappMenu','m')
    	->leftJoin('\Entidades\ZgappMenuPerfil'			,'mp'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'm.codigo 		= mp.codMenu')
    	->leftJoin('\Entidades\ZgsegUsuarioEmpresa'		,'ue'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'ue.codPerfil 	= mp.codPerfil')
    	->leftJoin('\Entidades\ZgsegUsuario'			,'u'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 		= ue.codUsuario')
    	->where($qb->expr()->andX(
    			$qb->expr()->eq('u.codigo'		, ':codUsuario'),
    			$qb->expr()->eq('m.codModulo'	, ':codModulo')
    	))
    	->setParameter('codUsuario'	, $system->getCodUsuario())
    	->setParameter('codModulo'	, $codModulo);
    
    	$query 	= $qb->getQuery();
    	$return = $query->getSingleScalarResult();
    
    	if ($return > 0) return true;
    	 
    	return false;
    }
    
    /**
     * @return the $codEmpresa
     */
    public function getCodEmpresa() {
    	return $this->codEmpresa;
    }
    
    /**
     * @param int $codEmpresa
     */
    public function setCodEmpresa($codEmpresa) {
    	$this->codEmpresa = $codEmpresa;
    }
    
    /**
     * @return the $codMatriz
     */
    public function getCodMatriz() {
    	return $this->codMatriz;
    }
    
    /**
     * @param int $codMatriz
     */
    public function setCodMatriz($codMatriz) {
    	$this->codMatriz = $codMatriz;
    }
    
    /**
     * @return the $codLang
     */
    public function getCodLang() {
    	return $this->codLang;
    }
    
    /**
     * @param int $codLang
     */
    public function setCodLang($codLang) {
    	$this->codLang = $codLang;
    }
    
    /**
     * @return the $codOrganizacao
     */
    public function getCodOrganizacao() {
    	return $this->codOrganizacao;
    }
    
    /**
     * @param int $codOrganizacao
     */
    public function setCodOrganizacao($codOrganizacao) {
    	$this->codOrganizacao = $codOrganizacao;
    }
    
}
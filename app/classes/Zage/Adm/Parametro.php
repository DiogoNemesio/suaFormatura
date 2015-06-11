<?php

namespace Zage\Adm;

/**
 * Parâmetros do sistema
 * 
 * @package: Parametro
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Parametro {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($parametro = null) {
		global $db;
		
    	return (
    		$db->extraiTodos("
				SELECT	P.*
				FROM	ZGADM_PARAMETRO P
				WHERE	P.PARAMETRO LIKE '%".$parametro."%'
				ORDER	BY PARAMETRO
			")
   		);
    }
    

    /**
	 * Salva o valor de um parâmetro
     */
    public function salva($parametro,$valor) {
		global $log,$db;
		$log->debug("Parametro: ".$parametro.' Valor: '.$valor);
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE ZGADM_PARAMETRO P SET P.VALOR = ? WHERE PARAMETRO = ?",
				array($valor,$parametro)
			);
			$db->con->commit();
			return null;
		}catch (\Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
     * Resgata o valor do parâmetro
     *
     * @param varchar $parametro
     * @return array
     */
    public static function getValor ($parametro,$indValorPadrao = false) {
		global $system,$em;
		
		#################################################################################
		## Buscar os parâmetros na seguinte ordem:
		##
		## 1 -> Parâmetro do sistema
		## 2 -> Parâmetro por Organização
		## 3 -> Parâmetro por Usuário
		#################################################################################
		
		
		#################################################################################
		## Busca o parâmetro do sistema
		#################################################################################
		$valor	= self::getValorSistema($parametro,$indValorPadrao);
		
		if ($valor !== false)	return $valor;
   	
		#################################################################################
		## Busca o parâmetro por Organização
		#################################################################################
		$valor	= self::getValorOrganizacao($parametro,$system->getCodOrganizacao(),$indValorPadrao);
		
		if ($valor !== false)	return $valor;
		
		#################################################################################
		## Busca o parâmetro por Usuário
		#################################################################################
		$valor	= self::getValorUsuario($parametro,$system->getCodUsuario(),$indValorPadrao);
		
		return $valor;
		
    }
    
    
    /**
     * Resgata o valor de um parâmetro do sistema
     * @param string $parametro
     */
    public static function getValorSistema ($parametro,$indValorPadrao = true) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro do sistema
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('ps')
    		->from('\Entidades\ZgadmParametroSistema','ps')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'	, ':parametro')
    		))
    		->setParameter('parametro', $parametro);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    			
    	    if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    
    
    /**
     * Resgata o valor de um parâmetro por organização
     * @param string $parametro
     */
    public static function getValorOrganizacao ($parametro,$codOrganizacao,$indValorPadrao = false) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro por organização
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('po')
    		->from('\Entidades\ZgadmParametroOrganizacao','po')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'po.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'		, ':parametro'),
    			$qb->expr()->eq('po.codOrganizacao'	, ':codOrganizacao')
    		))
    		->setParameter('parametro'		, $parametro)
    		->setParameter('codOrganizacao'	, $codOrganizacao);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    		 
			if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    

    /**
     * Resgata o valor de um parâmetro por usuário
     * @param string $parametro
     */
    public static function getValorUsuario ($parametro,$codUsuario,$indValorPadrao = false) {
    	global $system,$em;
    
    	#################################################################################
    	## Busca o parâmetro por Usuário
    	#################################################################################
    	$qb 	= $em->createQueryBuilder();
    
    	try {
    		$qb->select('pu')
    		->from('\Entidades\ZgadmParametroUsuario','pu')
    		->leftJoin('\Entidades\ZgappParametro'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'pu.codParametro 	= p.codigo')
    		->where($qb->expr()->andX(
    			$qb->expr()->eq('p.parametro'		, ':parametro'),
    			$qb->expr()->eq('pu.codUsuario'		, ':codUsuario')
    		))
    		->setParameter('parametro'		, $parametro)
    		->setParameter('codUsuario'		, $codUsuario);
    
    		$query 		= $qb->getQuery();
    		$return 	= $query->getOneOrNullResult();
    		 
    	    if ($return) {
    			return $return->getValor();
    		}elseif ($indValorPadrao == true) {
	    			return self::getValorPadrao($parametro);
   			}else{
   				return false;
    		}
    		
    		 
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    

    /**
     * Resgata o valor padrão de um parâmetro
     * @param string $parametro
     */
    public static function getValorPadrao ($parametro) {
    	global $system,$em;
    
    	try {
	    	$oParametro	= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('parametro' => $parametro));
	    
			if (!$oParametro) {
				return false;
			}else{
				return $oParametro->getValorPadrao();
			}

    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    }
    
    /**
     * Gerar o código HTML do parametros
     * @param integer $codigo
     * @param integer $codOrganizacao
     * @param integer $codUsuario
     * @param integer $tabIndex
     */
    public static function geraHtml($codigo,$codOrganizacao,$codUsuario, $tabIndex = -1) {
    	global $em,$log,$system;
    
    	#################################################################################
    	## Resgata as informações do parametro no APP
    	#################################################################################
    	$info		= $em->getRepository('Entidades\ZgappParametro')->findOneBy(array('codigo' => $codigo));
    	 
    	if (!$info) {
    		return null;
    	}
    	
    	#################################################################################
    	## Resgatar o valor já salvo
    	#################################################################################
    	if ($info->getCodUso()->getCodigo() == "S") {
    		$valor		= self::getValorSistema($info->getParametro(),true);
    	}elseif ($info->getCodUso()->getCodigo() == "O") {
    		$valor		= self::getValorOrganizacao($info->getParametro(),$codOrganizacao,true);
    	}elseif ($info->getCodUso()->getCodigo() == "U") {
    		$valor		= self::getValorUsuario($info->getParametro(),$codUsuario,true);
    	}else{
    		die('Uso de parâmetro desconhecido');
    	}
    
    	#################################################################################
    	## Montar as tags
    	#################################################################################
    	$nomeCampo		= self::geraNomeInput($codigo);
    	$idCampo		= self::geraIdInput($codigo);
    	$tipo			= $info->getCodTipo()->getCodigo();
    	 
    	($info->getIndObrigatorio() == 0) 	?	$tagRequired	= null : $tagRequired 	= 'required';
    	 
    	#################################################################################
    	## Montagem das tags Fixas
    	#################################################################################
    	//$tagDateFormat	= $system->config["data"]["maskDateFormat"];
    	 
    	#################################################################################
    	## Montar as tags das máscaras
    	#################################################################################
    	if ($info->getCodTipo()->getCodMascara()) {
    		$tagMask	= ' zg-data-toggle="mask" zg-data-mask="'.$info->getCodTipo()->getCodMascara()->getNome().'" zg-data-mask-retira="'.$info->getCodTipo()->getCodMascara()->getIndRetiraMascara().'"';
    	}else{
    		$tagMask	= " ";
    	}
    
    	#################################################################################
    	## Montar o html de acordo com o tipo do Parâmetro
    	#################################################################################
    	$htmlInput		= "";
    
    	if ($tipo == 'T') { # Texto
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
    	}elseif ($tipo == 'N') { # Número
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
    	}elseif ($tipo == 'DT') { # Data
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control datepicker" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
    	}elseif ($tipo == 'LIS') { # Lista Pré-Definida
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<select tabindex="'.$tabIndex.'" class="select2 " style="width:100%;" id="'.$idCampo.'" name="'.$nomeCampo.'" '.$tagRequired.' data-rel="select2">';
    
    		#################################################################################
    		## Resgatar os valores da combo
    		#################################################################################
    		try {
    			$aValores	= $em->getRepository('Entidades\ZgappParametroTipoValor')->findBy(array('codParametro' => $info->getCodigo()),array('valor' => 'ASC'));
    			$oValores	= $system->geraHtmlCombo($aValores,	'VALOR', 'VALOR', $valor, null);
    		} catch (\Exception $e) {
    			\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
    		}
    
    		$htmlInput	.= $oValores;
    		$htmlInput	.= '</select>';
    	}elseif ($tipo == 'DIN') { # Dinheiro
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="width-100" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
    	}elseif ($tipo == 'SN') { # Sim ou Não
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<select tabindex="'.$tabIndex.'" class="select2" style="width:100%;" id="'.$idCampo.'" name="'.$nomeCampo.'" '.$tagRequired.' data-rel="select2">';
    
    		#################################################################################
    		## Resgatar os valores da combo SN
    		#################################################################################
    		$oValores	= $system->geraHtmlComboSN($valor, null);
    
    		$htmlInput	.= $oValores;
    		$htmlInput	.= '</select>';
    	}elseif ($tipo == 'P') { # Porcentagem
    		$htmlInput	.= '<span class="input-group-addon"><i class="ace-icon fa fa-question-circle" data-rel="popover" data-placement="top" data-trigger="hover" data-original-title="<i class='.'ace-icon fa fa-question-circle red'.'></i> Ajuda" data-content="'.$info->getDescricao().'"></i></span>';
    		$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'"  value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
    	}
    
    	return $htmlInput;
    }
    
    /**
     * Gerar o ID do campo
     * @param integer $codParametro
     * @param integer$codigo
     * @return string
     */
    public static function geraIdInput($codigo) {
    	return '_zgParametro_'.$codigo.'ID';
    }
    
    /**
     * Gerar o Nome do campo
     * @param integer $codParametro
     * @param integer$codigo
     * @return string
     */
    public static function geraNomeInput($codigo) {
    	return '_zgParametro['.$codigo.']';
    }
    

}
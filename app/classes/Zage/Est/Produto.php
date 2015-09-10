<?php

namespace Zage\Est;


/**
 * Estoque
 *
 * @package Grupo
 * @author Diogo Nemésio
 * @version 1.0.1
 */
class Produto extends \Entidades\ZgestProduto {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
     * Gerar o código HTML do índice
     * @param unknown $codIndice
     */
    public static function geraHtml($codigo,$codDocumento = null,$tabIndex = -1) {
    	global $em,$log,$system;

    	#################################################################################
    	## Resgata as informações do índice 
    	#################################################################################
    	/*$info		= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('codigo' => $codigo));
    	
    	if (!$info) {
    		return null;
    	}*/
    	
    	#################################################################################
    	## Resgatar o valor já salvo
    	#################################################################################
    	if ($codDocumento !== null) {
    		$info		= $em->getRepository('Entidades\ZgestSubgrupoConf')->findOneBy(array('codSubgrupo' => $codDocumento));
    	}else{
    		$info		= null;
    	}

    	/*if (empty($valor)) {
    		$valor	= $info->getValorPadrao();
    	}else{
    		$valor 	= $valor->getValor();
    	}*/
    	 
    	#################################################################################
    	## Montar as tags
    	#################################################################################
    	$nomeCampo		= self::geraNomeInput($codigo);
    	$idCampo		= self::geraIdInput($codigo);
    	$tipo			= $info->getCodTipo()->getCodigo();
    	
    	($info->getTamanho() == 0) 			? 	$tagMaxLen		= null : $tagMaxLen 	= 'maxlength="'.$info->getTamanho().'"';
    	($info->getIndObrigatorio() == 0) 	?	$tagRequired	= null : $tagRequired 	= 'required';
    	
    	#################################################################################
    	## Montagem das tags Fixas
    	#################################################################################
    	//$tagDateFormat	= $system->config["data"]["maskDateFormat"];
    	
		#################################################################################
    	## Montar as tags das máscaras
    	## A Precendência é na seguinte ordem:
    	## 1 -> a Máscara do Índice
    	## 2 -> a Máscara do Tipo do Índice
    	#################################################################################
		/*if ($info->getMascara()) {
			$tagMask	= ' zg-data-toggle="mask" zg-data-mask="'.$info->getMascara().'" zg-data-mask-retira="0"';
		}elseif ($info->getCodTipo()->getCodMascara()) {
			$tagMask	= ' zg-data-toggle="mask" zg-data-mask="'.$info->getCodTipo()->getCodMascara()->getNome().'" zg-data-mask-retira="'.$info->getCodTipo()->getCodMascara()->getIndRetiraMascara().'"';
		}else{
			$tagMask	= " ";
		}*/
    	 
		#################################################################################
		## Montar o html de acordo com o tipo do índice
		#################################################################################
		$htmlInput		= "";
		
		if ($tipo == 'T') { # Texto
			$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" '.$tagMaxLen.' value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
			$htmlInput	.= '<span class="input-group-addon"><a href="#" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$info->getDescricao().'"><i class="fa fa-question-circle"></i></a></span>';
		}elseif ($tipo == 'N') { # Número
			$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" '.$tagMaxLen.' value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
			$htmlInput	.= '<span class="input-group-addon"><a href="#" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$info->getDescricao().'"><i class="fa fa-question-circle"></i></a></span>';
		}elseif ($tipo == 'DT') { # Data
			$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control datepicker" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" '.$tagMaxLen.' value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
			$htmlInput	.= '<span class="input-group-addon"><a href="#" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$info->getDescricao().'"><i class="fa fa-question-circle"></i></a></span>';
		}elseif ($tipo == 'LI') { # Lista Pré-Definida
			$htmlInput	.= '<select tabindex="'.$tabIndex.'" class="select2 " style="width:100%;" id="'.$idCampo.'" name="'.$nomeCampo.'" '.$tagRequired.' data-rel="select2">';
		
			#################################################################################
			## Resgatar os valores da combo
			#################################################################################
			try {
				$aValores	= $em->getRepository('Entidades\ZgestSubgrupoConfTipo')->findBy(array('codigo' => $info->getCodTipo()->getCodigo()),array('descricao' => 'ASC'));
				$oValores	= $system->geraHtmlCombo($aValores,	'CODIGO', 'DESCRICAO', $valor, null);
			} catch (\Exception $e) {
				\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
			}
		
			$htmlInput	.= $oValores;
			$htmlInput	.= '</select>';
		}elseif ($tipo == 'DIN') { # Dinheiro
			$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
			$htmlInput	.= '<span class="input-group-addon"><a href="#" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$info->getDescricao().'"><i class="fa fa-question-circle"></i></a></span>';
		}elseif ($tipo == 'SN') { # Sim ou Não
			$htmlInput	.= '<select tabindex="'.$tabIndex.'" class="select2" style="width:100%;" id="'.$idCampo.'" name="'.$nomeCampo.'" '.$tagRequired.' data-rel="select2">';
		
			#################################################################################
			## Resgatar os valores da combo SN
			#################################################################################
			$oValores	= $system->geraHtmlComboSN($valor, null);
		
			$htmlInput	.= $oValores;
			$htmlInput	.= '</select>';
		}elseif ($tipo == 'P') { # Porcentagem
			$htmlInput	.= '<input tabindex="'.$tabIndex.'" class="form-control" id="'.$idCampo.'" type="text" name="'.$nomeCampo.'" '.$tagMaxLen.' value="'.$valor.'" '.$tagRequired.' '.$tagMask.' autocomplete="off">';
			$htmlInput	.= '<span class="input-group-addon"><a href="#" data-container="body" data-toggle="popover" data-placement="top" data-content="'.$info->getDescricao().'"><i class="fa fa-question-circle"></i></a></span>';
		}
		
		return $htmlInput;
		
    	
    }
    
    /**
     * Gerar o ID do campo
     * @param integer $codDocumento
     * @param integer$codigo
     * @return string
     */
    public static function geraIdInput($codigo) {
    	//return 'zgIndice_'.$codDocumento.'_'.$codigo.'ID';
    	return '_zgIndice_'.$codigo.'ID';
    }

    /**
     * Gerar o Nome do campo
     * @param integer $codDocumento
     * @param integer$codigo
     * @return string
     */
    public static function geraNomeInput($codigo) {
    	return '_zgIndice['.$codigo.']';
    	//return 'zgIndice_'.$codDocumento.'['.$codigo.']';
    }
    
}


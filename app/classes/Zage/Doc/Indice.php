<?php

namespace Zage\Doc;


/**
 * Indice
 *
 * @package Indice
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Indice extends \Entidades\ZgdocIndice {

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
     * Lista Índices de um Tipo de Documento
     */
    public static function lista ($codTipoDoc) {
    	global $em,$system;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
	    	$qb->select('i')
	    	->from('\Entidades\ZgdocIndice','i')
	    	->where($qb->expr()->andX(
	    		$qb->expr()->eq('i.codDocumentoTipo'	, ':codDocumentoTipo')
	    	))
	    	->orderBy('i.nome', 'ASC')
	    	->setParameter('codDocumentoTipo', $codTipoDoc);
	    	 
	    	$query 		= $qb->getQuery();
	    	return($query->getResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	
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
    	$info		= $em->getRepository('Entidades\ZgdocIndice')->findOneBy(array('codigo' => $codigo));
    	
    	if (!$info) {
    		return null;
    	}
    	
    	#################################################################################
    	## Resgatar o valor já salvo
    	#################################################################################
    	if ($codDocumento !== null) {
    		$valor		= $em->getRepository('Entidades\ZgdocIndiceValor')->findOneBy(array('codDocumento' => $codDocumento,'codIndice' => $codigo));
    	}else{
    		$valor		= null;
    	}

    	if (empty($valor)) {
    		$valor	= $info->getValorPadrao();
    	}else{
    		$valor 	= $valor->getValor();
    	}
    	 
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
		if ($info->getMascara()) {
			$tagMask	= ' zg-data-toggle="mask" zg-data-mask="'.$info->getMascara().'" zg-data-mask-retira="0"';
		}elseif ($info->getCodTipo()->getCodMascara()) {
			$tagMask	= ' zg-data-toggle="mask" zg-data-mask="'.$info->getCodTipo()->getCodMascara()->getNome().'" zg-data-mask-retira="'.$info->getCodTipo()->getCodMascara()->getIndRetiraMascara().'"';
		}else{
			$tagMask	= " ";
		}
    	 
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
		}elseif ($tipo == 'LIS') { # Lista Pré-Definida
			$htmlInput	.= '<select tabindex="'.$tabIndex.'" class="select2 " style="width:100%;" id="'.$idCampo.'" name="'.$nomeCampo.'" '.$tagRequired.' data-rel="select2">';
		
			#################################################################################
			## Resgatar os valores da combo
			#################################################################################
			try {
				$aValores	= $em->getRepository('Entidades\ZgdocIndiceTipoValor')->findBy(array('codIndice' => $info->getCodigo()),array('valor' => 'ASC'));
				$oValores	= $system->geraHtmlCombo($aValores,	'VALOR', 'VALOR', $valor, null);
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

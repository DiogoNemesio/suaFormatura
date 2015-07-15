<?php

namespace Zage\App\Grid\Tipo;

/**
 * Gerenciar os grids em bootstrap
 *
 * @package \Zage\App\Grid\Tipo\Bootstrap
 * @created 30/10/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Bootstrap extends \Zage\App\Grid\Tipo {
	
	/**
	 * Endereço do arquivo de Linguagem
	 *
	 * @var string
	 */
	private $langUrl;
	
	/**
	 * Caminho do arquivo de Linguagem
	 *
	 * @var string
	 */
	private $langPath;
	
	
	/**
	 * Fazer uso de filtros
	 */
	private $filtro;
	
	
	
	/**
	 * Construtor
	 */
	public function __construct($nome) {
		
		parent::__construct($nome);

		/**
		 * Por padrão não fará paginação
		 */
		$this->setPagingType ( self::PG_BOOTSTRAP );
		
		/**
		 * Define o tipo padrão sendo Bootstrap
		 */
		$this->setTipo($this::TP_BOOTSTRAP);
		
		/**
		 * Linguagem padrão
		 */
		$this->setLangUrl ( PKG_URL		. "/zage/lang/pt_BR.txt" );
		$this->setLangPath( PKG_PATH 	. "/zage/lang/pt_BR.txt" );
		
		/**
		 * Definir como padrão o uso de filtros
		 */
		$this->setFiltro(1);

		/**
		 * Definir como padrão para mostrar as informações
		 */
		$this->setMostraInfo(1);
	}
	
	/**
	 * Gera o HTML
	 */
	protected function geraHTML() {
		
		/**
		 * Inicializa o arquivo html *
		 */
		$this->html = $this->getNL () . '<table id="' . $this->getId () . '" class="table table-condensed table-hover table-striped table-bordered bootstrap-datatable datatable display ">' . $this->getNL ();
		$this->html .= '<thead><tr>' . $this->getNL ();
		$htmlHead		= "";
		$htmlFoot		= "";
		
		/**
		 * Monta o cabeçalho
		 */
		for($i = 0; $i < $this->getNumColunas (); $i ++) {
			/**
			 * Verifica o alinhamento *
			 */
			$alinhamento = "text-align:center;";
			/**
			 * Verifica se a coluna está ativa *
			 */
			if ($this->colunas [$i]->getAtiva () == true) {
				
				if ($this->colunas [$i]->getTipo () == self::TP_CHECKBOX) {
					$htmlHead		= $this->getNL () . '<th class="center no-sort" data-bSortable="false"><label class="position-relative"><input type="checkbox" name="'.$this->colunas [$i]->getNome ().'_all" class="ace" value="all" /><span class="lbl"></span></label></th>' . $this->getNL ();
				}else{
					$htmlHead		= $this->getNL () . '<th style="width:' . $this->colunas [$i]->getTamanho () . '%; ' . $alinhamento . '">' . $this->colunas [$i]->getNome () . '</th>' . $this->getNL ();
				}
				
				$this->html		.= $htmlHead;
				
				if ($this->colunas [$i]->getIndFiltro() == true) {
					$htmlFoot	.= $htmlHead;
				}else{
					$htmlFoot	.= $htmlHead;
				}
				
			}
		}
		
		$this->html .= '</tr></thead>' . $this->getNL ();
		/*$this->html .= '<tfoot><tr>' . $this->getNL ();
		$this->html .= $htmlFoot;
		$this->html .= '</tr></tfoot>' . $this->getNL ();
		*/
		
		
		
		$this->html .= '<tbody>' . $this->getNL ();
		
		/**
		 * Faz o loop nas linhas *
		 */
		for($i = 0; $i < $this->getNumLinhas (); $i ++) {
			
			/**
			 * Verifica se a linha está ativa *
			 */
			if ($this->linhas [$i]->getAtiva () == true) {
				
				/**
				 * Adiciona a Tag de inicialização de registro *
				 */
				$this->html .= $this->getTAB () . "<tr>" . $this->getNL ();
				
				/**
				 * Faz o loop nas celulas da linha *
				 */
				for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
					
					/**
					 * Verifica se a coluna está ativa *
					 */
					if ($this->colunas [$j]->getAtiva () == true) {
						
						/**
						 * Alinhamento *
						 */
						switch ($this->colunas [$j]->getAlinhamento ()) {
							case self::LEFT :
								$alinhamento = "text-align: left;";
								break;
							case self::CENTER :
								$alinhamento = "text-align: center;";
								break;
							case self::RIGHT :
								$alinhamento = "text-align: right;";
								break;
							default :
								$alinhamento = "text-align: center;";
								break;
						}
						
						/**
						 * Verifica se a célula está ativa *
						 */
						if ($this->celulas [$i] [$j]->getAtiva () == true) {
							if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (), $this->celulas [$i] [$j]->getEnderecoImagem () , $this->celulas [$i] [$j]->getUrl() ) . "</td>" . $this->getNL ();
							}elseif ($this->colunas [$j]->getTipo () == self::TP_MOEDA) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( \Zage\App\Util::to_money($this->celulas [$i] [$j]->getValor ()) ) . "</td>" . $this->getNL ();
							}elseif ($this->colunas [$j]->getTipo () == self::TP_DATA) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( \Zage\App\Util::toDate($this->celulas [$i] [$j]->getValor ()) ) . "</td>" . $this->getNL ();
							}elseif ($this->colunas [$j]->getTipo () == self::TP_BOTAO) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (),$this->celulas [$i] [$j]->getUrl() ) . "</td>" . $this->getNL ();
							}elseif ($this->colunas [$j]->getTipo () == self::TP_ICONE) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (),$this->celulas [$i] [$j]->getUrl(), $this->celulas [$i] [$j]->getIcone(), $this->celulas [$i] [$j]->getDescricao() ) . "</td>" . $this->getNL ();
							}elseif ($this->colunas [$j]->getTipo () == self::TP_CHECKBOX) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ($this->colunas [$j]->getNome () , $this->celulas [$i] [$j]->getValor ()) . "</td>" . $this->getNL ();
							} else {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () ) . "</td>" . $this->getNL ();
							}
						} else {
							$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">&nbsp;</td>" . $this->getNL ();
						}
					} else {
						// $this->html .= $this->getTAB() . $this->getTAB() . "<td>&nbsp;</td>" . $this->getNL();
					}
				}
				
				/**
				 * Adiciona a Tag de finalização de registro *
				 */
				$this->html .= $this->getTAB () . "</tr>" . $this->getNL ();
			}
		}
		
		/**
		 * Verifica se foi passado o parâmetro para fazer processamento no servidor *
		 */
		if ($this->getServerSideUrl () == '') {
			$ssCode = '';
		} else {
			$ssCode = '"bProcessing": true,
        			"bServerSide": true,
        			"sAjaxSource": "' . $this->getServerSideUrl () . '",';
		}
		
		if ($this->getMostraInfo()) {
			$ssCode	.= '"bInfo": true, ';
		}else{
			$ssCode	.= '"bInfo": false, ';
		}
		
		
		if ($this->getFiltro()) {
			$ssCode	.= '"bFilter": true, ';
		}else{
			$ssCode	.= '"bFilter": false, ';
		}
		
		
		/**
		 * Verifica o tipo de paginação *
		 */
		switch ($this->getPagingType ()) {
			case self::PG_NONE :
				$sPaging = "\"bPaginate\": false,\"paging\": false,";
				break;
			case self::PG_BOOTSTRAP :
				$sPaging = "\"pagingType\"	: \"full_numbers\",";
				break;
			default :
				\Zage\App\Erro::halt ( 'Tipo de paginação desconhecida !!!' );
				break;
		}
		
		/**
		 * Verifica o arquivo de linguagem *
		 */
		if ($this->getLangUrl () != '') {
			/*$lang = '"oLanguage"			: {
						"sUrl": "' . $this->getLangUrl () . '"
					}, ';
			*/
			$lang = '"oLanguage"			:  ' . \Zage\App\Util::getConteudoArquivo($this->getLangPath()) . ', ';
		} else {
			$lang = "";
		}
				
		/**
		 * Verifica se é pra mostrar a barra de exportação
		 */
		if ($this->getMostraBarraExportacao() != false) {
			$be 	=	' 		var tableTools = new $.fn.dataTable.TableTools( table, {
			"sSwfPath": "'.PKG_URL.'/TableTools-2.2.3/swf/copy_csv_xls_pdf.swf",
	        "buttons": [
	            "Copiar",
	            "csv",
	            "xls",
				"pdf",
	            "Imprimir"
	        ]
	    	} );
	    	$( tableTools.fnContainer() ).insertBefore(\'#' . $this->getId () . '\');
	    		';
		}else{
			$be		= null;
		}
		
		$this->html .= "</tbody></table>" . $this->getNL ();
		$this->html .= "<script>" . $this->getNL ();
		$this->html .= '$(document).ready(function() {
		
		var columnSort = new Array;		
		$(\'#' . $this->getId () . '\').each(function(){
    		$(this).find("thead th").each(function(){
        		if($(this).attr("data-bSortable") == "false") {
					columnSort.push({ "bSortable": false });
        		} else {
            		columnSort.push({ "bSortable": true });
        		}
	    	});
		});
				
				
		var table = $(\'#' . $this->getId () . '\').dataTable( {
			bAutoWidth: false,
			aaSorting: [],
			"aoColumns": columnSort,
			"sDom": \'<"row"<"col-xs-3"<"dataTables_length"l>><"col-xs-6"<"#zgGridCustomHeaderID">><"col-xs-3"<"dataTables_filter"f>>><t><"row"<"col-xs-6"<"dataTables_info"i>><"col-xs-6"<"dataTables_paginate"p>>>\',
			' . $sPaging . '
			' . $ssCode . '
			' . $lang . '
		} );
		
		'.$be.'
			
			table.on("change", "th input:checkbox" , function(){
				var thCheck 		= $(this).is(":checked");
				if (thCheck) {
					var filteredRows 	= table.$("input:checkbox", { "filter": "applied" });
					filteredRows.each( function() {
						$(this).prop("checked", true);
					});
				}else{
					$("input:checkbox", table.fnGetNodes()).each( function() {
						$(this).prop("checked", false);
					});
				}
			});
					
			table.on("draw.dt", function () {
				$(\'[data-toggle="tooltip"]\').tooltip({html:true});
				$(\'.chosen-select\').chosen({allow_single_deselect:true});
				$(\'[data-rel=tooltip]\').tooltip();
				$(\'[data-rel=popover]\').popover({html:true});
				$(\'.datepicker\').datepicker({"autoclose": true});
			});
				
					

	    });

		
		';
		$this->html .= "</script>" . $this->getNL ();
	}
	
	
	/**
	 * Resgatar o código Json dos dados
	 * @param unknown $echo
	 * @param unknown $numRegTotal
	 * @param unknown $inicio
	 * @param unknown $tamanho
	 */
	public function getJsonData($echo, $numRegTotal, $inicio, $tamanho) {
		
		$output = array (
			"sEcho" => intval ( $echo ),
			"iTotalRecords" => $numRegTotal,
			"iTotalDisplayRecords" => $this->getNumLinhas (),
			"aaData" => array ()
		);
		
		if ($inicio == null)	$inicio = 0;
		if ($tamanho == null)	$tamanho = 10;
		
		$t = ($inicio + $tamanho);
		
		if ($t > $this->getNumLinhas ()) {
			$t = $this->getNumLinhas ();
		}
		
		/**
		 * Faz o loop nas linhas do intervalo
		 */
		for($i = $inicio; $i < $t; $i ++) {
			/**
			 * Verifica se a linha está ativa
			 */
			if ($this->linhas [$i]->getAtiva () == true) {
				
				$row = array ();
				
				/**
				 * Faz o loop nas celulas da linha *
				 */
				for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
					
					/**
					 * Verifica se a coluna está ativa
					 */
					if ($this->colunas [$j]->getAtiva () == true) {
						/**
						 * Alinhamento
						 */
						switch ($this->colunas [$j]->getAlinhamento ()) {
							case self::LEFT :
								$alinhamento = "text-align: left;";
								break;
							case self::CENTER :
								$alinhamento = "text-align: center;";
								break;
							case self::RIGHT :
								$alinhamento = "text-align: right;";
								break;
							default :
								$alinhamento = "text-align: center;";
								break;
						}
						
						/**
						 * Verifica se a célula está ativa
						 */
						if ($this->celulas [$i] [$j]->getAtiva () == true) {
							if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (), $this->celulas [$i] [$j]->getEnderecoImagem () , $this->celulas [$i] [$j]->getUrl() )."</div>";
							}elseif ($this->colunas [$j]->getTipo () == self::TP_MOEDA) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( \Zage\App\Util::to_money($this->celulas [$i] [$j]->getValor ()) ) . "<div>";
							}elseif ($this->colunas [$j]->getTipo () == self::TP_DATA) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( \Zage\App\Util::toDate($this->celulas [$i] [$j]->getValor ()) )."</div>";
							}elseif ($this->colunas [$j]->getTipo () == self::TP_BOTAO) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (),$this->celulas [$i] [$j]->getUrl() ) ."</div>";
							}elseif ($this->colunas [$j]->getTipo () == self::TP_ICONE) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (),$this->celulas [$i] [$j]->getUrl(), $this->celulas [$i] [$j]->getIcone() )  ."</div>";
							}elseif ($this->colunas [$j]->getTipo () == self::TP_CHECKBOX) {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ($this->colunas [$j]->getNome () , $this->celulas [$i] [$j]->getValor ())  ."</div>";
							} else {
								$row [] = "<div style=\"" . $alinhamento . "\">".$this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () ) . "<div>";
							}
						} else {
							$row [] = "<div style=\"" . $alinhamento . "\">&nbsp;<div>";
						}
						
					}
				}
				$output ['aaData'] [] = $row;
			}
		}
		return json_encode ( $output );
	}
	
	
	
	/**
	 *
	 * @return the $langUrl
	 */
	protected function getLangUrl() {
		return $this->langUrl;
	}
	
	/**
	 *
	 * @param string $langUrl        	
	 */
	protected function setLangUrl($langUrl) {
		$this->langUrl = $langUrl;
	}
	
	/**
	 *
	 * @return the $langPath
	 */
	protected function getLangPath() {
		return $this->langPath;
	}
	
	/**
	 *
	 * @param string $langPath
	 */
	protected function setLangPath($langPath) {
		$this->langPath = $langPath;
	}
	
	
	/**
	 * @return the $filtro
	 */
	public function getFiltro() {
		return $this->filtro;
	}

	/**
	 * @param field_type $filtro
	 */
	public function setFiltro($filtro) {
		$this->filtro = $filtro;
	}


}

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
class DHTMLX extends \Zage\App\Grid\Tipo {
	
	private $colHeader;
	private $colId;
	private $colWidth;
	private $colType;
	private $colAlign;
	
	/**
	 * Skin
	 *
	 * @var string
	 */
	private $skin;
	
	/**
	 * XML
	 *
	 * @var string
	 */
	private $xml;
	
	/**
	 * Tipo de registro SUB_ROW
	 *
	 * @var string
	 */
	private $subrow;
	
	/**
	 * Cores não padrão das linhas
	 *
	 * @var string
	 */
	private $cores;
	
	/**
	 * Valores não padrão das linhas
	 *
	 * @var string
	 */
	private $valores;
	
	/**
	 * Indicador de Ajuste de Altura automático
	 *
	 * @var string
	 */
	private $autoHeight;
	
	/**
	 * Indicador de Ajuste de Largura automático
	 *
	 * @var string
	 */
	private $autoWidth;
	
	/**
	 * Indicar se o grid vai fazer paginação
	 *
	 * @var string
	 */
	private $paging;
	
	/**
	 * Fazer uso de filtros
	 */
	private $filtro;
	
	/**
	 * Configuração de split
	 * @var number
	 */
	private $split;
	
	/**
	 * Array de rodapés
	 * @var array
	 */
	private $rodapes;
	
	/**
	 * Configuração de agrupamento
	 * @var string
	 */
	private $agrupamento;
	
	/**
	 * Construtor
	 */
	public function __construct($nome) {
		
		parent::__construct($nome);

		/**
		 * Por padrão não fará paginação
		 */
		$this->setPagingType ( false );

		/** Por padrão não fará paginação **/
		$this->paging	= array(
				"ENABLE" 		=> true,
				"NUMLINHAS"		=> 0,
				"NUMPAGINAS"	=> 5,
				"DIVLINHAS"		=> '',
				"DIVPAGINAS"	=> ''
		);
		
		
		/**
		 * Define o tipo padrão sendo Bootstrap
		 */
		$this->setTipo($this::TP_DHTMLX);
		
		/**
		 * Define o skin padrão
		 */
		$this->setSkin("dhx_skyblue");
		
		/** Inicializando os arrays **/
		$this->cores	= array();
		$this->valores	= array();
		
		/** Definindo a string de subrow **/
		$this->subrow	= 'sub_row';
		
		
	}
	
	/**
	 * Gera o HTML
	 */
	protected function geraHTML() {
		
		/**
		 * Define as configurações das colunas
		 */
		$this->_setColConfig();
		$this->_geraXML();
		
		/**
		 * Inicializa o arquivo html
		 */
		$this->html = "<script>"																			. $this->getNL();
		$this->html.= "var ".$this->getNome().";"															. $this->getNL();
		$this->html.= $this->getNome()." = new dhtmlXGridObject('".$this->getNome()."');"					. $this->getNL();
		$this->html.= $this->getNome().".setHeader(\"".$this->getColHeader()."\");"							. $this->getNL();
		$this->html.= $this->getNome().".setColumnIds(\"".$this->getColId()."\");"							. $this->getNL();
		$this->html.= $this->getNome().".setImagePath(\"".PKG_URL."/dhtmlx/dhtmlxGrid/codebase/imgs/\");"	. $this->getNL();
		$this->html.= $this->getNome().".setInitWidths(\"".$this->getColWidth()."\");"						. $this->getNL();
		$this->html.= $this->getNome().".setColAlign(\"".$this->getColAlign()."\");	"						. $this->getNL();
		$this->html.= $this->getNome().".setColTypes(\"".$this->getColType()."\");"							. $this->getNL();
		$this->html.= $this->getNome().".setSkin(\"".$this->getSkin()."\");"								. $this->getNL();
		$this->html.= $this->getNome().".i18n.decimal_separator=\".\";"										. $this->getNL();
		$this->html.= $this->getNome().".i18n.group_separator=\",\";"										. $this->getNL();
		
		
		/** Gerar os valores das combos **/
		if (is_array($this->coValues)) {
			foreach ($this->coValues as $key => $value) {
				$this->html .= "var combo".$key." = ".$this->getNome().".getCombo(".$key.");"				. $this->getNL();
				foreach ($this->coValues[$key] as $valor) {
					$this->html	.= "combo".$key.".put('".$valor[0]."','".$valor[1]."');"					. $this->getNL();
				}
			}
		}
		
		/** Verifica as opções **/
		if ($this->getAutoHeight()) {
			$this->html .= "var scrollHeight	= %GRID_ALTURA%-70;"										. $this->getNL();
			$this->html .= $this->getNome().".enableAutoHeight(true,scrollHeight,true);"					. $this->getNL();
		}
		if ($this->getAutoWidth()) {
			$this->html .= "var scrollWidth	= %GRID_ALTURA%-70;"											. $this->getNL();
			$this->html .= $this->getNome().".enableAutoWidth(true);"										. $this->getNL();
		}
		
		if ($this->getPagingType()) {
			$this->html .= $this->getNome().'.i18n.paging={results:"Resultados",records:"De ",to:" Até ",page:"Página ",perpage:"Registros por Página",first:"Para a Primeira página", previous:"Página Anterior",found:"Registros encontrados",next:"Próxima página",last:"Para a última página",of:" de ", notfound:"Nenhum registro encontrado" };' . $this->getNL(); 
			$this->html .= $this->getNome().".enablePaging(true,".$this->paging["NUMLINHAS"].",".$this->paging["NUMPAGINAS"].",'".$this->paging["DIVLINHAS"]."',true);"																																													. $this->getNL();
			$this->html .= $this->getNome().".setPagingSkin('toolbar','".$this->getSkin()."');"				. $this->getNL();
		}
		
		
		if ($this->getFiltro()) {
			$this->html .= $this->getNome().".attachHeader(\"".$this->getFiltro()."\");"					. $this->getNL();
			//$script .= $this->getNome().".enableSmartRendering(true);     ";
		}
		
		
		$this->html .= $this->getNome().".init();" 															. $this->getNL();
		
		
		/** Criando os rodapés **/
		if ($this->rodapes) {
			foreach ($this->rodapes as $rodape) {
				if (isset($rodape["CONFIG"])) {
					$config = $rodape["CONFIG"];
				}else{
					$config	= '[]';
				}
				 
				$this->html .= $this->getNome().'.attachFooter('.$rodape["COLUNAS"].','.$config.');'	. $this->getNL();
			}
		}
		
		//$this->html .= $this->getNome().'.attachFooter("Total,2,3,4,5,6,7,#stat_total,9", ["text-align:left;,text-align:center;"]);'		. $this->getNL();
		
		
		if ($this->getSplit() && is_numeric($this->getSplit())) {
			$this->html .= $this->getNome().".splitAt(".$this->getSplit().");"								. $this->getNL();
		}
		
		for($i = 0; $i < $this->getNumColunas (); $i ++) {
			/** Verifica se a coluna está ativa  */
			if ($this->colunas [$i]->getAtiva () == true && $this->colunas [$i]->getTipo() == \Zage\App\Grid\Tipo::TP_MOEDA) {
				$this->html .= $this->getNome().".setNumberFormat(\"R$ 0,000.00\",".$i.");"				. $this->getNL();
			}
		}
		
		
		$this->html	.= "var xml = '".$this->xml."';"														. $this->getNL();
		$this->html	.= $this->getNome().".parse(xml,\"xml\");"												. $this->getNL();
		
		if ($this->agrupamento) {
			$this->html .= $this->getNome().'.groupBy('.$this->agrupamento.');' 							. $this->getNL();
			$this->html .= $this->getNome().'.collapseAllGroups();'				 							. $this->getNL();
		}
		//$this->html .= $this->getNome().'.groupBy(0,["#title","","#cspan","#cspan","#cspan","#cspan","#cspan","#stat_total","#stat_total"]);';
		
		/** Verifica se tem alguma linha com cor diferente **/
		foreach ($this->cores as $linha => $cor) {
			$this->html .= $this->getNL().$this->getNome().".setRowColor(".$linha.", '".$cor."');"			.$this->getNL();
		}
		
		/** Verifica se tem alguma linha com valor diferente **/
		foreach ($this->valores as $linha => $aLinha) {
			foreach ($aLinha as $coluna => $valor) {
				$this->html .= $this->getNL().$this->getNome().".cells(".$linha.",".$coluna.").setValue('".$valor."');".$this->getNL();
			}
		}
		
		$this->html .= '</script>'.$this->getNL();
		
		/** retorna o código javascript **/
		return ($this->html);
		
	}
	
	
	/**
	 * Gera o XML
	 */
	private function _geraXML () {
		$nl	= $this->getNL();
		$this->setNL("");
		
		/** Inicializa o arquivo XML **/
		$this->xml	=  "<?xml version=\"1.0\" encoding=\"".$this->getCharset()."\"?>" . $this->getNL();
		$this->xml	.= "<rows>".$this->getNL();
		
		/**
		 * Faz o loop nas linhas 
		 */
		for($i = 0; $i < $this->getNumLinhas (); $i ++) {
				
			/**
			 * Verifica se a linha está ativa
			 */
			if ($this->linhas [$i]->getAtiva () == true) {
		
				/**
				 * Adiciona a Tag de inicialização de registro 
				 */
				$this->xml	.= "<row id=\"".$i."\">".$this->getNL();
		
				/**
				 * Faz o loop nas celulas da linha *
				*/
				for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
						
					/**
					 * Verifica se a coluna está ativa *
					 */
					if ($this->colunas [$j]->getAtiva () == true) {
						/**
						 * Verifica se a célula está ativa
						 */
						if ($this->celulas [$i] [$j]->getAtiva () == true) {
							if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
								$this->xml	.= "<cell>" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () ) . "</cell>".$this->getNL();
							} else {
								$this->xml	.= "<cell>" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () ) . "</cell>".$this->getNL();
							}
						} else {
							$this->xml	.= "<cell></cell>".$this->getNL();
						}
					}
				}
		
				/** Adiciona a Tag de finalização de registro **/
				$this->xml	.= "</row>".$this->getNL();
			}
		}
		$this->xml	.= "</rows>".$this->getNL();
		$this->setNL($nl);
		
	}
	
	/**
	 * Gera a string do Header
	 */
	private function _setColConfig() {
		$header	= '';
		$ids	= '';
		$width	= '';
		$type	= '';
		$align	= '';
		
		/** Faz o loop nas colunas para pegar os nomes delas **/
		for($i = 0; $i < $this->getNumColunas (); $i ++) {
			
			/** Verifica se a coluna está ativa  */
			if ($this->colunas [$i]->getAtiva () == true) {
				$header	.= $this->colunas [$i]->getNome ()  		. ',';
				$ids	.= $this->colunas [$i]->getNomeCampo ()  	. ',';
				$width	.= $this->colunas [$i]->getTamanho ()  		. ',';
				$type	.= $this->colunas [$i]->getTipo ()  		. ',';
				$align	.= $this->colunas [$i]->getAlinhamento()  	. ',';
			}
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		$header	= substr($header,0,-1);
		$ids	= substr($ids,0,-1);
		$width	= substr($width,0,-1);
		$type	= substr($type,0,-1);
		$align	= substr($align,0,-1);
		
		$this->setColHeader($header);
		$this->setColId($ids);
		$this->setColWidth($width);
		$this->setColType($type);
		$this->setColAlign($align);
	}
	
	/**
	 * Configurar o grid para fazer paginação
	 */
	public function setPaging($numLinhas,$divLinhas,$divPaginas) {
		$this->paging["ENABLE"]         = true;
		$this->paging["NUMLINHAS"]      = $numLinhas;
		$this->paging["DIVLINHAS"]      = $divLinhas;
		$this->paging["DIVPAGINAS"]     = $divPaginas;
	}
	
	/**
	 * Adicionar um rodapé
	 * @param array $colunas
	 * @param array $config
	 */
	public function adicionaRodape(array $colunas,array $config = array()) {
		if (!is_array($colunas)) die('Parâmetro deve ser array em '.__FUNCTION__);
		
		$i = sizeof($this->rodapes);
		$this->rodapes[$i]["COLUNAS"] 	= '["'.implode('","',$colunas) . '"]';
		if ($config)	$this->rodapes[$i]["CONFIG"] 	= '["'.implode('","',$config). '"]';
	}
	
	/**
	 * Agrupar os dados do Grid
	 * @param int $coluna
	 * @param array $colunas
	 */
	public function agrupar ($coluna, array $colunas = array()) {
		
		$this->agrupamento	= $coluna;
		
		if (is_array($colunas)) {
			$this->agrupamento .= ',["'.implode('","', $colunas).'"]';
		}
		
		
	}
	
	/**
	 * Configurar o grid para fazer filtro
	 */
	public function setFiltro($filtro) {
		$this->filtro   = $filtro;
	}
	
	/**
	 * @return the $filtro
	 */
	private function getFiltro() {
		return $this->filtro;
	}
	
	
	/**
	 * @return the $colHeader
	 */
	private function getColHeader() {
		return $this->colHeader;
	}

	/**
	 * @param field_type $colHeader
	 */
	private function setColHeader($colHeader) {
		$this->colHeader = $colHeader;
	}

	/**
	 * @return the $colId
	 */
	private function getColId() {
		return $this->colId;
	}

	/**
	 * @param field_type $colId
	 */
	private function setColId($colId) {
		$this->colId = $colId;
	}

	/**
	 * @return the $colWidth
	 */
	private function getColWidth() {
		return $this->colWidth;
	}

	/**
	 * @param field_type $colWidth
	 */
	private function setColWidth($colWidth) {
		$this->colWidth = $colWidth;
	}

	/**
	 * @return the $colType
	 */
	private function getColType() {
		return $this->colType;
	}

	/**
	 * @param field_type $colType
	 */
	private function setColType($colType) {
		$this->colType = $colType;
	}
	
	/**
	 * @return the $colAlign
	 */
	private function getColAlign() {
		return $this->colAlign;
	}

	/**
	 * @param field_type $colAlign
	 */
	private function setColAlign($colAlign) {
		$this->colAlign = $colAlign;
	}
	
	/**
	 * @return the $skin
	 */
	private function getSkin() {
		return $this->skin;
	}

	/**
	 * @param string $skin
	 */
	private function setSkin($skin) {
		$this->skin = $skin;
	}
	
	/**
	 * @return the $xml
	 */
	private function getXml() {
		return $this->xml;
	}

	/**
	 * @param string $xml
	 */
	private function setXml($xml) {
		$this->xml = $xml;
	}

	/**
	 * @return the $subrow
	 */
	private function getSubrow() {
		return $this->subrow;
	}

	/**
	 * @param string $subrow
	 */
	private function setSubrow($subrow) {
		$this->subrow = $subrow;
	}

	/**
	 * @return the $cores
	 */
	private function getCores() {
		return $this->cores;
	}

	/**
	 * @param string $cores
	 */
	private function setCores($cores) {
		$this->cores = $cores;
	}

	/**
	 * @return the $valores
	 */
	private function getValores() {
		return $this->valores;
	}

	/**
	 * @param string $valores
	 */
	private function setValores($valores) {
		$this->valores = $valores;
	}

	/**
	 * @return the $autoHeight
	 */
	public function getAutoHeight() {
		return $this->autoHeight;
	}

	/**
	 * @param string $autoHeight
	 */
	public function setAutoHeight($autoHeight) {
		$this->autoHeight = $autoHeight;
	}

	/**
	 * @return the $autoWidth
	 */
	public function getAutoWidth() {
		return $this->autoWidth;
	}

	/**
	 * @param string $autoWidth
	 */
	public function setAutoWidth($autoWidth) {
		$this->autoWidth = $autoWidth;
	}

	/**
	 * @return the $split
	 */
	public function getSplit() {
		return $this->split;
	}

	/**
	 * @param number $split
	 */
	public function setSplit($split) {
		$this->split = $split;
	}


}

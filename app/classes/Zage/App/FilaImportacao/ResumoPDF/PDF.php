<?php
namespace Zage\App\FilaImportacao\ResumoPDF;

/**
 * @package: \Zage\App\FilaImportacao\ResumoPDF\PDF
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Geração do Resumo em PDF
 */

class PDF extends \ZendPdf\PdfDocument {
	
	/**
	 * Variável para guardar o cabeçalho
	 *
	 * @var array
	 */
	private $header;

	/**
	 * Variável para guardar o rodapé
	 *
	 * @var string
	 */
	private $footer;

	/**
	 * Número de páginas do documento
	 * 
	 * @var number
	 */
	private $numPages;
	
	/**
	 * Estilos do Documento
	 * 
	 * @var array
	 */
	private $styles;
	
	/**
	 * pageMargin
	 * 
	 * @var number
	 */
	private $pageMargin;

	/**
	 * tamanho da página
	 * 
	 * @var number
	 */
	private $w,$h;

	/**
	 * Página Atual
	 * 
	 * @var number
	 */
	public $actPage;

	/**
	 * Altura da Fonte
	 * 
	 * @var number
	 */
	private $fontHeight;

	/**
	 * Altura da Linha
	 * 
	 * @var number
	 */
	private $lineHeight;

	/**
	 * Distancia entre as linhas
	 * 
	 * @var number
	 */
	private $offset;
	
	/**
	 * Altura disponível na página atual
	 * 
	 * @var array
	 */
	private $availableHeight;

	/**
	 * Coordenada Y atual da página
	 * 
	 * @var number
	 */
	private $y;

	/**
	 * Estilo padrão
	 * 
	 * @var number
	 */
	private $defStyle;

	/**
	 * Largura da Linha
	 * 
	 * @var number
	 */
	public  $lineWidth;
	
	/**
	 * IndAgrupamento
	 * 
	 * @var unknown
	 */
	private $indAgrupamento;

	/**
	 * Construtor
	 */
	public function __construct($indAgrupamento) {
		
		global $system;
		
		/** Chama o construtor da classe mãe **/
		parent::__construct();
		
		/** Inicializa as variáveis **/
		$this->header			= '';
		$this->footer			= '';
		$this->numPages			= 0;
		$this->pageMargin		= 20;
		$this->w				= 595;
		$this->h				= 842;
		$this->offset			= 2;
		$this->defStyle			= "N";
		$this->lineWidth		= ($this->w - ($this->pageMargin * 2));
		
		/**
		 * Estilos
		 * 
		 * N - Texto normal 
		 * B - Texto maior e em negrito
		 */
		$this->styles				= array("TEXT","TITLE");
		$this->styles["N"]["STYLE"]	= new \ZendPdf\Style();
		$this->styles["B"]["STYLE"]	= new \ZendPdf\Style();
		
		
		$this->styles["N"]["STYLE"]->setLineColor(new \ZendPdf\Color\GrayScale(0.7));
		$this->styles["N"]["STYLE"]->setFont(
			\ZendPdf\Font::fontWithName(\ZendPdf\Font::FONT_HELVETICA),
			8
		);
		
		$this->styles["B"]["STYLE"]->setLineColor(new \ZendPdf\Color\GrayScale(0.7));
		$this->styles["B"]["STYLE"]->setFont(
			\ZendPdf\Font::fontWithName(\ZendPdf\Font::FONT_HELVETICA_BOLD),
			10
		);
		
		$this->styles["N"]["fontH"]	= $this->styles["N"]["STYLE"]->getFontSize();
		$this->styles["N"]["fontW"]	= 3;
		$this->styles["B"]["fontH"]	= $this->styles["B"]["STYLE"]->getFontSize();
		$this->styles["B"]["fontW"]	= 4;
		
		$this->styles["N"]["lineH"]	= $this->styles["N"]["fontH"] + ($this->offset * 2);
		$this->styles["B"]["lineH"]	= $this->styles["B"]["fontH"] + ($this->offset * 2);
		
		/** Salva a propriedade de agrupamento **/
		$this->indAgrupamento	= $indAgrupamento;
		
		/** Cria a primeira página **/
		$this->_addPage();
		
	}
	
	/**
	 * Adicionar uma nova página 
	 */
	public function _addPage() {
		global $system;
		
		$numPages				= sizeof($this->pages);
		$i						= $numPages + 1;
		$this->actPage			= $i;
		$this->pages[$i] 		= new \ZendPdf\Page(\ZendPdf\Page::SIZE_A4);
		$this->pages[$i]->setStyle($this->styles[$this->defStyle]["STYLE"]);

		/** Desenho o Retângulo Grande (Borda da página) **/
		$this->pages[$i]->drawRectangle(
			($this->w - $this->pageMargin),
			($this->h - $this->pageMargin),
			$this->pageMargin,
			$this->pageMargin,
			\ZendPdf\Page::SHAPE_DRAW_STROKE
		);
		
		/** Calcula a altura disponível **/
		$this->availableHeight	= $this->h - ($this->pageMargin * 2);
		
		/** Atualiza a coordenada y atual **/
		$this->y				= $this->h - ($this->pageMargin);
		
		//$system->log->debug->debug("_addPage: AvailableHeight = ".$this->availableHeight." this->y = ".$this->y);
      	
		/** Adiciona o cabecalho **/
		$this->_addHeader($i);
		
		/** Adiciona o cabecalho da tabela **/
		if ($this->indAgrupamento) {
			$this->addRow(
				array(
					array('Tipo',80,'C'),
					array('Posição',80,'C'),
					array('Tipo Registro',80,'C'),
					array('Linha(s)',200,'C'),
					array('Qtde',80,'C')
				),
				'B',
				'L',
				0,
				null
			);
				
		}else{
			$this->addRow(
				array(
					array('Tipo',100,'C'),
					array('Posição',100,'C'),
					array('Tipo Registro',100,'C'),
					array('Linha',100,'C'),
				),
				'B',
				'L',
				0,
				null
			);
		}
		
		$this->addLine();
		
		/** Adiciona o rodapé **/
		$this->_addFooter($i);
		
	}
	
	/**
	 * Adicionar o cabeçalho em uma página
	 */
	private function _addHeader($pageIndex) {
		global $system;
		
		$headerLines	= 3;
		$posHeaderLines	= 1;
		$imageSpace		= 24; // Porcentagem
		
		/** Desenha o 1 Retângulo do cabeçalho **/
		$this->pages[$pageIndex]->drawRectangle(
			($this->pageMargin),
			($this->h - $this->pageMargin),
			($this->w - $this->pageMargin),
			(($this->h - ($this->pageMargin + ($this->styles["N"]["lineH"] * $headerLines)))),
			\ZendPdf\Page::SHAPE_DRAW_STROKE
		);
		
		/** Desenha a linha que separa a logo das informações **/
		$this->pages[$pageIndex]->drawLine(
			(round($this->w*$imageSpace/100) + $this->pageMargin),
			($this->h - $this->pageMargin),
			(round($this->w*$imageSpace/100) + $this->pageMargin),
			(($this->h - ($this->pageMargin + ($this->styles["N"]["lineH"] * $headerLines)))),
			\ZendPdf\Page::SHAPE_DRAW_STROKE
		);
		
		/** Colocar a Logo da Empresa **/
		$image = \ZendPdf\Image::imageWithPath(IMG_PATH . '/zage_100x24.png');
		$this->pages[$pageIndex]->drawImage($image,
			($this->pageMargin + 25),
			($this->h - ($this->pageMargin + 29)),
			($this->pageMargin + 125),
			($this->h - ($this->pageMargin + 5))
		);
		
		/** Colocar as informações do Relatório **/
		/** Nome do relatório **/
		$this->pages[$pageIndex]->drawText(
			"Relatório de importação",
			(round($this->w*$imageSpace/100) + $this->pageMargin + 10),
			($this->h - ($this->pageMargin + ($this->styles["N"]["lineH"] * 1) - $this->offset) ),
			$system->config["charset"]
		);
		
		$this->pages[$pageIndex]->drawText(
			date('d/m/Y H:i:s'),
			(round($this->w*$imageSpace/100) + $this->pageMargin + 300),
			($this->h - ($this->pageMargin + ($this->styles["N"]["lineH"] * 1) - $this->offset) ),
			$system->config["charset"]
		);
		
		/** Atualiza a altura disponível da página atual **/
		$this->availableHeight	-= ($this->styles["N"]["lineH"] * ($headerLines));

		/** Atualiza a coordenada y atual **/
		$this->y				-= ($this->styles["N"]["lineH"] * ($headerLines));
		
	}

	/**
	 * Adicionar o rodapé em uma página
	 */
	private function _addFooter($pageIndex) {
		global $system;
		
		$footerLines	= 1;
		
		/** Desenho o 1 Retângulo do cabeçalho **/
		$this->pages[$pageIndex]->drawRectangle(
			($this->pageMargin),
			($this->pageMargin + $this->styles["N"]["lineH"]),
			($this->w - $this->pageMargin),
			($this->pageMargin),
			\ZendPdf\Page::SHAPE_DRAW_STROKE
		);
				
		/** Atualiza a altura disponível da página atual **/
		$this->availableHeight	-= ($this->styles["N"]["lineH"] * $footerLines);

		//$system->log->debug->debug("_addFooter: AvailableHeight = ".$this->availableHeight." this->y = ".$this->y);
		/** Colocar nome do Sistema **/
		$this->pages[$pageIndex]->drawText(
			$system->config["nome"],
			($this->pageMargin + $this->offset),
			($this->pageMargin + $this->offset),
			$system->config["charset"]
		);
	
		/** Colocar o número da página **/
		$this->pages[$pageIndex]->drawText(
			'Pag. '.($pageIndex),
			($this->w - ($this->pageMargin + 50)),
			($this->pageMargin + $this->offset),
			$system->config["charset"]
		);

	
	}
	
	/**
	 * Adicionar uma linha de texto
	 * 
	 * text: O texto a ser adicionado
	 * align: alinhamento (C -> CENTER, L -> Left, R -> Right)
	 * style: (N -> normal ou B -> Bold)
	 */
	public function addText($text,$align = 'L',$style = null) {
		global $system;
		
		/** Verifica se os parâmetros passados são válidos **/
		switch ($style) {
			case null:
				$style	= $this->defStyle;
			case 'N':
			case 'B':
				
				/** Calcula o tamanho do texto **/
				$w	= mb_strlen($text,'utf8') * $this->styles[$style]["fontW"];
				break;
			default:
				die('Parâmetro style deve ser: N, ou B');
		}

		
		/** Verifica se os parâmetros passados são válidos **/
		switch ($align) {
			case 'C':
				$x	= (round($this->w/2) - round(($w/2)));
				break;
			case 'L':
				$x	= $this->pageMargin + $this->offset;
				break;
			case 'R':
				$x	= ($this->w - ($this->pageMargin + $w + $this->offset));
				break;
			default:
				die('Parâmetro align deve ser: C,L ou R');
		}
		
		/** Verifica se a página ainda cabe a linha **/
		//$system->log->debug->debug("_addLine: X = $x, lineH = ".$this->styles[$style]["lineH"]." AvailableHeight = ".$this->availableHeight." this->y = ".$this->y." Tamanho do Texto: ".mb_strlen($text,'utf8').' Espaço do Texto: '.$w);
		
		/** Verifica se o texto vai caber na linha **/
		$tamanhoTexto	= (mb_strlen($text,'utf8') * $this->styles[$style]["fontW"]);
		$numChars		= floor($this->lineWidth / $this->styles[$style]["fontW"]) + 22;
		$tempText		= $text;
		while ($tamanhoTexto != 0) {
		
			//$system->log->debug->debug("_addLine: TamanhoTexto: $tamanhoTexto, NumChars: $numChars, LineWidth: ".$this->lineWidth.", FontW: ".$this->styles[$style]["fontW"]);
			if ($tamanhoTexto > $this->lineWidth) {
				$newText		= substr($tempText,0,$numChars);
				$tempText		= substr($tempText,$numChars);
				$tamanhoTexto	= (mb_strlen($tempText,'utf8') * $this->styles[$style]["fontW"]);
			}else{
				$newText		= $tempText;
				$tamanhoTexto 	= 0;
			}
			
			if (($this->availableHeight < $this->styles[$style]["lineH"]) ) {
				$this->_addPage();
			}
			
			/** Aplica o estilo **/
			$this->pages[$this->actPage]->setStyle($this->styles[$style]["STYLE"]);
			
			$this->pages[$this->actPage]->drawText(
				$newText,
				$x,
				$this->y - ($this->styles[$style]["lineH"] - $this->offset),
				$system->config["charset"]
			);
			
			/** Atualiza a altura disponível da página atual **/
			$this->availableHeight	-= ($this->styles[$style]["lineH"]);
	
			/** Atualiza a coordenada y atual **/
			$this->y				-= ($this->styles[$style]["lineH"]);
		}
		
	}


	/**
	 * Adicionar um Linha (registro) de uma tabela
	 * 
	 * $aRow array de celulas (
	 * 		Texto , tamanho da celula, align da celula
	 * 		Texto , tamanho da celula, align da celula
	 * 		Texto , tamanho da celula, align da celula
	 * )
	 * style: (N -> normal ou B -> Bold)
	 * align: alinhamento (C -> CENTER, L -> Left, R -> Right) da tabela
	 * border: 0 ou 1 (Imprimir a borda)
	 * fillColor: html da cor de preenchimento
	 */
	public function addRow($aRow,$style,$align,$border,$fillColor) {
		global $system;
		
		
		/** Verifica se os parâmetros passados são válidos **/
		switch ($border) {
			case 0:
			case 1:
				break;
			default:
				die('Parâmetro border deve ser: 0 ou 1');
		}
		
		if (!is_array($aRow)){
			die('Parâmetro aRow deve ser um array');
		}

		/** Calcular o tamanho total do registro **/
		$rowW	= 0;
		foreach ($aRow as $row) {
			if (!is_array($row)) die('Parâmetro incorreto row não é array');
			if (sizeof($row) !== 3) die('Parâmetro incorreto tamanho do array inconsistente ('.sizeof($row).')');
			if ($row[1] == 'F') {
				if (sizeof($aRow) > 1) die('Parâmetro incorreto tamanho do array inconsistente "F"');
				$rowW	= $this->lineWidth;
			}
			$rowW	+= $row[1];
		}
		//$rowW	-= (($this->offset * 2) * sizeof($aRow));

		/** Verifica se os parâmetros passados são válidos **/
		switch ($align) {
			case 'C':
				$x	= (round($this->w/2) - round(($rowW/2)));
				break;
			case 'L':
				$x	= $this->pageMargin + $this->offset;
				break;
			case 'R':
				$x	= ($this->w - ($this->pageMargin + $rowW + $this->offset));
				//$x	= (round($this->w/2) - round(($rowW/2)));
				break;
			default:
				die('Parâmetro align deve ser: C,L ou R');
		}

		
		/** Verifica se a página ainda cabe a linha **/
		if (($this->availableHeight < $this->styles[$style]["lineH"]) ) {
			$this->_addPage();
		}
		
		/** Aplica o estilo **/
		$this->pages[$this->actPage]->setStyle($this->styles[$style]["STYLE"]);
		
		/** Desenha a borda externa **/
		if ($border	== 1) {
			$this->pages[$this->actPage]->drawRectangle(
				$x,
				$this->y,
				$x + $rowW,
				$this->y - $this->styles[$style]["lineH"],
				\ZendPdf\Page::SHAPE_DRAW_STROKE
			);
		}
		

		/** Desenha as celulas **/
		$trX	= $x;
		foreach ($aRow as $row) {
			
			$textW	= (mb_strlen($row[0],'utf8') * $this->styles[$style]["fontW"]);
			
			/** Verifica o alinhamento da celula **/
			//Texto , tamanho da celula, align da celula
			if ($row[1] == 'F') {
				$cellW	= $this->lineWidth;
			}else{
				//$cellW	= $row[1] + ($this->offset * 2);
				$cellW	= $row[1];
				if ($cellW > $this->lineWidth) $cellW = $this->lineWidth;
			}
			switch ($row[2]) {
				case 'C':
					$rowX	= (($trX + round($cellW/2)) - round(($textW/2)));
					break;
				case 'L':
					$rowX	= $trX + $this->offset;
					break;
				case 'R':
					$rowX	= (($trX + $cellW) - ($textW - $this->offset));
					break;
				default:
					die('Parâmetro align deve ser: C,L ou R');
			}
			
			//$system->log->debug->debug("_addRow: rowX = $rowX, AvailableHeight = ".$this->availableHeight." this->y = ".$this->y.' Tamanho da celula'.$cellW." trX = $trX");
			
			/** Desenha o texto **/
			$this->pages[$this->actPage]->drawText(
				$row[0],
				$rowX,
				$this->y - ($this->styles[$style]["lineH"] - $this->offset),
				$system->config["charset"]
			);
			
			/** Desenha a borda da célula **/
			if ($border == 1) {
				$this->pages[$this->actPage]->drawLine(
					($trX + $cellW),
					($this->y),
					($trX + $cellW),
					($this->y - ($this->styles[$style]["lineH"])),
					\ZendPdf\Page::SHAPE_DRAW_STROKE
				);
			}
			
			$trX	+= $cellW;
		}
		
		/** Atualiza a altura disponível da página atual **/
		$this->availableHeight	-= ($this->styles[$style]["lineH"]);

		/** Atualiza a coordenada y atual **/
		$this->y				-= ($this->styles[$style]["lineH"]);
	}

	/**
	 * Adicionar uma linha horizontal
	 * 
	 */
	public function addLine() {
		global $system;
		
		/** Verifica se a página ainda cabe a linha **/
		if (($this->availableHeight < $this->styles[$this->defStyle]["lineH"]) ) {
			$this->_addPage();
		}

		/** Desenha a Linha **/	
		$this->pages[$this->actPage]->drawLine(
			$this->pageMargin,
			$this->y - round($this->styles[$this->defStyle]["lineH"] / 2),
			($this->w - $this->pageMargin),
			$this->y - round($this->styles[$this->defStyle]["lineH"] / 2)
		);
		
		/** Atualiza a altura disponível da página atual **/
		$this->availableHeight	-= ($this->styles[$this->defStyle]["lineH"]);

		/** Atualiza a coordenada y atual **/
		$this->y				-= ($this->styles[$this->defStyle]["lineH"]);
	}

}

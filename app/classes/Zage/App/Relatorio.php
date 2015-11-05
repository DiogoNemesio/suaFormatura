<?php
namespace Zage\App;

/**
 * Implementação de relatórios
 * 
 * @package: Relatorio
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class Relatorio extends \mPDF {
	
	
	/**
	 * Filtros do relatório
	 * @var array
	 */
	public $_filtros 	= array();
	
	/**
	 * Logomarca / Info empresa
	 * @var string
	 */
	public $_logo;
	
	/**
	 * Indicador de impressão de filtros sem valor
	 * @var boolean
	 */
	private $_indExibeFiltrosNulos;
	
	/**
	 * Número de linhas do filtro
	 * @var unknown
	 */
	private $_numLinhasFiltro = 4;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	public function __construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {
		global $system;
		
		/**
		 * Chama o construtor da classe mãe
		 */
		parent::__construct($mode,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$orientation);
		
		/**
		 * Definir o charset padrão
		 */
		$this->charset_in	=	$system->config["charset"];
		
		
		/**
		 * Define as margens
		 */
		$this->setAutoTopMargin	= 'stretch';
		
		
		/**
		 * Define as configurações padrão 
		 */
		$this->exibeFiltrosNulo();
		
		/**
		 * Definir as propriedades do PDF
		 */
		$this->SetTitle($nome);
		$this->SetAuthor("Zage Tecnologia");
		$this->SetCreator("Zage Tecnologia");
		$this->SetSubject("Zage Tecnologia");
		$this->SetKeywords("Zage Tecnologia");
		
		/**
		 * Carregar os CSS utilizados no projeto
		 */
		$printCss	= 		"body {
			background-color: #FFFFFF;
		}
				
		";
		
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/bootstrap.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/font-awesome.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/jquery-ui.custom.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/jquery-ui.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/jquery.gritter.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/ace-fonts.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/ace.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/ace-skins.css'),1);
		//$this->WriteHTML(file_get_contents(PKG_PATH . '/ace/assets/css/colorbox.css'),1);
		$this->WriteHTML(file_get_contents(CSS_PATH . '/mpdf.css'),1);
		//$this->WriteHTML(file_get_contents(CSS_PATH . '/zgWeb.css'),1);
		$this->WriteHTML($printCss,1);
		
	}
	
	/**
	 * Adicionar o cabeçalho no relatório
	 * @param string $nome
	 */
	public function adicionaCabecalho($nome) {
		global $system,$_user,$log;
		
		/**
		 * Monta a logo
		 */
		$this->_montaLogo();
		
		/** Verifica os filtros **/
		$numFiltros			= sizeof($this->_filtros);
		
		if ($nome)	{
			$orgName	= '<tr><td colspan="2" style="text-align: center;width: 100%; height: 15px;"><strong>'.$nome.'</strong></td></tr>';
		}else{
			$orgName	= null;
		}
		
		if ($this->_logo) {
			$logo		= '<td style="width: 200px; height: 80px;"><h6 align="center"><img src="'.$this->_logo.'" align="center" style=""/></h6></td>';
			$filtro		= ($numFiltros) ? '<td style="width: 100%;  height: 80px;">'.$this->_montaFiltros().'</td>' : null;
		}else{
			$logo		= null;
			$filtro		= ($numFiltros) ? '<td colspan="2" style="width: 100%;  height: 80px;">'.$this->_montaFiltros().'</td>' : null;
		}

		$header	= '<table class="table">
		'.$orgName.'
		<tr>
			'.$logo.'
			'.$filtro.'
		</tr>
				
		</table>';
		
		//echo $header;
		
		
		$this->SetHTMLHeader($header);
	}
	
	/**
	 * Adicionar o rodapé ao relatório
	 */
	public function adicionaRodape() {
		global $system;
		$this->SetFooter("[".date($system->config["data"]["datetimeFormat"])."]||Pág: {PAGENO} de {nbpg}");
	}
	
	/**
	 * Montar a logo da empresa
	 */
	private function _montaLogo() {
		global $system,$logoOrg,$_user,$log,$em;
		
		if (!empty($this->_logo)) return;
		
		/** Verifica o tipo de organização **/
		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
		if ($oOrg->getCodTipo()->getCodigo() == "FMT") {
			$oFmtAdm				= \Zage\Fmt\Formatura::getCerimonalAdm($system->getCodOrganizacao());
			if ($oFmtAdm)	{
				$logoOrg	= $oFmtAdm;
			}else{
				$logoOrg	= $oOrg;
			}
		}else{
			$logoOrg	= $oOrg;
		}
		
		if (!empty($this->_logo)) return;
		
		if (isset($logoOrg) && (is_object($logoOrg))) {
			/** Verifica se a organização possui logo **/
			$temLogo		= $em->getRepository('Entidades\ZgadmOrganizacaoLogo')->findOneBy(array('codOrganizacao' => $logoOrg->getCodigo()));
			
			if ($temLogo) {
				$logoUrl		= ROOT_URL . "/Adm/mostraLogomarca.php";
			}else{
				$logoUrl		= null;
			}
			
			if (!$logoUrl) {
				$empre	= $logoOrg->getNome();
				$end1	= $logoOrg->getEndereco() . ", " . $logoOrg->getNumero(). " - ".$logoOrg->getBairro();
				$cidade	= ($logoOrg->getCodLogradouro()) ? $logoOrg->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getNome() : "";
				$estado	= ($logoOrg->getCodLogradouro()) ? $logoOrg->getCodLogradouro()->getCodBairro()->getCodLocalidade()->getCodCidade()->getCodUf()->getCodUf() : "";
				$end2	= "CEP: ".\Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CEP)->aplicaMascara($logoOrg->getCep()) . " " . $cidade. " - ".$estado;
			
			
				$logo	= '
				<table class="" style="border: none;">
				<tr style="border: none;">
					<td style="width: 100%; 	height: 15px;"><h5><strong>'.$empre.'</strong></h5></td>
				</tr>
				<tro  style="border: none;">
					<td style="width: 100%; 	height: 15px;"><h6>'.$end1.'</h6></td>
				</tr>
				<tr style="border: none;">
					<td style="width: 100%; 	height: 15px;"><h6>'.$end2.'</h6></td>
				</tr>
				</table>';
			}else{
				$logo	= '<img src="'.$logoUrl.'" />';
			}
		}else{
			$logo	= null;
		}
		
		$this->_logo	= $logo;
		
	} 
	
	/**
	 * Montar os filtros dos relatório
	 * @param string $nome
	 * @return string
	 */
	private function _montaFiltros() {
		
		/** Inicia o html **/
		$html	= '<table class="table" border="0">';
		
		$dados				= array();
		$numFiltros			= sizeof($this->_filtros);
		$numColsPorLinha	= floor($numFiltros / $this->_numLinhasFiltro);
		$resto				= ($numFiltros % 4);
		$i					= 0;
		
		if ($numFiltros		== 0) return '';
		
		//echo "NumFiltros: $numFiltros<BR>NumColsPorLinha: $numColsPorLinha<BR>Resto: $resto<BR>";
		
		for ($c = 1; $c <= $numColsPorLinha; $c++) {
			for ($l = 1; $l <= $this->_numLinhasFiltro; $l++) {
				$dados[$c][$l]	= '<td style="height: 15px;"><strong>'.$this->_filtros[$i]["NOME"].':</strong> '.$this->_filtros[$i]["VALOR"].'</td>';
				$i++; 
			}
		}
		
		for ($r = 1; $r <= $this->_numLinhasFiltro; $r++) {
			if (isset($this->_filtros[$i])) {
				$dados[$numColsPorLinha+1][$r]	= '<td style="height: 15px;"><strong>'.$this->_filtros[$i]["NOME"].':</strong> '.$this->_filtros[$i]["VALOR"].'</td>';
			}else{
				$dados[$numColsPorLinha+1][$r]	= '<td style="height: 15px;"></td>';
			}
			$i++;
		}
		
		for ($l = 1; $l <= $this->_numLinhasFiltro; $l++) {
			$html .= "<tr>";
			for ($c = 1; $c <= ($numColsPorLinha+1); $c++) {
				$html .= $dados[$c][$l];
			}
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		//echo $html;
		return $html;
	}
	
	/**
	 * Adicionar um filtro no cabeçalho do relatório
	 * @param string $nome
	 * @param string $valor
	 */
	public function adicionaFiltro($nome = null,$valor = null) {
		
		/** Calcula a quantidade atual de filtros **/
		$i	= sizeof($this->_filtros);
		
		$this->_filtros[$i]["NOME"]		= $nome;
		$this->_filtros[$i]["VALOR"]	= $valor;
	}
	
	public function exibeFiltrosNulo() {
		$this->_indExibeFiltrosNulos	= true;
	}
    
	public function NaoExibeFiltrosNulo() {
		$this->_indExibeFiltrosNulos	= false;
	}
	
}
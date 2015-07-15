<?php

namespace Zage\App\Grid;

/**
 * Gerenciar os tipos de grid
 *
 * @package \Zage\App\Grid\Tipo
 * @created 31/10/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
abstract class Tipo {
	
	/**
	 * Tipos de Coluna
	 */
	const TP_TEXTO 		= "ro";
	const TP_BOTAO 		= "bo";
	const TP_ICONE 		= "ic";
	const TP_IMAGEM 	= "im";
	const TP_MOEDA 		= "edn";
	const TP_STATUS		= "status";
	const TP_DATA		= "data";
	const TP_DATAHORA	= "dataHora";
	const TP_SIMOUNAO	= "simounao";
	const TP_CHECKBOX	= "checkbox";
		
	
	/**
	 * Alinhamentos
	 */
	const LEFT 		= "left";
	const CENTER 	= "center";
	const RIGHT 	= "right";
	
	/**
	 * Estilos de paginação
	 */
	const PG_NONE 		= 0;
	const PG_BOOTSTRAP 	= 1;
	
	/**
	 * Estilos de Filtro
	 */
	const FT_TEXTO 		= 0;
	const FT_SELECT 	= 1;
	
	/**
	 * Tipo do Grid
	 */
	const TP_BOOTSTRAP 		= 0;
	const TP_DHTMLX 		= 1;
	const TP_DHTMLX_SUBGRIG = 2;
	
	/**
	 * Variável para guardar as linhas
	 *
	 * @var array
	 */
	protected $linhas;
	
	/**
	 * Variável para guardar as linhas
	 *
	 * @var array
	 */
	protected $colunas;
	
	/**
	 * Variável para guardar as celulas
	 *
	 * @var array
	 */
	protected $celulas;
	
	/**
	 * Número de Linhas
	 *
	 * @var integer
	 */
	protected $numLinhas;
	
	/**
	 * Número de Linhas
	 *
	 * @var integer
	 */
	protected $numColunas;
	
	/**
	 * HTML
	 *
	 * @var string
	 */
	protected $html;
	
	/**
	 * charset
	 *
	 * @var string
	 */
	protected $charset;
	
	/**
	 * Caracter de nova linha
	 *
	 * @var string
	 */
	protected $nl;
	
	/**
	 * Caracter TAB
	 *
	 * @var string
	 */
	protected $tab;
	
	/**
	 * Tipo de paginação
	 *
	 * @var string
	 */
	protected $pagingType;
	
	/**
	 * Variável para guardar os valores de uma combo
	 *
	 * @var array
	 */
	protected $coValues;
	
	/**
	 * Endereço do script para carregar dados do grid (ServerSide Processing)
	 *
	 * @var string
	 */
	protected $serverSideUrl;
	
	/**
	 * Nome
	 *
	 * @var string
	 */
	protected $nome;
	
	/**
	 * Id
	 *
	 * @var string
	 */
	protected $id;
	
	/**
	 * Tipo do Grid (Bootstrap ou DHTMLX)
	 * @var number
	 */
	protected $tipo;
	
	/**
	 * 
	 * @var unknown
	 */
	private $acessor;
	
	/**
	 * Mostrar informações
	 * @var boolean
	 */
	private $mostraInfo;
	
	/**
	 * Mostrar batta de exportação
	 * @var boolean
	 */
	private $mostraBarraExportacao;
	
	/**
	 * Construtor
	 */
	public function __construct($nome) {
		
		/**
		 * Define o Nome do grid *
		 */
		$this->setNome ( $nome );
		
		/**
		 * Define o ID do grid
		 */
		$this->setId ( $nome . 'ID' );
		
		/**
		 * Definindo os valores padrões das variáveis *
		 */
		$this->setNumLinhas ( 0 );
		$this->setNumColunas ( 0 );
		
		/**
		 * Inicializando os arrays *
		 */
		$this->colunas 	= array ();
		$this->linhas 	= array ();
		$this->celulas 	= array ();
		
		/**
		 * Definindo o caracter de nova linha *
		 */
		// $this->nl = null;
		$this->nl = chr ( 10 );
		
		/**
		 * Definindo o caracter tab *
		 */
		// $this->tab = null;
		$this->tab = chr ( 9 );
		
		/**
		 * Charset Padrão *
		 */
		$this->setCharset ( "UTF-8" );
		
		/**
		 * Inicializa o objeto do Synfony para acessar a propriedade do objeto Doctrine
		 */
		$this->accessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
		
		/** Não mostrar barra de exportação por padrão **/
		$this->setMostraBarraExportacao(false);
		
	}
	
	/**
	 * Resgatar o caracter de nova linha
	 */
	protected function getNL() {
		return ($this->nl);
	}
	
	/**
	 * Resgatar o caracter de nova linha
	 */
	protected function setNL($nl) {
		$this->nl = $nl;
	}
	
	/**
	 * Resgatar o caracter TAB
	 */
	protected function getTAB() {
		return ($this->tab);
	}
	
	/**
	 * Adicionar uma Coluna
	 *
	 * @param string $nome        	
	 * @param integer $tamanho        	
	 * @param string $alinhamento        	
	 * @param string $tipo        	
	 */
	protected function adicionaColuna($nome, $tamanho, $alinhamento, $tipo) {
		
		/**
		 * Validar os parâmetros *
		 */
		if (($tamanho) && (! is_numeric ( $tamanho ))) {
			\Zage\App\Erro::halt ( 'Parâmetro Tamanho não numérico !!!' );
		}
		
		if (($alinhamento) && ($alinhamento != self::LEFT) && ($alinhamento != self::CENTER) && ($alinhamento != self::RIGHT)) {
			\Zage\App\Erro::halt ( 'Parâmetro Alinhamento deve ser (LEFT,CENTER,RIGHT) ' );
		}
		
		/**
		 * Define o próximo índice *
		 */
		$i = sizeof ( $this->colunas );
		
		/**
		 * Verifica o tipo para instanciar o objeto correto *
		 */
		switch ($tipo) {
			case self::TP_TEXTO :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Texto ();
				break;
			case self::TP_MOEDA :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Moeda ();
				break;
			case self::TP_BOTAO :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Botao ();
				break;
			case self::TP_ICONE :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Icone ();
				break;
			case self::TP_IMAGEM :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Imagem ();
				break;
			case self::TP_STATUS :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Status ();
				break;
			case self::TP_DATA :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\Data ();
				break;
			case self::TP_DATAHORA :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\DataHora();
				break;
			case self::TP_SIMOUNAO :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\SimOuNao ();
				break;
			case self::TP_CHECKBOX :
				$this->colunas [$i] = new \Zage\App\Grid\Coluna\CheckBox();
				break;
			default :
				\Zage\App\Erro::halt ( 'Tipo de coluna desconhecido' );
				break;
		}
		
		/**
		 * Definindo os valores *
		 */
		$this->colunas [$i]->setNome ( $nome );
		$this->colunas [$i]->setTamanho ( $tamanho );
		$this->colunas [$i]->setAlinhamento ( $alinhamento );
		$this->colunas [$i]->setTipo ( $tipo );
		
		/**
		 * Altera o valor da variável numColunas *
		 */
		$this->setNumColunas ( $i + 1 );
		
		/**
		 * Retorna o índice adicionado *
		 */
		return ($i);
	}
	
	/**
	 * Adicionar uma Coluna do tipo botão
	 *
	 * @param string $tipo        	
	 */
	public function adicionaBotao($modelo) {
		
		/**
		 * Valida alguns tipos
		 */
		if (($modelo != \Zage\App\Grid\Coluna\Botao::MOD_ADD) && ($modelo != \Zage\App\Grid\Coluna\Botao::MOD_EDIT) && ($modelo != \Zage\App\Grid\Coluna\Botao::MOD_REMOVE) && ($modelo != \Zage\App\Grid\Coluna\Botao::MOD_CANCEL) ) {
			\Zage\App\Erro::halt ( 'Tipo desconhecido !!!' );
		}
		
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( "&nbsp;", 4, self::CENTER, self::TP_BOTAO );
		
		/**
		 * Define o modelo do botão
		 */
		$this->colunas [$i]->setModelo ( $modelo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Imagem
	 *
	 * @param string $imagem        	
	 */
	public function adicionaImagem($url, $endereco) {
		
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( null, 4, self::CENTER, self::TP_IMAGEM );
		
		/**
		 * Define as informações da Imagem
		 */
		$this->colunas [$i]->setUrl ( $url );
		$this->colunas [$i]->setEnderecoImagem ( $endereco );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Ícone
	 *
	 * @param string $Icone        	
	 */
	public function adicionaIcone($url, $icone, $descricao) {
		
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( null, 4, self::CENTER, self::TP_ICONE );
		
		/**
		 * Define as informações do Ícone
		 */
		$this->colunas [$i]->setUrl ( $url );
		$this->colunas [$i]->setIcone ( $icone );
		$this->colunas [$i]->setDescricao ( $descricao );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Texto
	 * @param unknown $nome
	 * @param unknown $tamanho
	 * @param unknown $alinhamento
	 * @param unknown $nomeCampo
	 */
	public function adicionaTexto($nome, $tamanho, $alinhamento, $nomeCampo, $mascara = null) {
		
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, $tamanho, $alinhamento, self::TP_TEXTO );
		
		/**
		 * Define as informações do Texto
		 */
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
		$this->colunas [$i]->setMascara ( $mascara );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Moeda
	 * @param unknown $nome
	 * @param unknown $tamanho
	 * @param unknown $alinhamento
	 * @param unknown $nomeCampo
	 */
	public function adicionaMoeda($nome, $tamanho, $alinhamento, $nomeCampo) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, $tamanho, $alinhamento, self::TP_MOEDA );
	
		/**
		 * Define as informações do Texto
		*/
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Status
	 *
	 * @param string $Icone
	 */
	public function adicionaStatus($nome,$nomeCampo) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, 6, self::CENTER, self::TP_STATUS );
	
		/**
		 * Define as informações do Status
		 */
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Data
	 * @param unknown $nome
	 * @param unknown $tamanho
	 * @param unknown $alinhamento
	 * @param unknown $nomeCampo
	 */
	public function adicionaData($nome, $tamanho, $alinhamento, $nomeCampo) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, $tamanho, $alinhamento, self::TP_DATA );
	
		/**
		 * Define as informações do Texto
		*/
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Data
	 * @param unknown $nome
	 * @param unknown $tamanho
	 * @param unknown $alinhamento
	 * @param unknown $nomeCampo
	 */
	public function adicionaDataHora($nome, $tamanho, $alinhamento, $nomeCampo) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, $tamanho, $alinhamento, self::TP_DATAHORA );
	
		/**
		 * Define as informações do Texto
		*/
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Status
	 *
	 * @param string $Icone
	 */
	public function adicionaSimOuNao($nome,$nomeCampo) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, 6, self::CENTER, self::TP_SIMOUNAO );
	
		/**
		 * Define as informações do Status
		*/
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo CheckBox
	 *
	 * @param string $nome
	 */
	public function adicionaCheckBox($nome) {
	
		/**
		 * Adiciona a coluna
		 */
		$i = $this->adicionaColuna ( $nome, 4, self::CENTER, self::TP_CHECKBOX);
	}
	
	
	/**
	 * Carrega os dados a partir um array
	 *
	 * @param array $dados        	
	 */
	public function importaDadosArray($dados,$autoDetectCharset = false) {
		global $system;
		/**
		 * Array esperado é do tipo Zend_Db::FETCH_OBJ
		 *
		 * As propriedades do objeto devem ser iguais aos nomes das colunas
		 */
		
		/**
		 * Zera o array de linhas *
		 */
		$this->linhas = array ();
		$this->setNumLinhas ( 0 );
		
		//ini_set('mbstring.substitute_character', "none");
		
		/**
		 * Zera o array de celulas *
		 */
		$this->celulas = array ();
		
		if ($dados instanceof \Zend\Db\ResultSet\ResultSet) {
			$i = 0;
			foreach ($dados as $d) {
				/** Inicializa os objetos **/
				$this->linhas [$i] = new \Zage\App\Grid\Linha ();
				$this->linhas [$i]->setIndice ( $i );
					
				for($j = 0; $j <= $this->getNumColunas () - 1; $j ++) {
					$nome = $this->colunas [$j]->getNome ();
					$campo = $this->colunas [$j]->getNomeCampo ();
						
					$this->celulas [$i] [$j] = new \Zage\App\Grid\Celula ();
					$this->celulas [$i] [$j]->setLinha ( $i );
					$this->celulas [$i] [$j]->setColuna ( $j );
						
					if (! empty ( $campo )) {
						if (property_exists ( $d, $campo )) {
							if ($autoDetectCharset == false) {
								$valor = htmlspecialchars($d->$campo);
							}else{
								$valor		= mb_convert_encoding($d->$campo, $system->config->database->charset, mb_detect_encoding($dados [$i]->$campo, "auto"));
								//$valor 		= iconv($charset, "utf-8//IGNORE", $dados [$i]->$campo);
								$valor		= htmlspecialchars($valor);
							}
							$this->celulas [$i] [$j]->setValor ( $valor );
						}
					}
				}
				$this->numLinhas ++;
				$i++;
			}
		}elseif (is_array($dados) ) {
			for($i = 0; $i < sizeof ( $dados ); $i ++) {
				/** Inicializa os objetos **/
				$this->linhas [$i] = new \Zage\App\Grid\Linha ();
				$this->linhas [$i]->setIndice ( $i );
				
				for($j = 0; $j <= $this->getNumColunas () - 1; $j ++) {
					$nome = $this->colunas [$j]->getNome ();
					$campo = $this->colunas [$j]->getNomeCampo ();
			
					$this->celulas [$i] [$j] = new \Zage\App\Grid\Celula ();
					$this->celulas [$i] [$j]->setLinha ( $i );
					$this->celulas [$i] [$j]->setColuna ( $j );
			
					if (! empty ( $campo )) {
						if ($autoDetectCharset == false) {
							
							if (is_array($dados[$i]) && array_key_exists($campo, $dados [$i])) {
								$valor = htmlspecialchars($dados [$i][$campo]);
							} elseif (is_object($dados [$i]) && property_exists ( $dados [$i], $campo )) {
								$valor = htmlspecialchars($dados [$i]->$campo);
							}else{
								die('Tipo de Campo desconhecido !!!');
							}
						}else{
							//print_r($dados[$i]);
							if (is_array($dados[$i]) && array_key_exists($campo, $dados [$i])) {
							//if (array_key_exists($campo, $dados [$i])) {
								$valor		= mb_convert_encoding($dados[$i][$campo], $system->config->database->charset, mb_detect_encoding($dados[$i][$campo], "auto"));
							} elseif (is_object($dados [$i]) && property_exists ( $dados [$i], $campo )) {
								$valor		= mb_convert_encoding($dados [$i]->$campo, $system->config->database->charset, mb_detect_encoding($dados [$i]->$campo, "auto"));
							}
								
							//$valor 		= iconv($charset, "utf-8//IGNORE", $dados [$i]->$campo);
							$valor		= htmlspecialchars($valor);
						}
						if (is_array($dados[$i]) && array_key_exists($campo, $dados [$i])) {
							$this->celulas [$i] [$j]->setValor ( $valor );
							} elseif (is_object($dados [$i]) && property_exists ( $dados [$i], $campo )) {
							$this->celulas [$i] [$j]->setValor ( $valor );
						}
					}
				}
				$this->numLinhas ++;
			}
		}
		
	}
	
	/**
	 * Carregar os dados a partir de um retorno do doctrine
	 * @param array $dados
	 * @param string $autoDetectCharset
	 */
	public function importaDadosDoctrine(array $dados,$autoDetectCharset = false) {
		global $system,$log;

		/**
		 * Array esperado é do tipo object(stdClass)
		 *
		 * os nomes da colunas devem ser uma propriedade privada da classe, e deve conter o método get(NOME_COLUNA)() 
		 */
	
		/**
		 * Zera o array de linhas *
		 */
		$this->linhas = array ();
		$this->setNumLinhas ( 0 );
	
		
		/**
		 * Zera o array de celulas *
		*/
		$this->celulas = array ();
	
		if (!is_array($dados)) die ('Tipo de dados imcompatível '.__CLASS__);

		$i = 0;

		for($i = 0; $i < sizeof ( $dados ); $i ++) {
			/** Inicializa os objetos **/
			
			$this->linhas [$i] = new \Zage\App\Grid\Linha ();
			$this->linhas [$i]->setIndice ( $i );

			for($j = 0; $j <= $this->getNumColunas () - 1; $j ++) {
				$nome 	= $this->colunas [$j]->getNome ();
				$campo	= $this->colunas [$j]->getNomeCampo ();
					
				$this->celulas [$i] [$j] = new \Zage\App\Grid\Celula ();
				$this->celulas [$i] [$j]->setLinha ( $i );
				$this->celulas [$i] [$j]->setColuna ( $j );
					
				if (! empty ( $campo )) {
					
					try {
						
						$temp			= explode('|', $campo);
						$data			= $temp[0];
						if (isset($temp[1])) {
							$valorPadrao	= $temp[1];
						}else{
							$valorPadrao	= "";
						}
						
						
						$_campos	= explode(':', $data);
						$objeto		= $dados[$i];
						//$comando	= '$valor = $dados[$i]';
						for ($n = 0; $n < sizeof ($_campos); $n++) {
							if (is_object($objeto)) {
								$valor		= $this->accessor->getValue($objeto, $_campos[$n]);
								$objeto		= $valor;
							}else{
								$valor		= $valorPadrao;
								$objeto		= null;
							} 
						}
						
						if (is_object($valor)) {
							if ($valor instanceof \DateTime) {
								if ($this->colunas [$j]->getTipo() == self::TP_DATAHORA) {
									$valor	= $valor->format($system->config["data"]["datetimeFormat"]);
								}else{
									$valor	= $valor->format($system->config["data"]["dateFormat"]);
								}
							}else{
								die ('Grid: Objeto informado quando esperado uma string. Campo: '.$campo);
							}
						}elseif ($valor == null) {
							$valor	= $valorPadrao;
						}
						
						if ($autoDetectCharset == false) {
							$valor = htmlspecialchars($valor);
						}else{
							$valor		= mb_convert_encoding($valor, $system->config["database"]["charset"], mb_detect_encoding($valor, "auto"));
							$valor		= htmlspecialchars($valor);
						}
	
						$this->celulas [$i] [$j]->setValor ( $valor );
					} catch (\Exception $e) {
						\Zage\App\Erro::halt($e->getMessage(),__FILE__,__LINE__);
					}
						
				}
			}
			$this->numLinhas ++;
		}
	}
	
	private function _getValorCampoDoctrine ($objeto,$propriedade) {
		return($this->accessor->getValue($objeto, $propriedade));
		//return 'get'.strtoupper(substr($campo,0,1)) . substr($campo,1).'()';
	}
	
	/**
	 * Carrega os dados a partir um array
	 */
	public function getHtmlCode() {
		/**
		 * Verifica se foi setado algum caracter set, senão utilizar UTF-8 *
		 */
		if (! $this->getCharset ()) {
			$this->setCharset ( 'UTF-8' );
		}
		
		$this->geraHTML();
		
		return ($this->html);
	}
	
	/**
	 * Retorna o código em Json dos registros
	 *
	 * @param unknown $echo        	
	 * @param unknown $numRegTotal        	
	 * @param unknown $inicio        	
	 * @param unknown $tamanho        	
	 * @return string
	 */
	public function getJsonDataOld($echo, $numRegTotal, $inicio, $tamanho) {
		$output = array (
				"sEcho" => intval ( $echo ),
				"iTotalRecords" => $numRegTotal,
				"iTotalDisplayRecords" => $this->getNumLinhas (),
				"aaData" => array () 
		);
		
		if ($inicio == null)
			$inicio = 0;
		if ($tamanho == null)
			$tamanho = 10;
		
		$t = ($inicio + $tamanho);
		
		if ($t > $this->getNumLinhas ()) {
			$t = $this->getNumLinhas ();
		}
		
		for($i = $inicio; $i < $t; $i ++) {
			$row = array ();
			/**
			 * Faz o loop nas celulas da linha *
			 */
			for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
				if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
					$row [] = $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (), $this->celulas [$i] [$j]->getEnderecoImagem () );
				} else {
					$row [] = $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () );
				}
			}
			$output ['aaData'] [] = $row;
		}
		
		return json_encode ( $output );
	}
	
	/**
	 * Definir o valor de uma célula
	 */
	public function setValorCelula($linha, $coluna, $valor) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->setValor ( $valor );
		}
	}
	
	/**
	 * Definir o valor do endereço da imagem de uma célula do tipo imagem
	 */
	public function setEnderecoImagemCelula($linha, $coluna, $enderecoImagem) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			if ($this->colunas [$coluna]->getTipo () !== self::TP_IMAGEM) {
				\Zage\App\Erro::halt ( 'Endereço de imagem só pode ser definido para uma coluna do tipo Imagem !!!' );
			} else {
				$this->celulas [$linha] [$coluna]->setEnderecoImagem ( $enderecoImagem );
			}
		}
	}
	
	/**
	 * Definir o valor da url de uma célula
	 */
	public function setUrlCelula($linha, $coluna, $url) {
		global $log;
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			switch ($this->colunas [$coluna]->getTipo ()) {
				case self::TP_IMAGEM:
				case self::TP_BOTAO:
				case self::TP_ICONE:
					$this->celulas [$linha] [$coluna]->setUrl($url);
					break;
				default:
					\Zage\App\Erro::halt ( 'Url só pode ser definida para tipos de coluna: BOTAO, IMAGEM e ICONE' );
					break;
			}
		}
	}
	
	/**
	 * Definir o icone de uma célula
	 */
	public function setIconeCelula($linha, $coluna, $url) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			switch ($this->colunas [$coluna]->getTipo ()) {
				case self::TP_ICONE:
					$this->celulas [$linha] [$coluna]->setIcone($url);
					break;
				default:
					\Zage\App\Erro::halt ( 'Ícone só pode ser definido para tipos de coluna: ICONE' );
					break;
			}
		}
	}
	

	/**
	 * Definir a descrição de uma célula
	 */
	public function setDescricaoCelula($linha, $coluna, $descricao) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			switch ($this->colunas [$coluna]->getTipo ()) {
				case self::TP_ICONE:
					$this->celulas [$linha] [$coluna]->setDescricao($descricao);
					break;
				default:
					\Zage\App\Erro::halt ( 'Descrição só pode ser definido para tipos de coluna: ICONE' );
					break;
			}
		}
	}
	
	/**
	 * Resgatar o nome do campo de uma determinada coluna
	 * @param unknown $coluna
	 * @return NULL
	 */
	public function getNomeCampo($coluna) {
		global $log;
		
		if (isset($this->colunas[$coluna])) {
			return($this->colunas [$coluna]->getNomeCampo ());
		}else{
			return null;
		}
	}
	
	/**
	 * Desabilitar uma Linha
	 */
	public function desabilitaLinha($indice) {
		if (isset ( $this->linhas [$indice] )) {
			$this->linhas [$indice]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Linha
	 */
	public function habilitaLinha($indice) {
		if (isset ( $this->linhas [$indice] )) {
			$this->linhas [$indice]->ativar ();
		}
	}
	
	/**
	 * Desabilitar uma Coluna
	 */
	public function desabilitaColuna($indice) {
		if (isset ( $this->colunas [$indice] )) {
			$this->colunas [$indice]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Linha
	 */
	public function habilitaColuna($indice) {
		if (isset ( $this->colunas [$indice] )) {
			$this->colunas [$indice]->ativar ();
		}
	}
	
	/**
	 * Desabilitar uma Célula
	 */
	public function desabilitaCelula($linha, $coluna) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Célula
	 */
	public function habilitaCelula($linha, $coluna) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->ativar ();
		}
	}
	
	/**
	 *
	 * @return the $numLinhas
	 */
	public function getNumLinhas() {
		return $this->numLinhas;
	}
	
	/**
	 *
	 * @param number $numLinhas        	
	 */
	public function setNumLinhas($numLinhas) {
		$this->numLinhas = $numLinhas;
	}
	
	/**
	 *
	 * @return the $numColunas
	 */
	public function getNumColunas() {
		return $this->numColunas;
	}
	
	/**
	 *
	 * @param number $numColunas        	
	 */
	public function setNumColunas($numColunas) {
		$this->numColunas = $numColunas;
	}
	
	/**
	 *
	 * @return the $html
	 */
	public function getHtml() {
		return $this->html;
	}
	
	/**
	 *
	 * @param string $html        	
	 */
	public function setHtml($html) {
		$this->html = $html;
	}
	
	/**
	 *
	 * @return the $charset
	 */
	public function getCharset() {
		return $this->charset;
	}
	
	/**
	 *
	 * @param string $charset
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}
	
	/**
	 *
	 * @return the $pagingType
	 */
	public function getPagingType() {
		return $this->pagingType;
	}
	
	/**
	 *
	 * @param string $pagingType        	
	 */
	public function setPagingType($pagingType) {
		$this->pagingType = $pagingType;
	}
	
	/**
	 *
	 * @return the $serverSideUrl
	 */
	public function getServerSideUrl() {
		return $this->serverSideUrl;
	}
	
	/**
	 *
	 * @param multitype: $serverSideUrl        	
	 */
	public function setServerSideUrl($serverSideUrl) {
		$this->serverSideUrl = $serverSideUrl;
	}
	
	/**
	 *
	 * @return the $nome
	 */
	protected function getNome() {
		return $this->nome;
	}
	
	/**
	 *
	 * @param string $nome        	
	 */
	protected function setNome($nome) {
		$this->nome = $nome;
	}
	
	/**
	 *
	 * @return the $id
	 */
	protected function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @param string $id        	
	 */
	protected function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param number $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	/**
	 * @return the $mostraInfo
	 */
	public function getMostraInfo() {
		return $this->mostraInfo;
	}

	/**
	 * @param boolean $mostraInfo
	 */
	public function setMostraInfo($mostraInfo) {
		$this->mostraInfo = $mostraInfo;
	}
	
	/**
	 * @return the $mostraBarraExportacao
	 */
	public function getMostraBarraExportacao() {
		return $this->mostraBarraExportacao;
	}

	/**
	 * @param boolean $mostraBarraExportacao
	 */
	public function setMostraBarraExportacao($mostraBarraExportacao) {
		$this->mostraBarraExportacao = $mostraBarraExportacao;
	}

	/** Definir o filtro individual por coluna **/
	public function filtraPorColuna($indCol,$tipo) {
		global $log;
		
		switch ($tipo) {
			case self::FT_TEXTO:
			case self::FT_SELECT:
				break;
			default :
				\Zage\App\Erro::halt ( 'Tipo de filtro desconhecido' );
				break;
		}
		
		/** Verificar se o índice da coluna existe **/
		if (isset ( $this->colunas[$indCol])) {
			$this->colunas[$indCol]->setIndFiltro(true);
			$this->colunas[$indCol]->setTipoFiltro($tipo);
		}
		
	}
	
}
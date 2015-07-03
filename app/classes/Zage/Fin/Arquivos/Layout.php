<?php
namespace Zage\Fin\Arquivos;

/**
 * @package: \Zage\Fin\Arquivos\Layout
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar os layouts de arquivos
 */

abstract class Layout {
	
	/**
	 * Tipo do arquivo PTU
	 *
	 * @var string
	 */
	private $codTipoLayout;

	/**
	 * Array de linhas do arquivo
	 *
	 * @var array
	 */
	public $registros = array();
	
	/**
	 * Conteúdo do arquivo
	 *
	 * @var string
	 */
	private $conteudo;
	

	/**
	 * Nome do Layout
	 *
	 * @var string
	 */
	private $nome;
	
	/**
	 * Código do tipo de Arquivo
	 */
	private $codTipoArquivo;
	
	/**
	 * Objeto de Resumo
	 *
	 * @var \Zage\App\FilaImportacao\ResumoPDF
	 */
	protected $_resumo;

	/**
	 * Tipos de Registro
	 * @var array
	 */
	protected $_tiposRegistro	= array();
	
	/**
	 * Caracter de fim de linha
	 *
	 * @var string
	 */
	private $EOL;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		#################################################################################
		## Inicia o objeto de resumo
		#################################################################################
		$this->_resumo	= new \Zage\App\FilaImportacao\ResumoPDF();
		
		
		#################################################################################
		## Seta a variável de Fim de linha
		#################################################################################
		$this->EOL = chr(10);
	}
	
	/**
	 * @param object $tipoRegistro
	 */
	public function adicionaRegistro($tipoRegistro) {
		global $log;
		
		#################################################################################
		## Verifica se o Tipo do Layout foi informado
		#################################################################################
		if (!$this->getCodTipoLayout())	{
			$this->_resumo->adicionaErro(0, 0, null, 'Tipo do Layout nao definido !!! ');
			throw new \Exception('Tipo do Layout nao definido !!! ');
		}
		
		#################################################################################
		## Verifica se o Tipo do Registro é válido para esse layout
		#################################################################################
		if (!array_key_exists($tipoRegistro, $this->_tiposRegistro))	{
			$this->_resumo->adicionaErro(0, 0, null, 'Tipo de registro "'.$tipoRegistro.'" não é válido para o layout "'.$this->getCodTipoLayout().'" !!! ');
			throw new \Exception('Tipo de registro "'.$tipoRegistro.'" não é válido para o layout "'.$this->getCodTipoLayout().'" !!! ');
		}
		
		#################################################################################
		## Calcula o próximo índice
		#################################################################################
		$i 			= $this->numRegistros();
		
		#################################################################################
		## Monta a string da classe
		#################################################################################
		$classe 	= "\\Zage\\Fin\\Arquivos\\Layout\\".$this->getCodTipoLayout()."\\TipoRegistro\\" . $tipoRegistro;
		$fileClass	= CLASS_PATH . '/Zage/Fin/Arquivos/Layout/'.$this->getCodTipoLayout().'/TipoRegistro/'.$tipoRegistro.'.php';
		
		if (file_exists($fileClass)) {
			$this->registros[$i]   = new $classe;
			$this->registros[$i]->setLinha($i+1);
		}else{
			$log->debug("Classe não existe ($classe)");
			$this->_resumo->adicionaErro(0, 0, null, 'Tipo de Registro não encontrado: '.$tipoRegistro.' !!! ');
			throw new \Exception('Tipo de Registro não encontrado: '.$tipoRegistro.' !!! ');
		}

		return ($i);
	}

	/**
	 * @return number $numRegistros
	 */
	public function numRegistros() {
		return sizeof($this->registros);
	}
	
	/**
	 * @param object $tipoRegistro
	 */
	public function setValor($registro,$ordem,$valor) {
		if (!isset($this->registros[$registro])) {
			die('Registro não encontrado ('.$registro.') !!!');
		}
		
		if (!isset($this->registros[$registro]->campos[$ordem])) {
			die('Sequência não encontrada ('.$ordem.') !!!');
		}
		
		$this->registros[$registro]->setValor($ordem,$valor);
		//$this->registros[$registro]->campos[$ordem]->setValor($valor);
	}
	
	/**
	 * Resgatar o valor
	 */
	public function getValor($registro,$ordem) {
		if (!isset($this->registros[$registro])) {
			$this->_resumo->adicionaErro(0, 0, null, 'Registro não encontrado ('.$registro.') !!!');
			throw new \Exception('Registro não encontrado ('.$registro.') !!!');
		}
	
		if (!isset($this->registros[$registro]->campos[$ordem])) {
			$this->_resumo->adicionaErro(0, 0, null, 'Sequência não encontrada ('.$ordem.') !!!');
			throw new \Exception('Sequência não encontrada ('.$ordem.') !!!');
		}
		return ($this->registros[$registro]->getValor());
	}

	/**
	 * Resgatar o registro
	 */
	public function getRegistro($registro) {
		if (!isset($this->registros[$registro])) {
			$this->_resumo->adicionaErro(0, 0, null, 'Registro não encontrado ('.$registro.') !!!');
			throw new \Exception('Registro não encontrado ('.$registro.') !!!');
		}
		return ($this->registros[$registro]->getRegistro());
	}
	
	/**
	 * Gerar o arquivo 
	 */
	public function geraArquivo() {
		foreach ($this->registros as $reg) {
			$valido	= $reg->validar();
			if ($valido === true) {
				$this->conteudo .= $reg->getRegistro() . $this->EOL;
			}else{
				if ($valido instanceof \Zage\Fin\Arquivos\Erro) {
					$this->_resumo->adicionaErro(0, 0, null, $valido->getMensagem());
					throw new \Exception("Linha: '".$reg->getLinha(). "' " . $valido->getMensagem() . $this->EOL);
				}else{
					$this->_resumo->adicionaErro(0, 0, null, $valido);
					throw new \Exception("Linha: '".$reg->getLinha(). "' " . $valido . $this->EOL);
				}
			}
		}
	}
	
	/**
	 * Resgatar o conteúdo do arquivo
	 * @return string
	 */
	public function getConteudo () {
		return ($this->conteudo);
	}
	
	
	/**
	 * Calcula o número de linhas do PTU
	 */
	public static function calculaNumLinhas ($arquivo) {
		return (intval(exec('wc -l ' . $arquivo)));
	}

	/**
	 * Adicionar um registro de erro
	 * @param string||\Zage\Fin\Arquivos\Erro $erro
	 * @param integer $linha
	 * @param string $tipoReg
	 * @param integer $ordem
	 */
	protected function adicionaErro ($erro,$linha,$tipoReg,$ordem) {
		#################################################################################
		## Número máximo de erros
		#################################################################################
		$max = 100;
		
		$n	= sizeof($this->erros);
		
		if ($n == $max) {
			$this->erros[$n]	= new \Zage\Fin\Arquivos\Erro();
			$this->erros[$n]->setLinha(0);
			$this->erros[$n]->setTipoRegistro("Geral");
			$this->erros[$n]->setOrdem(0);
			$this->erros[$n]->setMensagem("Número máximo de erros alcançado !!!");
			return;
		}elseif ($n > $max) {
			return;
		}
		
		if ($erro instanceof \Zage\Fin\Arquivos\Erro) {
			$this->erros[$n]	= $erro;
			if ($erro->getLinha() 			== null)	$erro->setLinha($linha);
			if ($erro->getTipoRegistro() 	== null) 	$erro->setTipoRegistro($tipoReg);
			if ($erro->getOrdem() 			== null) 	$erro->setOrdem($ordem);
		}else{
			$this->erros[$n]	= new \Zage\Fin\Arquivos\Erro();
			$this->erros[$n]->setLinha($linha);
			$this->erros[$n]->setTipoRegistro($tipoReg);
			$this->erros[$n]->setOrdem($ordem);
			$this->erros[$n]->setMensagem($erro);
		}
	}

	
	/**
	 *
	 * @return the string
	 */
	public function getCodTipoLayout() {
		return $this->codTipoLayout;
	}
	
	/**
	 *
	 * @param string $codTipoLayout        	
	 */
	public function setCodTipoLayout($codTipoLayout) {
		$this->codTipoLayout = $codTipoLayout;
		return $this;
	}
	
	/**
	 * @return the $codTipoArquivo
	 */
	public function getCodTipoArquivo() {
		return $this->codTipoArquivo;
	}

	/**
	 * @param string $codTipoArquivo
	 */
	public function setCodTipoArquivo($codTipoArquivo) {
		$this->codTipoArquivo = $codTipoArquivo;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getNome() {
		return $this->nome;
	}
	
	/**
	 *
	 * @param string $nome        	
	 */
	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}
	
	/**
	 * Retornar o resumo em PDF
	 */
	public function getResumoPDF() {
		return $this->_resumo->getPdf();
	}
	
}

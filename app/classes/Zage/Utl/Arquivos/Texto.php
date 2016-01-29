<?php
namespace Zage\Utl\Arquivos;

/**
 * @package: \Zage\Utl\Arquivos\Texto
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 * Gerenciar arquivos Texto
 */

abstract class Texto {
	
	/**
	 * Array de linhas do arquivo
	 *
	 * @var array
	 */
	public $linhas = array();
	
	/**
	 * Número de Linhas
	 *
	 * @var string
	 */
	private $numLinhas;
	
	/**
	 * Conteúdo do arquivo
	 *
	 * @var string
	 */
	private $conteudo;
	
	/**
	 * Tamanho do arquivo
	 *
	 * @var string
	 */
	private $tamanho;
	
	/**
	 * Nome do Layout
	 *
	 * @var string
	 */
	private $nome;
	
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
		## Seta a variável de Fim de linha
		#################################################################################
		$this->EOL = chr(10);

	}

	
	/**
	 * Calcula o número de linhas do PTU
	 */
	public static function calculaNumLinhas ($arquivo) {
		return (intval(exec('wc -l ' . $arquivo)));
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getNumLinhas() {
		return $this->numLinhas;
	}
	
	/**
	 *
	 * @param string $numLinhas        	
	 */
	public function setNumLinhas($numLinhas) {
		$this->numLinhas = $numLinhas;
		return $this;
	}
	
	/**
	 * Resgatar o conteúdo do arquivo
	 * @return string
	 */
	public function getConteudo () {
		return ($this->conteudo);
	}
	
	/**
	 *
	 * @param string $conteudo        	
	 */
	public function setConteudo($conteudo) {
		$this->conteudo = $conteudo;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getTamanho() {
		return $this->tamanho;
	}
	
	/**
	 *
	 * @param string $tamanho        	
	 */
	public function setTamanho($tamanho) {
		$this->tamanho = $tamanho;
		return $this;
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
	 *
	 * @return the string
	 */
	public function getEOL() {
		return $this->EOL;
	}
	
	/**
	 * Ler ($numLinhas) de um arquivo do S.O. e carregar as linhas no array, se numLinhas for nulo, carregar por completo
	 * @param string $arquivo
	 * @param integer $numLinhas
	 * @throws \Exception
	 */
	public function lerArquivo($arquivo,$numLinhas = null) {
	
		#################################################################################
		## Verifica se o arquivo existe
		#################################################################################
		if (!file_exists($arquivo)) 	{
			throw new \Exception('Arquivo não encontrado ('.$arquivo.') ');
		}
	
		#################################################################################
		## Verifica se o arquivo pode ser lido
		#################################################################################
		if (!is_readable($arquivo)) 	{
			throw new \Exception('Arquivo não pode ser lido ('.$arquivo.') ');
		}
	
		#################################################################################
		## Lê o arquivo
		#################################################################################
		$lines	= file($arquivo);
	
		#################################################################################
		## Verifica se o arquivo tem informação
		#################################################################################
		if (sizeof($lines) < 1) {
			throw new \Exception('Arquivo sem informações ('.$arquivo.') ');
		}
			
		#################################################################################
		## Percorre as linhas do arquivo
		#################################################################################
		$totalLinhas		= ($numLinhas) ? (int) $numLinhas : sizeof($lines);
		for ($i = 0; $i < $totalLinhas; $i++) {
			$linha				= str_replace(array("\n", "\r"), '', $lines[$i]);
			$n					= sizeof($this->linhas);
			$this->linhas[$n]	= $linha;
		}

		$this->setNumLinhas(sizeof($this->linhas));
	}
	
	

}

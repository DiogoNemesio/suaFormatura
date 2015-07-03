<?php
namespace Zage\Fin\Arquivos;

/**
 * @package: \Zage\Fin\Arquivos\Campo
 * @created: 29/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os Tipos do campo
 */

class Campo extends \Zage\Fin\Arquivos\TipoDado {
	
	/**
	 * Tipos de Dados
	 *
	 * @var string
	 */
	const T_N		= 'NUM';
	const T_A		= 'ALFA';
	const T_D		= 'DIN';
	const T_DT6		= 'DT6';
	
	private $_tipoValidos	= array(self::T_N,self::T_A,self::T_D,self::T_DT6);
	
	/**
	 * Ordem 
	 *
	 * @var string
	 */
	private $ordem;
	
	/**
	 * Posição Inicial
	 *
	 * @var string
	 */
	private $posicaoInicial;
	
	/**
	 * Posição Final
	 *
	 * @var string
	 */
	private $posicaoFinal;

	/**
	 * Nome
	 *
	 * @var string
	 */
	private $nome;
	
	/**
	 * Variável
	 *
	 * @var string
	 */
	private $variavel;

	/**
	 * valorFixo
	 *
	 * @var string
	 */
	private $valorFixo;
	
	
	/**
	 * Tipo
	 *
	 * @var object
	 */
	public $tipo;
	
	
	/**
	 * Construtor
	 */
	public function __construct($tipo) {
		
		#################################################################################
		## Verifica se o tipo é válido
		#################################################################################
		if (!in_array($tipo, $this->_tipoValidos)) {
			die('Tipo de Registro inválido ('.$tipo.')');
		}
		
		#################################################################################
		## Verifica se existe classe para esse tipo
		#################################################################################
		$classe = "\\Zage\\Fin\\Arquivos\\TipoDado\\" . $tipo;
		if (class_exists($classe)) {
			$this->tipo	= new $classe;
		}else{
			die('Classe: '.$classe." não existe !!!");
		}
		
	}
	
	#################################################################################
	## Função de validação
	#################################################################################
	public function validar() {
		$this->tipo->validar();
	}

	/**
	 * @return the $ordem
	 */
	public function getOrdem() {
		return $this->ordem;
	}

	/**
	 * @param string $ordem
	 */
	public function setOrdem($ordem) {
		$this->ordem = $ordem;
	}

	/**
	 * @return the $posicaoInicial
	 */
	public function getPosicaoInicial() {
		return $this->posicaoInicial;
	}

	/**
	 * @param string $posicaoInicial
	 */
	public function setPosicaoInicial($posicaoInicial) {
		$this->posicaoInicial = $posicaoInicial;
	}

	/**
	 * @return the $posicaoFinal
	 */
	public function getPosicaoFinal() {
		return $this->posicaoFinal;
	}

	/**
	 * @param string $posicaoFinal
	 */
	public function setPosicaoFinal($posicaoFinal) {
		$this->posicaoFinal = $posicaoFinal;
	}

	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param string $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * @return the $tipo
	 */
	public function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param string $tipo
	 */
	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	/**
	 * @return the $tamanho
	 */
	public function getTamanho() {
		return $this->tipo->getTamanho();
	}

	/**
	 * @param string $tamanho
	 */
	public function setTamanho($tamanho) {
		$this->tipo->setTamanho($tamanho);
	}

	/**
	 * @return the $valor
	 */
	public function getValor() {
		return $this->tipo->getValor();
	}
	
	/**
	 * @param string $valor
	 */
	public function setValor($valor) {
		$this->tipo->setValor($valor);
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getVariavel() {
		return $this->variavel;
	}
	
	/**
	 *
	 * @param string $variavel        	
	 */
	public function setVariavel($variavel) {
		$this->variavel = $variavel;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getValorFixo() {
		return $this->valorFixo;
	}
	
	/**
	 *
	 * @param string $valorFixo        	
	 */
	public function setValorFixo($valorFixo) {
		$this->valorFixo = $valorFixo;
		return $this;
	}
	
	#################################################################################
	## Função de retornar o valor limpo de formatação
	#################################################################################
	public function getCleanVal() {
		return $this->tipo->getCleanVal();
	}
	
	
	
}

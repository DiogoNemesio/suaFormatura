<?php
namespace Zage\Fin\Arquivos\Layout;

/**
 * @package: \Zage\Fin\Arquivos\Layout\RetornoBancarioBoleto
 * @created: 28/01/2016
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar liquidações de boleto através de retornos bancários 
 */

class RetornoBancarioBoleto extends \Zage\Fin\Arquivos\Layout {
	
	/**
	 * Array de liquidacoes
	 *
	 * @var array
	 */
	public $liquidacoes = array();
	
	/**
	 * Código do Banco
	 *
	 * @var string
	 */
	private $codBanco;
	
	/**
	 * Conta Corrente
	 *
	 * @var string
	 */
	private $contaCorrente;
	
	/**
	 * Agência
	 *
	 * @var string
	 */
	private $agencia;
	
	/**
	 * Código identificador do Cedente junto ao banco
	 *
	 * @var string
	 */
	private $codCedente;
	
	/**
	 * Valor total Liquidado
	 *
	 * @var float
	 */
	private $valorTotalLiquidado;
	
	/**
	 * Número de liquidações
	 * @var int
	 */
	private $numLiquidacoes;
	
	/**
	 * Construtor
	 */
	public function __construct() {

		#################################################################################
		## Chama o construtor da classe mãe
		#################################################################################
		parent::__construct();
		
	}
	
	/**
	 * Carregar o array de liquidações
	 */
	//public abstract function geraLiquidacoes();
	
	/**
	 * Adicionar uma liquidação 
	 * @param date $dataLiquidacao
	 * @param string $nossoNumero
	 * @param integer $sequencial
	 * @param string $codLiquidacao
	 * @param float $valorBoleto
	 * @param float $valorPago
	 * @param float $valorDesconto
	 * @param float $valorIOF
	 * @param float $valorJuros
	 * @param float $valorLiquido
	 * @param float $valorOutrosCreditos
	 * @param float $valorOutrasDespesas
	 * @throws \Exception
	 */
	protected function adicionaLiquidacao($dataLiquidacao,$nossoNumero,$sequencial,$codLiquidacao,$valorBoleto,$valorPago,$valorDesconto,$valorIOF,$valorJuros,$valorLiquido,$valorOutrosCreditos,$valorOutrasDespesas) {
		global $log;
		
		#################################################################################
		## Calcula o próximo índice
		#################################################################################
		$i 			= sizeof($this->liquidacoes);
		
		#################################################################################
		## Instancia a classe de liquidação
		#################################################################################
		$this->liquidacoes[$i]		= new \Zage\Fin\Arquivos\Layout\RetornoBancarioBoleto\Liquidacao();
		$this->liquidacoes[$i]->setDataLiquidacao($dataLiquidacao);
		$this->liquidacoes[$i]->setNossoNumero($nossoNumero);
		$this->liquidacoes[$i]->setSequencial($sequencial);
		$this->liquidacoes[$i]->setCodLiquidacao($codLiquidacao);
		$this->liquidacoes[$i]->setValorBoleto($valorBoleto);
		$this->liquidacoes[$i]->setValorDesconto($valorDesconto);
		$this->liquidacoes[$i]->setValorIOF($valorIOF);
		$this->liquidacoes[$i]->setValorJuros($valorJuros);
		$this->liquidacoes[$i]->setValorLiquido($valorLiquido);
		$this->liquidacoes[$i]->setValorOutrasDespesas($valorOutrasDespesas);
		$this->liquidacoes[$i]->setValorOutrosCreditos($valorOutrosCreditos);
		$this->liquidacoes[$i]->setValorPago($valorPago);

		#################################################################################
		## Atualiza o número de liquidações
		#################################################################################
		$this->numLiquidacoes	= ($i + 1);
		
		
		return ($i);
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCodBanco() {
		return $this->codBanco;
	}
	
	/**
	 *
	 * @param string $codBanco        	
	 */
	public function setCodBanco($codBanco) {
		$this->codBanco = $codBanco;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getContaCorrente() {
		return $this->contaCorrente;
	}
	
	/**
	 *
	 * @param string $contaCorrente        	
	 */
	public function setContaCorrente($contaCorrente) {
		$this->contaCorrente = $contaCorrente;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getAgencia() {
		return $this->agencia;
	}
	
	/**
	 *
	 * @param string $agencia        	
	 */
	public function setAgencia($agencia) {
		$this->agencia = $agencia;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCodCedente() {
		return $this->codCedente;
	}
	
	/**
	 *
	 * @param string $codCedente        	
	 */
	public function setCodCedente($codCedente) {
		$this->codCedente = $codCedente;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorTotalLiquidado() {
		return $this->valorTotalLiquidado;
	}
	
	/**
	 *
	 * @param float $valorTotalLiquidado        	
	 */
	public function setValorTotalLiquidado($valorTotalLiquidado) {
		$this->valorTotalLiquidado = $valorTotalLiquidado;
		return $this;
	}
	
	/**
	 *
	 * @return the int
	 */
	public function getNumLiquidacoes() {
		return $this->numLiquidacoes;
	}
	
	
}
	
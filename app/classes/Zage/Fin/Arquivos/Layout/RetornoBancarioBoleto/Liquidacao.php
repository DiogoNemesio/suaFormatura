<?php
namespace Zage\Fin\Arquivos\Layout\RetornoBancarioBoleto;

/**
 * @package: \Zage\Fin\Arquivos\Layout\RetornoBancarioBoleto\Liquidacao
 * @created: 28/01/2016
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar uma liquidação de boleto através de retorno bancário 
 */

class Liquidacao {
	
	/**
	 * Data da Liquidação
	 *
	 * @var date
	 */
	private $dataLiquidacao;
	
	/**
	 * Valor do Boleto
	 *
	 * @var float
	 */
	private $valorBoleto;
	
	/**
	 * Valor Pago
	 *
	 * @var float
	 */
	private $valorPago;
	
	/**
	 * Valor do Desconto
	 *
	 * @var float
	 */
	private $valorDesconto;
	
	/**
	 * Valor do Iof
	 *
	 * @var float
	 */
	private $valorIOF;
	
	/**
	 * Valor de Juros/ Mora
	 *
	 * @var float
	 */
	private $valorJuros;
	
	/**
	 * Valor Líquido
	 *
	 * @var float
	 */
	private $valorLiquido;
	
	/**
	 * Valor de outros créditos
	 *
	 * @var float
	 */
	private $valorOutrosCreditos;
	

	/**
	 * Valor de outras despesas
	 *
	 * @var float
	 */
	private $valorOutrasDespesas;
	
	/**
	 * Nosso número
	 *
	 * @var string
	 */
	private $nossoNumero;
	
	/**
	 * Sequencial do registro
	 *
	 * @var integer
	 */
	private $sequencial;
	
	/**
	 * Código de liquidação
	 *
	 * @var string
	 */
	private $codLiquidacao;
	
	
	/**
	 * Construtor
	 */
	public function __construct() {
	}
	
	/**
	 *
	 * @return the date
	 */
	public function getDataLiquidacao() {
		return $this->dataLiquidacao;
	}
	
	/**
	 *
	 * @param date $dataLiquidacao        	
	 */
	public function setDataLiquidacao($dataLiquidacao) {
		$this->dataLiquidacao = $dataLiquidacao;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorBoleto() {
		return $this->valorBoleto;
	}
	
	/**
	 *
	 * @param float $valorBoleto        	
	 */
	public function setValorBoleto($valorBoleto) {
		$this->valorBoleto = $valorBoleto;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorPago() {
		return $this->valorPago;
	}
	
	/**
	 *
	 * @param float $valorPago        	
	 */
	public function setValorPago($valorPago) {
		$this->valorPago = $valorPago;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorDesconto() {
		return $this->valorDesconto;
	}
	
	/**
	 *
	 * @param float $valorDesconto        	
	 */
	public function setValorDesconto($valorDesconto) {
		$this->valorDesconto = $valorDesconto;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorIOF() {
		return $this->valorIOF;
	}
	
	/**
	 *
	 * @param float $valorIOF        	
	 */
	public function setValorIOF($valorIOF) {
		$this->valorIOF = $valorIOF;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorJuros() {
		return $this->valorJuros;
	}
	
	/**
	 *
	 * @param float $valorJuros        	
	 */
	public function setValorJuros($valorJuros) {
		$this->valorJuros = $valorJuros;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorLiquido() {
		return $this->valorLiquido;
	}
	
	/**
	 *
	 * @param float $valorLiquido        	
	 */
	public function setValorLiquido($valorLiquido) {
		$this->valorLiquido = $valorLiquido;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorOutrosCreditos() {
		return $this->valorOutrosCreditos;
	}
	
	/**
	 *
	 * @param float $valorOutrosCreditos        	
	 */
	public function setValorOutrosCreditos($valorOutrosCreditos) {
		$this->valorOutrosCreditos = $valorOutrosCreditos;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorOutrasDespesas() {
		return $this->valorOutrasDespesas;
	}
	
	/**
	 *
	 * @param float $valorOutrasDespesas        	
	 */
	public function setValorOutrasDespesas($valorOutrasDespesas) {
		$this->valorOutrasDespesas = $valorOutrasDespesas;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getNossoNumero() {
		return $this->nossoNumero;
	}
	
	/**
	 *
	 * @param string $nossoNumero        	
	 */
	public function setNossoNumero($nossoNumero) {
		$this->nossoNumero = $nossoNumero;
		return $this;
	}
	
	/**
	 *
	 * @return the integer
	 */
	public function getSequencial() {
		return $this->sequencial;
	}
	
	/**
	 *
	 * @param integer $sequencial        	
	 */
	public function setSequencial($sequencial) {
		$this->sequencial = $sequencial;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCodLiquidacao() {
		return $this->codLiquidacao;
	}
	
	/**
	 *
	 * @param string $codLiquidacao        	
	 */
	public function setCodLiquidacao($codLiquidacao) {
		$this->codLiquidacao = $codLiquidacao;
		return $this;
	}
	
	
	
}
	
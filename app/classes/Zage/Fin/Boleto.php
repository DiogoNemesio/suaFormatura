<?php

namespace Zage\Fin;

/**
 * Gerenciar Boletos
 * 
 * @package: Boleto
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Boleto {
	
	/**
	 * Código da Moeda (Real)
	 */
	const	COD_REAL	= 9;
	
	/**
	 * Código do banco
	 */
	private $codBanco;
	
	/**
	 * Vencimento do boleto
	 * @var data
	 */
	private $vencimento;
	
	/**
	 * Valor total do boleto
	 * @var float
	 */
	private $valor;
	
	/**
	 * Valor do juros
	 * @var float
	 */
	private $juros;
	
	/**
	 * Valor da Mora
	 * @var float
	 */
	private $mora;
	
	/**
	 * Valor do desconto
	 * @var float
	 */
	private $desconto;
	
	/**
	 * Outros Valores
	 * @var float
	 */
	private $outrosValores;
	
	/**
	 * Sequencial do Nosso Número do Boleto
	 * @var number
	 */
	private $sequencial;
	
	/**
	 * Nosso Número do Boleto
	 * o nosso numero é a identificação do boleto no banco e é aconselhável que ele não se repita para um mesmo cedente, embora alguns bancos permitam que isso aconteça.
	 * @var string
	 */
	private $nossoNumero;
	
	/**
	 * Número do Documento
	 * O número do documento existe apenas para fins de informação e o banco não faz nenhum controle sobre ele, é comum que seja utilizado para informar o número de uma nota fiscal de venda, mas pode ser utilizado para qualquer fim obedecendo apenas o número máximo de caracteres estipulado pelo banco.
	 * @var string
	 */
	private $numeroDocumento;
	
	/**
	 * Data de geração do documento 
	 * @var date
	 */
	private $dataDocumento;
	
	/**
	 * Nome do sacado
	 * O sacado é a pessoa para o qual o boleto está sendo emitido, podemos resumir dizendo que o sacado é o cliente do Cedente, ou aquele para o qual uma determina mercadoria foi vendida e o pagamento desta será efetuado por meio de boleto de cobrança.
	 * @var string
	 */
	private $sacadoNome;
	
	/**
	 * CNPJ/CPF do sacado
	 * @var string
	 */
	private $sacadoCNPJ;
	
	/**
	 * Endereço do sacado
	 * @var string
	 */
	private $sacadoEndereco;
	
	/**
	 * Cep do endereço do sacado
	 * @var string
	 */
	private $sacadoCep;
	
	/**
	 * Cidade do endereço do sacado
	 * @var string
	 */
	private $sacadoCidade;
	
	/**
	 * UF do endereço do sacado
	 * @var string
	 */
	private $sacadoUF;
	
	/**
	 * 1 Linha do Demonstrativo do Boleto 
	 * @var string
	 */
	private $demonstrativo1;
	
	/**
	 * 2 Linha do Demonstrativo do Boleto
	 * @var string
	 */
	private $demonstrativo2;

	/**
	 * 3 Linha do Demonstrativo do Boleto
	 * @var string
	 */
	private $demonstrativo3;
	
	/**
	 * 1 Linha das instruções do boleto 
	 * @var unknown
	 */
	private $instrucao1;
	
	/**
	 * 2 Linha das instruções do boleto 
	 * @var unknown
	 */
	private $instrucao2;
	
	/**
	 * 3 Linha das instruções do boleto
	 * @var unknown
	 */
	private $instrucao3;
	
	/**
	 * 4 Linha das instruções do boleto
	 * @var unknown
	 */
	private $instrucao4;
	
	/**
	 * Quantidade
	 * @var number
	 */
	private $quantidade;
	
	/**
	 * Valor unitário
	 * @var float
	 */
	private $valorUnitario;
	
	/**
	 * Aceite
	 * @var string
	 */
	private $aceite;
	
	/**
	 * Espécie
	 * @var string
	 */
	private $especie;
	
	/**
	 * Espécie do documento
	 * @var string
	 */
	private $especieDocumento;
	
	/**
	 * Agência
	 * @var string
	 */
	private $agencia;
	
	/**
	 * Dígito verificador da Agência
	 * @var string
	 */
	private $agenciaDigito;
	
	/**
	 * Conta Corrente
	 * @var string
	 */
	private $conta;
	
	/**
	 * Dígito verificador da conta
	 * @var string
	 */
	private $contaDigito;
	
	/**
	 * Código da carteira
	 * @var string
	 */
	private $carteira;
	
	/**
	 * Identificação do cedente do boleto
	 * @var string
	 */
	private $identificacao;
	
	/**
	 * CNPJ / Cpf do cedente do boleto
	 * @var string
	 */
	private $cnpj;
	
	/**
	 * Endereço do cedente do boleto
	 * @var string
	 */
	private $endereco;
	
	/**
	 * Cep do Endereço do cedente do boleto
	 * @var string
	 */
	private $cep;
	
	/**
	 * Cidade do cedente do boleto
	 * @var unknown
	 */
	private $cidade;
	
	/**
	 * UF do cedente do boleto
	 * @var unknown
	 */
	private $uf;
	
	/**
	 * Cedente
	 * O Cedente nada mais é do que o nome do titular da conta onde o boleto esta sendo emitido, em muitos casos pode-se utilizar um nome diferente do titilar da conta, porém é sempre bom consultar o banco.
	 * @var string
	 */
	private $cedente;
	
	/**
	 * Código do cedente junto a instituição bancária
	 * @var string
	 */
	private $codigoCedente;
	
	/**
	 * Linha digitável
	 * @var string
	 */
	private $linhaDigitavel;
	
	/**
	 * Objeto que vai receber a instância do OpenBoleto
	 * @var object
	 */
	private $_boleto;
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct($codBanco) {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
		
		$this->setCodBanco($codBanco);
		
		
	}
	
	/**
	 * Emitir o Boleto
	 */
	public function emitir() {
		global $log,$system;
		

		#################################################################################
		## Checa informações obrigatórias
		#################################################################################
		if (!$this->getVencimento()) 		throw new \Exception("Vencimento do boleto não informado");
		if (!$this->getValor()) 			throw new \Exception("Valor do boleto não informado");
		if (!$this->getSequencial()) 		throw new \Exception("Sequencial do Nosso Número do boleto não informado");
		if (!$this->getAgencia()) 			throw new \Exception("Agência não informada");
		if (!$this->getConta()) 			throw new \Exception("Conta corrente não informada");
		if (!$this->getCarteira()) 			throw new \Exception("Carteira não informada");
		
		#################################################################################
		## Fixar a espécie / Moeda
		#################################################################################
		$this->setEspecie(self::COD_REAL);
		
		
		#################################################################################
		## Criar o objeto do sacado
		#################################################################################
		$sacado 	= new \OpenBoleto\Agente($this->getSacadoNome(), $this->getSacadoCNPJ(), $this->getSacadoEndereco(),$this->getSacadoCep(),$this->getSacadoCidade(),$this->getSacadoUF());
		$cedente 	= new \OpenBoleto\Agente($this->getCedente(), $this->getCnpj(), $this->getEndereco(),$this->getCep(),$this->getCidade(),$this->getUf());
		
		#################################################################################
		## Formata as informações de vencimento / Valor
		#################################################################################
		$vencimento				= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $this->getVencimento());
		$valor					= $this->getValor();
		$juros					= $this->getJuros();
		$mora					= $this->getMora();
		$desconto				= $this->getDesconto();
		$outros					= $this->getOutrosValores();
		$valorTotal				= ($valor + $juros + $mora + $outros - $desconto);
		$valorJuros				= ($juros + $mora);
		
		#################################################################################
		## Resgata as informações da conta corrente
		#################################################################################
		$agencia				= $this->getAgencia();
		$agenciaDV				= $this->getAgenciaDigito();
		$ccorrenteDV			= $this->getContaDigito();
		$carteira				= $this->getCarteira();
		$ccorrente				= ($this->getCodigoCedente()) ? $this->getCodigoCedente() : $this->getConta();
		$ccorrenteDV			= ($this->getCodigoCedente()) ? \OpenBoleto\BoletoAbstract::modulo11($this->getCodigoCedente())["digito"] : $this->getConta();
		
		#################################################################################
		## Cria o array dos demonstrativos
		#################################################################################
		$aDemo				= array($this->getDemonstrativo1(),$this->getDemonstrativo2(),$this->getDemonstrativo3());
		
		#################################################################################
		## Cria o array das Instruções
		#################################################################################
		$aInst				= array($this->getInstrucao1(),$this->getInstrucao2(),$this->getInstrucao3(),$this->getInstrucao4());
		
		#################################################################################
		## Instanciar a classe do OpenBoleto de acordo com o código do banco
		#################################################################################
		switch ($this->getCodBanco()) {
				
			case '001':
			case '01':
			case '1':
				$nomeClasse		= "BancoDoBrasil";
				break;
					
			case '237':
				$nomeClasse		= "Bradesco";
				break;
			case '070':
			case '70':
				$nomeClasse		= "Brb";
				break;
			case '104':
				$nomeClasse		= "Caixa";
				break;
			case '341':
				$nomeClasse		= "Itau";
				break;
			case '033':
			case '33':
				$nomeClasse		= "Santander";
				break;
			case '090':
			case '90':
				$nomeClasse		= "Unicred";
				break;
			default:
				throw new \Exception("Banco '".$this->getCodBanco()."' ainda não implementado !");
				break;
		}
		
		#################################################################################
		## Montar o array de configuração
		#################################################################################
		$config	= array(
			'dataVencimento'			=> $vencimento,
			'valor' 					=> $valorTotal,
			'sequencial' 				=> $this->getSequencial(),
			'sacado' 					=> $sacado,
			'cedente'					=> $cedente,
			'agencia' 					=> $agencia,
			'agenciaDv' 				=> $agenciaDV,
			'carteira' 					=> $carteira,
			'conta' 					=> $ccorrente,
			'contaDv' 					=> $ccorrenteDV,
			'codigoCliente' 			=> $this->getCodigoCedente(),
			'numeroDocumento' 			=> $this->getNumeroDocumento(),
			'descricaoDemonstrativo' 	=> $aDemo,
			'instrucoes' 				=> $aInst,

			###### Parâmetros Opicionais #######
			'dataDocumento' 			=> new \DateTime(),
			'dataProcessamento' 		=> new \DateTime(),
			'moeda' 					=> $this->getEspecie(),
			'especieDoc' 				=> $this->getEspecieDocumento(),
			'descontosAbatimentos' 		=> $desconto,
			'outrosAcrescimos' 			=> $valorJuros,
			'valorCobrado' 				=> $valorTotal,
			'valorUnitario' 			=> $valor,
			'quantidade' 				=> $this->getQuantidade(),
			//'usoBanco' 				=> 'Uso banco',
			//'layout' 					=> 'layout.phtml',
			//'logoPath' 				=> 'http://boletophp.com.br/img/opensource-55x48-t.png',
			//'sacadorAvalista' 		=> new Agente('Antônio da Silva', '02.123.123/0001-11'),
			//'contraApresentacao' 		=> true,
			//'pagamentoMinimo' 		=> 23.00,
			//'aceite' 					=> 'N',
			//'moraMulta' 				=> $valorJuros,
			//'outrasDeducoes' 			=> $desconto,

			###### Configurações do OpenBoleto #######
			'resourcePath' 				=> CLASS_PATH  . '/OpenBoleto/resources',
		
		);
		
		//$log->debug("Config BOL: ".serialize($config));
		
		#################################################################################
		## Instanciar a classe do OpenBoleto
		#################################################################################
		$nomeClasse		= '\\OpenBoleto\\Banco\\'.$nomeClasse;
		$this->_boleto	= new $nomeClasse($config);	
		
		#################################################################################
		## Salvar a linha digitável
		#################################################################################
		$this->setLinhaDigitavel($this->_boleto->getLinhaDigitavel());
		
		#################################################################################
		## Salvar o nosso número
		#################################################################################
		$this->setNossoNumero($this->_boleto->getNossoNumero(true));
		
	}
	
	/**
	 * Retorna o html do boleto
	 */
	public function getHtml() {
		return $this->_boleto->getOutput();
	}
	
	/**
	 *
	 * @return the unknown_type
	 */
	public function getCodBanco() {
		return $this->codBanco;
	}
	
	/**
	 *
	 * @param unknown_type $codBanco        	
	 */
	public function setCodBanco($codBanco) {
		$this->codBanco = $codBanco;
		return $this;
	}
	
	/**
	 *
	 * @return the data
	 */
	public function getVencimento() {
		return $this->vencimento;
	}
	
	/**
	 *
	 * @param data $vencimento        	
	 */
	public function setVencimento($vencimento) {
		$this->vencimento = $vencimento;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValor() {
		return $this->valor;
	}
	
	/**
	 *
	 * @param $valor        	
	 */
	public function setValor($valor) {
		$this->valor = $valor;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getJuros() {
		return $this->juros;
	}
	
	/**
	 *
	 * @param float $juros        	
	 */
	public function setJuros($juros) {
		$this->juros = $juros;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getMora() {
		return $this->mora;
	}
	
	/**
	 *
	 * @param
	 *        	$mora
	 */
	public function setMora($mora) {
		$this->mora = $mora;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getDesconto() {
		return $this->desconto;
	}
	
	/**
	 *
	 * @param float $desconto        	
	 */
	public function setDesconto($desconto) {
		$this->desconto = $desconto;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getOutrosValores() {
		return $this->outrosValores;
	}
	
	/**
	 *
	 * @param float $outrosValores
	 */
	public function setOutrosValores($outrosValores) {
		$this->outrosValores = $outrosValores;
		return $this;
	}
	
	/**
	 *
	 * @return the number
	 */
	public function getSequencial() {
		return $this->sequencial;
	}
	
	/**
	 *
	 * @param number $sequencial        	
	 */
	public function setSequencial($sequencial) {
		$this->sequencial = $sequencial;
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
	 * @param $nossoNumero        	
	 */
	public function setNossoNumero($nossoNumero) {
		$this->nossoNumero = $nossoNumero;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getNumeroDocumento() {
		return $this->numeroDocumento;
	}
	
	/**
	 *
	 * @param $numeroDocumento        	
	 */
	public function setNumeroDocumento($numeroDocumento) {
		$this->numeroDocumento = $numeroDocumento;
		return $this;
	}
	
	/**
	 *
	 * @return the date
	 */
	public function getDataDocumento() {
		return $this->dataDocumento;
	}
	
	/**
	 *
	 * @param date $dataDocumento        	
	 */
	public function setDataDocumento($dataDocumento) {
		$this->dataDocumento = $dataDocumento;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoNome() {
		return $this->sacadoNome;
	}
	
	/**
	 *
	 * @param $sacadoNome        	
	 */
	public function setSacadoNome($sacadoNome) {
		$this->sacadoNome = $sacadoNome;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoCNPJ() {
		return $this->sacadoCNPJ;
	}
	
	/**
	 *
	 * @param $sacadoCNPJ        	
	 */
	public function setSacadoCNPJ($sacadoCNPJ) {
		$this->sacadoCNPJ = $sacadoCNPJ;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoEndereco() {
		return $this->sacadoEndereco;
	}
	
	/**
	 *
	 * @param $sacadoEndereco        	
	 */
	public function setSacadoEndereco($sacadoEndereco) {
		$this->sacadoEndereco = $sacadoEndereco;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoCep() {
		return $this->sacadoCep;
	}
	
	/**
	 *
	 * @param $sacadoCep        	
	 */
	public function setSacadoCep($sacadoCep) {
		$this->sacadoCep = $sacadoCep;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoCidade() {
		return $this->sacadoCidade;
	}
	
	/**
	 *
	 * @param $sacadoCidade        	
	 */
	public function setSacadoCidade($sacadoCidade) {
		$this->sacadoCidade = $sacadoCidade;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getSacadoUF() {
		return $this->sacadoUF;
	}
	
	/**
	 *
	 * @param $sacadoUF        	
	 */
	public function setSacadoUF($sacadoUF) {
		$this->sacadoUF = $sacadoUF;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getDemonstrativo1() {
		return $this->demonstrativo1;
	}
	
	/**
	 *
	 * @param $demonstrativo1        	
	 */
	public function setDemonstrativo1($demonstrativo1) {
		$this->demonstrativo1 = $demonstrativo1;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getDemonstrativo2() {
		return $this->demonstrativo2;
	}
	
	/**
	 *
	 * @param $demonstrativo2        	
	 */
	public function setDemonstrativo2($demonstrativo2) {
		$this->demonstrativo2 = $demonstrativo2;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getDemonstrativo3() {
		return $this->demonstrativo3;
	}
	
	/**
	 *
	 * @param $demonstrativo3        	
	 */
	public function setDemonstrativo3($demonstrativo3) {
		$this->demonstrativo3 = $demonstrativo3;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getInstrucao1() {
		return $this->instrucao1;
	}
	
	/**
	 *
	 * @param
	 *        	$instrucao1
	 */
	public function setInstrucao1($instrucao1) {
		$this->instrucao1 = $instrucao1;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getInstrucao2() {
		return $this->instrucao2;
	}
	
	/**
	 *
	 * @param
	 *        	$instrucao2
	 */
	public function setInstrucao2($instrucao2) {
		$this->instrucao2 = $instrucao2;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getInstrucao3() {
		return $this->instrucao3;
	}
	
	/**
	 *
	 * @param
	 *        	$instrucao3
	 */
	public function setInstrucao3($instrucao3) {
		$this->instrucao3 = $instrucao3;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getInstrucao4() {
		return $this->instrucao4;
	}
	
	/**
	 *
	 * @param
	 *        	$instrucao4
	 */
	public function setInstrucao4($instrucao4) {
		$this->instrucao4 = $instrucao4;
		return $this;
	}
	
	/**
	 *
	 * @return the number
	 */
	public function getQuantidade() {
		return $this->quantidade;
	}
	
	/**
	 *
	 * @param number $quantidade        	
	 */
	public function setQuantidade($quantidade) {
		$this->quantidade = $quantidade;
		return $this;
	}
	
	/**
	 *
	 * @return the float
	 */
	public function getValorUnitario() {
		return $this->valorUnitario;
	}
	
	/**
	 *
	 * @param $valorUnitario        	
	 */
	public function setValorUnitario($valorUnitario) {
		$this->valorUnitario = $valorUnitario;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getAceite() {
		return $this->aceite;
	}
	
	/**
	 *
	 * @param $aceite        	
	 */
	public function setAceite($aceite) {
		$this->aceite = $aceite;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getEspecie() {
		return $this->especie;
	}
	
	/**
	 *
	 * @param $especie        	
	 */
	public function setEspecie($especie) {
		$this->especie = $especie;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getEspecieDocumento() {
		return $this->especieDocumento;
	}
	
	/**
	 *
	 * @param $especieDocumento        	
	 */
	public function setEspecieDocumento($especieDocumento) {
		$this->especieDocumento = $especieDocumento;
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
	 * @param $agencia        	
	 */
	public function setAgencia($agencia) {
		$this->agencia = $agencia;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getAgenciaDigito() {
		return $this->agenciaDigito;
	}
	
	/**
	 *
	 * @param $agenciaDigito
	 */
	public function setAgenciaDigito($agenciaDigito) {
		$this->agenciaDigito = $agenciaDigito;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getConta() {
		return $this->conta;
	}
	
	/**
	 *
	 * @param $conta        	
	 */
	public function setConta($conta) {
		$this->conta = $conta;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getContaDigito() {
		return $this->contaDigito;
	}
	
	/**
	 *
	 * @param $contaDigito        	
	 */
	public function setContaDigito($contaDigito) {
		$this->contaDigito = $contaDigito;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCarteira() {
		return $this->carteira;
	}
	
	/**
	 *
	 * @param $carteira        	
	 */
	public function setCarteira($carteira) {
		$this->carteira = $carteira;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getIdentificacao() {
		return $this->identificacao;
	}
	
	/**
	 *
	 * @param $identificacao        	
	 */
	public function setIdentificacao($identificacao) {
		$this->identificacao = $identificacao;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCnpj() {
		return $this->cnpj;
	}
	
	/**
	 *
	 * @param $cnpj        	
	 */
	public function setCnpj($cnpj) {
		$this->cnpj = $cnpj;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getEndereco() {
		return $this->endereco;
	}
	
	/**
	 *
	 * @param $endereco        	
	 */
	public function setEndereco($endereco) {
		$this->endereco = $endereco;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCep() {
		return $this->cep;
	}
	
	/**
	 *
	 * @param $cep
	 */
	public function setCep($cep) {
		$this->cep = $cep;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getCidade() {
		return $this->cidade;
	}
	
	/**
	 *
	 * @param
	 *        	$cidade
	 */
	public function setCidade($cidade) {
		$this->cidade = $cidade;
		return $this;
	}
	
	/**
	 *
	 * @return the unknown
	 */
	public function getUf() {
		return $this->uf;
	}
	
	/**
	 *
	 * @param
	 *        	$uf
	 */
	public function setUf($uf) {
		$this->uf = $uf;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCedente() {
		return $this->cedente;
	}
	
	/**
	 *
	 * @param $cedente        	
	 */
	public function setCedente($cedente) {
		$this->cedente = $cedente;
		return $this;
	}
	
	/**
	 *
	 * @return the string
	 */
	public function getCodigoCedente() {
		return $this->codigoCedente;
	}
	
	/**
	 *
	 * @param string $codigoCedente        	
	 */
	public function setCodigoCedente($codigoCedente) {
		$this->codigoCedente = $codigoCedente;
		return $this;
	}
		
	/**
	 *
	 * @return the string
	 */
	public function getLinhaDigitavel() {
		return $this->linhaDigitavel;
	}
	
	/**
	 *
	 * @param string $linhaDigitavel
	 */
	protected function setLinhaDigitavel($linhaDigitavel) {
		$this->linhaDigitavel = $linhaDigitavel;
		return $this;
	}
	
}
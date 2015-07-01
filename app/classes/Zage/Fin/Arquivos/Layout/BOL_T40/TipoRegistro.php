<?php
namespace Zage\Fin\Arquivos\Layout\BOL_T40;

/**
 * @package: \Zage\Fin\Arquivos\Layout\BOL_T40\TipoRegistro
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os Tipos de Registro
 */

abstract class TipoRegistro {
	
	
	/**
	 * Tipo do arquivo PTU
	 *
	 * @var string
	 */
	private $tipoRegistro;
	
	/**
	 * Campos
	 *
	 * @var array
	 */
	public $campos = array();
	
	/**
	 * Linha
	 *
	 * @var number
	 */
	private $linha;
	
	/**
	 * Tamanho
	 *
	 * @var number
	 */
	private $tamanho;
	
	/**
	 * Descrição
	 *
	 * @var number
	 */
	private $nome;
	
	/**
	 * Código do Layout
	 *
	 * @var string
	 */
	private $codLayout;
	
	/**
	 * Tipo de Arquivo
	 *
	 * @var string
	 */
	private $tipoArquivo;
	
	/**
	 * Indicador de tamanho Fixo
	 * 
	 * @var boolean
	 */
	private $indTamanhoFixo;
	
	/**
	 * Construtor
	 */
	public function __construct() {
	
	}
	
	#################################################################################
	## Adicionar um campo
	#################################################################################
	public function adicionaCampo($ordem,$posicaoInicial,$posicaoFinal,$nome,$tipo,$tamanho) {

		#################################################################################
		## Validação dos campos
		#################################################################################
		if ( (empty($ordem)) || (!is_int($ordem)) ) {
			die('Parâmetro Ordem ('.$ordem.') nulo ou inválido !!!');
		}
		
		if ($tamanho != "V") {
			if ( (empty($posicaoInicial)) || (!is_integer($posicaoInicial)) ) {
				die('Parâmetro posicaoInicial nulo ou inválido !!!');
			}
		
			if ( (empty($posicaoFinal)) || (!is_integer($posicaoFinal)) ) {
				die('Parâmetro posicaoFinal nulo ou inválido !!!');
			}
		
			if ( $posicaoInicial > $posicaoFinal ) {
				die('Parâmetro Posição Final deve ser maior que a Posição inicial');
			}
		}
		
		#################################################################################
		## Verifica se a sequencia já existe
		#################################################################################
		if (array_key_exists($ordem, $this->campos)) {
			die('Ordem já existente !!!');
		}

		#################################################################################
		## Cria o campo
		#################################################################################
		$this->campos[$ordem]	= new \Zage\Fin\Arquivos\Campo($tipo);
		$this->campos[$ordem]->setOrdem($ordem);
		$this->campos[$ordem]->setPosicaoInicial($posicaoInicial);
		$this->campos[$ordem]->setPosicaoFinal($posicaoFinal);
		$this->campos[$ordem]->setNome($nome);
		$this->campos[$ordem]->setTamanho($tamanho);
		$this->campos[$ordem]->setUso($uso);
		
	}
	
	#################################################################################
	## Adiciona um campo
	#################################################################################
	public function _adicionaCampo($tamanho,$nome,$tipo) {
		$i = sizeof($this->campos);
		
		if ($i == 0) {
			$ordem			= 1;
			$posicaoInicial = 1;
			$posicaoFinal	= $tamanho;
		}else{
			$ordem			= $i + 1;
			$posicaoInicial	= $this->campos[$i]->getPosicaoFinal() + 1;
			$posicaoFinal	= $this->campos[$i]->getPosicaoFinal() + $tamanho;
		}
		$this->adicionaCampo($ordem, $posicaoInicial, $posicaoFinal, $nome, $tipo, $tamanho);
	}
	
	#################################################################################
	## Carregar as configurações dos campos a partir do banco
	#################################################################################
	public function carregarCampos() {
		$campos		= $this->_listaCampos();
		
		for ($i = 0; $i < sizeof($campos); $i++) {
			$this->_adicionaCampo(
				$campos[$i]->getTamanho(),
				$campos[$i]->getNome(),
				$campos[$i]->getCodFormato()->getCodigo()
			);
		}
	}
	
	#################################################################################
	## Resgatar as configurações do tipo de registro no banco
	#################################################################################
	public function setConfigFromDB() {
		global $em;
		
		#################################################################################
		## Verifica se as configurações iniciais foram atribuídas
		#################################################################################
		if (!$this->getTipoRegistro()) 	throw new \Exception('Tipo de Registro não definido !!! '.__FILE__);
		if (!$this->getTipoArquivo()) 	throw new \Exception('Tipo de Arquivo não definido !!! '.__FILE__);
		
		#################################################################################
		# Resgata as informações do tipo de registro do banco
		#################################################################################
		$info	= $em->getRepository('\Entidades\ZgfinArquivoRegistroTipo')->findOneBy(array('codTipoRegistro' => $this->getTipoRegistro(),'codTipoArquivo' => $this->getTipoArquivo()));
		
		if ($info !== null) {
			$this->setNome($info->getNome());
			$this->setTamanho($info->getCodTipoArquivo()->getTamanho());
			$this->setIndTamanhoFixo($info->getCodTipoArquivo()->getIndTamanhoFixo());
		}else{
			\Exception('Configurações do Tipo de Registro não encontradas !!!'.__FILE__);
		}
		
	}
	
	/**
	 * @return the $tipoRegistro
	 */
	public function getTipoRegistro() {
		return $this->tipoRegistro;
	}

	/**
	 * @param string $tipoRegistro
	 */
	public function setTipoRegistro($tipoRegistro) {
		$this->tipoRegistro = $tipoRegistro;
	}
	
	#################################################################################
	## Validar o Registro
	#################################################################################
	public function validar() {
		global $system;
		$registro = "";
		foreach ($this->campos as $campo) {
			$campo->tipo->completar();
			if ($campo->tipo->validar() == false) {
				$erro	= new \Zage\Fin\Arquivos\Erro();
				$erro->setOrdem($campo->getOrdem());
				$erro->setTipoRegistro($this->getTipoRegistro());
				$erro->setMensagem($campo->tipo->getMensagemInvalido());
				return $erro;
			}else{
				$registro .= $campo->getValor();
			}
		}
		
		if ($this->getTamanho() != "V") {
			if (mb_strlen($registro,$system->config["database"]["charset"]) !== $this->getTamanho()) {
				$erro	= new \Zage\Fin\Arquivos\Erro();
				$erro->setOrdem(0);
				$erro->setTipoRegistro($this->getTipoRegistro());
				$erro->setMensagem("Tamanho do registro (".mb_strlen($registro,$system->config["database"]["charset"]).") inválido, deveria ser (".$this->getTamanho().")" );
				return $erro;
			}
		}
		return true;
	}
	
	#################################################################################
	## Carregar a Linha em memória
	#################################################################################
	public function carregaLinha($linha) {
		if ($this->getTamanho() !== mb_strlen($linha,$system->config["database"]["charset"])) {
			return 'Número de caracteres imcompatível com o tipo de registro ('.strlen($linha).') esperado: ('.$this->getTamanho().')';
		}
		foreach ($this->campos as $campo) {
			$campo->setValor(substr($linha,$campo->getPosicaoInicial()-1,$campo->getTamanho()));
		}
		
		return true;
	}
	
	#################################################################################
	## Carregar a Linha em memória
	#################################################################################
	public function carregaLinhaDebug($linha) {
		foreach ($this->campos as $campo) {
			$campo->setValor(substr($linha,$campo->getPosicaoInicial()-1,$campo->getTamanho()));
		}
	
		return true;
	}
	
	
	#################################################################################
	## Retorna o Registro
	#################################################################################
	public function getRegistro() {
		$registro = "";
		foreach ($this->campos as $campo) {
			$campo->tipo->completar();
			$registro .= $campo->getValor();
		}
		return ($registro);
	}
	
	#################################################################################
	## Mostrar os campos
	#################################################################################
	public function mostraCampos() {
		echo "Tipo: ".$this->getTipoRegistro()."<br>";
		echo "<table><tr><th>Seq</th><th>Pos.Ini</th><th>Pos.Fim</th><th>Elemento Dado</th><th>Descrição</th><th>Tipo</th><th>Tam</th><th>Uso</th></tr>";
		foreach ($this->campos as $campo) {
			echo "<tr><td>".$campo->getSequencia()."</td><td>".$campo->getPosicaoInicial()."</td><td>".$campo->getPosicaoFinal()."</td><td>".$campo->getElementoDado()."</td><td>".$campo->getDescricao()."</td><td>".$campo->getTipo()->getNome()."</td><td>".$campo->getTamanho()."</td><td>".$campo->getUso()."</td></tr>";
		}
		echo "</table><br>";
	}
	
	/**
	 * Retorna o valor de uma sequencia do registro
	 * @param integer $ordem
	 * @return string
	 */
	public function getValor($ordem) {
		#################################################################################
		## Verifica se a sequencia já existe
		#################################################################################
		if (array_key_exists($ordem, $this->campos)) {
			return ($this->campos[$ordem]->getValor());
		}else{
			return null;
		}
	}

	
	/**
	 * Definir o valor de uma sequencia do registro
	 * @param integer $ordem
	 * @param string $valor
	 * @return void
	 */
	public function setValor($ordem,$valor) {
		#################################################################################
		## Verifica se a sequencia já existe
		#################################################################################
		if (array_key_exists($ordem, $this->campos)) {
			$this->campos[$ordem]->setValor($valor);
		}
	
	}
	
	/**
	 * @return the $linha
	 */
	public function getLinha() {
		return $this->linha;
	}

	/**
	 * @param number $linha
	 */
	public function setLinha($linha) {
		$this->linha = $linha;
	}
	
	/**
	 * @return the $tamanho
	 */
	public function getTamanho() {
		return $this->tamanho;
	}

	/**
	 * @param number $tamanho
	 */
	public function setTamanho($tamanho) {
		$this->tamanho = $tamanho;
	}
	
	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param number $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}
	
	/**
	 * @return the $codLayout
	 */
	protected function getCodLayout() {
		return $this->codLayout;
	}

	/**
	 * @param string $codLayout
	 */
	protected function setCodLayout($codLayout) {
		$this->codLayout = $codLayout;
	}
	
	/**
	 * @return the $tipoArquivo
	 */
	public function getTipoArquivo() {
		return $this->tipoArquivo;
	}
	
	/**
	 * @param string $tipoArquivo
	 */
	public function setTipoArquivo($tipoArquivo) {
		$this->tipoArquivo = $tipoArquivo;
	}
	
	/**
	 *
	 * @return the boolean
	 */
	public function getIndTamanhoFixo() {
		return $this->indTamanhoFixo;
	}
	
	/**
	 *
	 * @param boolean $indTamanhoFixo        	
	 */
	public function setIndTamanhoFixo($indTamanhoFixo) {
		$this->indTamanhoFixo = $indTamanhoFixo;
	}
	
	/**
	 * Listar os campos para esse tipo de registro
	 */
	private function _listaCampos() {
		if (!$this->getCodLayout()) 	throw new Exception('Código do Layout não definido !!! '.__FILE__);
		if (!$this->getTipoRegistro()) 	throw new Exception('Tipo do registro não definido !!! '.__FILE__);
	
		$campos		= $em->getRepository('\Entidades\ZgfinArquivoLayoutRegistro')->findBy(array('codLayout' => $this->getCodLayout(),'codTipoRegistro' => $this->getTipoRegistro()),array('ordem' => "ASC"));
		return $campos;
	
	}
	
}

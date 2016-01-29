<?php
namespace Zage\Fin\Arquivos\Layout;

/**
 * @package: \Zage\Fin\Arquivos\Layout\BOL_T400
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os arquivos do Layout BOL_T400
 */

class BOL_T400 extends \Zage\Fin\Arquivos\Layout\RetornoBancarioBoleto {
	
	/**
	 * Array com os campos do header
	 * @var array
	 */
	protected $header;
	
	/**
	 * Array com os campos do trailler
	 * @var array
	 */
	protected $trailler;
	
	/**
	 * Array com os detalhes
	 * @var array
	 */
	protected $detalhes;
	
	#################################################################################
	## Construtor
	#################################################################################
	public function __construct() {
		global $em;
		
		#################################################################################
		## Chama o construtor da classe mãe
		#################################################################################
		parent::__construct();
		
		#################################################################################
		## Define o tipo do Arquivo
		#################################################################################
		$this->setCodTipoLayout("BOL_T400");
		
		#################################################################################
		## Descobre o Tipo de arquivo através do Layout
		#################################################################################
		$info		= $em->getRepository('\Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $this->getCodTipoLayout()));
		if (!$info)	{
			$this->adicionaErro(0, 0, null, 'Configuração do Layout "'.$this->getCodTipoLayout().'" não encontradas !!! ');
			throw new \Exception('Configuração do Layout "'.$this->getCodTipoLayout().'" não encontradas !!! ');
		}
		$this->setCodTipoArquivo($info->getCodTipoArquivo()->getCodigo());
		$this->setNome($info->getNome());
		
		#################################################################################
		## Carrega os tipos de registros 
		#################################################################################
		$tipos		= $em->getRepository('\Entidades\ZgfinArquivoRegistroTipo')->findBy(array('codTipoArquivo' => $this->getCodTipoArquivo()),array('codTipoRegistro' => "ASC"));
		for ($i = 0; $i < sizeof($tipos); $i++) {
			$this->_tiposRegistro["R".$tipos[$i]->getCodTipoRegistro()] = $tipos[$i]->getNome();
		}

		#################################################################################
		## Inicializa o array de detalhes
		#################################################################################
		$this->detalhes		= array();
	
	}
	
	#################################################################################
	## Validar o arquivo PTU
	#################################################################################
	public function valida ($codFila) {
		global $log;
		
		#################################################################################
		## Zera os contadores de quantidade de registros
		#################################################################################
		$linha			= 0;
		$numDetalhes	= 0;
		
		#################################################################################
		## Alterar o status para Validando
		#################################################################################
		\Zage\App\Fila::alteraStatus($codFila, 'V');
		
		foreach ($this->registros as $reg) {
			
			#################################################################################
			## Descobre o tipo de Registro
			#################################################################################
			$tipoReg	= $reg->getTipoRegistro();
			$linha++;
			
			#################################################################################
			## Alterar a linha atual
			#################################################################################
			\Zage\App\Fila::alteraLinhaAtual($codFila, $linha);
			
			#################################################################################
			## Faz a validação do registro (tipo de dados, tamanho e etc ...)
			#################################################################################
			$valido	= $reg->validar();
			if ($valido !== true)	$this->adicionaErro(0, $reg->getLinha(), $reg->getTipoRegistro(), $valido);
			
			#################################################################################
			## Verifica se a primeira linha é o header
			#################################################################################
			if (($linha == 1) && ($tipoReg !== '0')) {
				$this->adicionaErro(0, $reg->getLinha(), $reg->getTipoRegistro(), 'Header não encontrado');
			}
			
			#################################################################################
			## Verifica o tipo de arquivo, para fazer a devida validação
			#################################################################################
			switch ($tipoReg) {
				 
				#################################################################################
				## Header
				#################################################################################
				case '0':
					#################################################################################
					## Salva os campos do header
					#################################################################################
					foreach ($reg->campos as $campo) {
						$var	= $campo->getVariavel();
						if ($var) {
							$this->header[$var]	= $campo->getCleanVal();
						}
					}
						
				break;
				
				#################################################################################
				## Detalhes
				#################################################################################
				case '1':
					$numDetalhes++;
					
					#################################################################################
					## Salva os detalhes com as variáveis
					#################################################################################
					$i	= sizeof($this->detalhes);
					foreach ($reg->campos as $campo) {
						$var	= $campo->getVariavel();
						if ($var) {
							$this->detalhes[$i][$var]	= $campo->getCleanVal();
						}
					}
					
				break;

				#################################################################################
				## Trailler
				#################################################################################
				case '9':
					#################################################################################
					## Salva os campos do trailler
					#################################################################################
					foreach ($reg->campos as $campo) {
						$var	= $campo->getVariavel();
						if ($var) {
							$this->trailler[$var]	= $campo->getCleanVal();
						}
					}
					
				break;
			}
			
		}
		
		#################################################################################
		## Validação geral do arquivo
		#################################################################################
		if (empty($this->header))			$this->adicionaErro(0, 0, "0", 'As informações do Header não foram encontradas');
		if (empty($this->trailler))			$this->adicionaErro(0, 0, "9", 'As informações do trailler não foram encontradas');

		#################################################################################
		## Verifica a quantidade de registros
		#################################################################################
		if (sizeof($this->detalhes)	== 0)	$this->adicionaErro(0, 0, "1", 'As informações dos registros de detalhes não foram encontradas');
		
		#################################################################################
		## Verifica as variáveis obrigatórias
		#################################################################################
		if (!isset($this->header["AGENCIA"]) 		|| empty($this->header["AGENCIA"]))			$this->adicionaErro(0, 0, "0", 'Variável "Agência" não encontrada no Header');
		//if (!isset($this->header["CONTA_CORRENTE"]) || empty($this->header["CONTA_CORRENTE"]))	$this->adicionaErro(0, 0, "0", 'Variável "CONTA_CORRENTE" não encontrada no Header');
	
	}
	
	/**
	 * Carregar o array de liquidações
	 */
	public final function geraLiquidacoes() {
		
		#################################################################################
		## Verificar se o arquivo está válido
		#################################################################################
		if ($this->estaValido() !== true) 	throw new \Exception('Arquivo não está válido, portanto não pode ser gerada as liquidações');
		
		#################################################################################
		## Salvar as informações do Header
		#################################################################################
		if (isset($this->header["AGENCIA"]))		$this->setAgencia($this->header["AGENCIA"]);
		if (isset($this->header["CODIGO_BANCO"]))	$this->setCodBanco($this->header["CODIGO_BANCO"]);
		if (isset($this->header["COD_CEDENTE"]))	$this->setCodCedente($this->header["COD_CEDENTE"]);
		if (isset($this->header["CONTA_CORRENTE"]))	$this->setContaCorrente($this->header["CONTA_CORRENTE"]);
		
		#################################################################################
		## Limpar o registro de header para liberar memória
		#################################################################################
		$this->header	= null;
		unset($this->header);
		
		#################################################################################
		## Limpar o registro de trailler para liberar memória
		#################################################################################
		$this->trailler	= null;
		unset($this->trailler);
		
		#################################################################################
		## Salvar as informações dos detalhes
		#################################################################################
		for ($i = 0; $i < sizeof($this->detalhes); $i++) {
			
			$nossoNumero			= (isset($this->detalhes[$i]["NOSSO_NUMERO"])) 			? $this->detalhes[$i]["NOSSO_NUMERO"] 			: null;
			$sequencial				= (isset($this->detalhes[$i]["SEQUENCIAL_REGISTRO"]))	? $this->detalhes[$i]["SEQUENCIAL_REGISTRO"]	: null;
			$codLiquidacao			= (isset($this->detalhes[$i]["CODIGO_LIQUIDACAO"]))		? $this->detalhes[$i]["CODIGO_LIQUIDACAO"]		: null;
			$dataLiquidacao			= (isset($this->detalhes[$i]["DATA_OCORRENCIA"]))		? $this->detalhes[$i]["DATA_OCORRENCIA"]		: null;
			$valorPago				= (isset($this->detalhes[$i]["VALOR"]))					? $this->detalhes[$i]["VALOR"]					: null;
			$valorIOF				= (isset($this->detalhes[$i]["VALOR_IOF"]))				? $this->detalhes[$i]["VALOR_IOF"]				: null;
			$valorBoleto			= (isset($this->detalhes[$i]["VALOR_PRINCIPAL"]))		? $this->detalhes[$i]["VALOR_PRINCIPAL"]		: null;
			$valorDesconto			= (isset($this->detalhes[$i]["VALOR_DESCONTO"]))		? $this->detalhes[$i]["VALOR_DESCONTO"]			: null;
			$valorJuros				= (isset($this->detalhes[$i]["VALOR_JUROS"]))			? $this->detalhes[$i]["VALOR_JUROS"]			: null;
			$valorLiquido			= (isset($this->detalhes[$i]["VALOR_LIQUIDO"]))			? $this->detalhes[$i]["VALOR_LIQUIDO"]			: null;
			$valorOutrosCreditos	= (isset($this->detalhes[$i]["VALOR_OUTROS_CREDITOS"]))	? $this->detalhes[$i]["VALOR_OUTROS_CREDITOS"]	: null;
			$valorOutrasDespesas	= (isset($this->detalhes[$i]["VALOR_OUTRAS_DESPESAS"]))	? $this->detalhes[$i]["VALOR_OUTRAS_DESPESAS"]	: null;
				
			#################################################################################
			## Criar a liquidação
			#################################################################################
			$this->adicionaLiquidacao($dataLiquidacao,$nossoNumero,$sequencial,$codLiquidacao,$valorBoleto,$valorPago,$valorDesconto,$valorIOF,$valorJuros,$valorLiquido,$valorOutrosCreditos,$valorOutrasDespesas);
			
			#################################################################################
			## Limpar o registro de detalhe para liberar memória
			#################################################################################
			$this->detalhes[$i]		= null;
			unset($this->detalhes[$i]);
		}
	}
	
	
	/**
	 * Carregar um arquivo PTU
	 */
	public function loadFile ($arquivo) {
		
		#################################################################################
		## Verifica se o arquivo existe
		#################################################################################
		if (!file_exists($arquivo)) 	{
			$this->adicionaErro(0, 0, null, 'Arquivo não encontrado ('.$arquivo.') ');
			throw new \Exception('Arquivo não encontrado ('.$arquivo.') ');
		}

		#################################################################################
		## Verifica se o arquivo pode ser lido
		#################################################################################
		if (!is_readable($arquivo)) 	{
			$this->adicionaErro(0, 0, null, 'Arquivo não pode ser lido ('.$arquivo.') ');
			throw new \Exception('Arquivo não pode ser lido ('.$arquivo.') ');
		}

		#################################################################################
		## Lê o arquivo
		#################################################################################
		$lines	= file($arquivo);
		
		#################################################################################
		## Verifica se o arquivo tem informação
		#################################################################################
		if (sizeof($lines) < 2) {
			$this->adicionaErro(0, 0, null, 'Arquivo sem informações ('.$arquivo.') ');
			throw new \Exception('Arquivo sem informações ('.$arquivo.') ');
		}
		 
		#################################################################################
		## Percorre as linhas do arquivo
		#################################################################################
		for ($i = 0; $i < sizeof($lines); $i++) {

			$tipoReg	= "R".substr($lines[$i],0 ,1);
			$linha		= str_replace(array("\n", "\r"), '', $lines[$i]); 
			$reg		= $this->adicionaRegistro($tipoReg);
			if ($reg === null) {
				$this->adicionaErro(0, $i+1, null, "Linha fora do padrão definido");
				return;
			}else{
				$ok			= $this->registros[$reg]->carregaLinha($linha);
			}
			
			if ($ok !== true) 	$this->adicionaErro(0, $this->registros[$reg]->getLinha(), $this->registros[$reg]->getTipoRegistro(), $ok);
		}

	}
	
	
}

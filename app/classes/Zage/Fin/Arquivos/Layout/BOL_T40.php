<?php
namespace Zage\Fin\Arquivos\Layout;

/**
 * @package: \Zage\Fin\Arquivos\Layout\BOL_T40
 * @created: 30/06/2015
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os arquivos do Layout BOL_T40
 */

class BOL_T40 extends \Zage\Fin\Arquivos\Layout {
	
	
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
		$this->setCodTipoLayout("BOL_T40");
		
		#################################################################################
		## Descobre o Tipo de arquivo através do Layout
		#################################################################################
		$info		= $em->getRepository('\Entidades\ZgfinArquivoLayoutTipo')->findOneBy(array('codigo' => $this->getCodTipoLayout()));
		if (!$info)	{
			$this->_resumo->adicionaErro(0, 0, null, 'Configuração do Layout "'.$this->getCodTipoLayout().'" não encontradas !!! ');
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

	}
	
	#################################################################################
	## Validar o arquivo PTU
	#################################################################################
	public function valida () {
		global $log;
		
		#################################################################################
		## Zera os contadores de quantidade de registros
		#################################################################################
		$linha			= 0;
		$numDetalhes	= 0;
		
		foreach ($this->registros as $reg) {
			
			#################################################################################
			## Descobre o tipo de Registro
			#################################################################################
			$tipoReg	= $reg->getTipoRegistro();
			$linha++;
			
			#################################################################################
			## Faz a validação do registro (tipo de dados, tamanho e etc ...)
			#################################################################################
			$valido	= $reg->validar();
			if ($valido !== true)	$this->_resumo->adicionaErro(0, $reg->getLinha(), $reg->getTipoRegistro(), $valido);
			
			#################################################################################
			## Verifica se a primeira linha é o header
			#################################################################################
			if (($linha == 1) && ($tipoReg !== '0')) {
				$this->_resumo->adicionaErro(0, $reg->getLinha(), $reg->getTipoRegistro(), 'Header não encontrado');
			}
			
			/*
			 * 
			 * 
			 * 
			 * 
			 * 
			 * 
			 * Executar comando para atualizar a linha atual
			 * 
			 * 
			 * 
			 * 
			 * 
			 */
			
			#################################################################################
			## Verifica o tipo de arquivo, para fazer a devida validação
			#################################################################################
			switch ($tipoReg) {
				 
				#################################################################################
				## Header
				#################################################################################
				case '0':
					#################################################################################
					## 
					#################################################################################

				break;
				
				#################################################################################
				## Detalhes
				#################################################################################
				case '1':
					$numDetalhes++;
					
					#################################################################################
					## 
					#################################################################################
					
					
				break;

				#################################################################################
				## Trailler
				#################################################################################
				case '9':
					#################################################################################
					##
					#################################################################################

				break;
			}
			
		}
		
		#################################################################################
		## Validação geral do arquivo
		#################################################################################

		#################################################################################
		## Verifica a quantidade de registros
		#################################################################################
		
	}
	
	/**
	 * Carregar um arquivo PTU
	 */
	public function loadFile ($arquivo) {
		
		#################################################################################
		## Verifica se o arquivo existe
		#################################################################################
		if (!file_exists($arquivo)) 	{
			$this->_resumo->adicionaErro(0, 0, null, 'Arquivo não encontrado ('.$arquivo.') ');
			throw new \Exception('Arquivo não encontrado ('.$arquivo.') ');
		}

		#################################################################################
		## Verifica se o arquivo pode ser lido
		#################################################################################
		if (!is_readable($arquivo)) 	{
			$this->_resumo->adicionaErro(0, 0, null, 'Arquivo não pode ser lido ('.$arquivo.') ');
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
			$this->_resumo->adicionaErro(0, 0, null, 'Arquivo sem informações ('.$arquivo.') ');
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
				$this->_resumo->adicionaErro(0, $i+1, null, "Linha fora do padrão definido");
				return;
			}else{
				$ok			= $this->registros[$reg]->carregaLinha($linha);
			}
			
			if ($ok !== true) 	$this->_resumo->adicionaErro(0, $this->registros[$reg]->getLinha(), $this->registros[$reg]->getTipoRegistro(), $ok);
		}

	}
	
	
}

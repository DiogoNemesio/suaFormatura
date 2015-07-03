<?php

namespace Zage\App;

/**
 * Fila
 * 
 * @package: Fila
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Fila {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Buscar um módulo através do Apelido
     *
     * @param integer $ident
     * @return array
     */
	public static function cadastrar($modulo,$arquivo,$codTipoArq,$atividade,$variavel) {
		global $em,$system,$_user,$log;
	
		
		#################################################################################
		## Resgata os objetos
		#################################################################################
		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
		$oMod		= \Zage\App\Modulo::buscaPorApelido($modulo);
		$oTipoArq	= $em->getRepository('Entidades\ZgappImportacaoArquivoTipo')->findOneBy(array('codigo' => $codTipoArq));
		$oStatus	= $em->getRepository('Entidades\ZgappImportacaoStatusTipo')->findOneBy(array('codigo' => 'A'));
		$oAtividade	= \Zage\Utl\Atividade::buscaPorIdentificacao($atividade);
		
		#################################################################################
		## Validar os objetos
		#################################################################################
		if (!$oOrg)			throw new \Exception(sprintf('Organização não encontrada "%s" !!!', $system->getCodOrganizacao()));
		if (!$oMod)			throw new \Exception(sprintf('Módulo não encontrado "%s" !!!', $modulo));
		if (!$oTipoArq)		throw new \Exception(sprintf('Tipo de Arquivo não encontrado "%s" !!!', $codTipoArq));
		if (!$oStatus)		throw new \Exception(sprintf('Status não encontrado "%s" !!!', "A"));
		if (!$oAtividade)	throw new \Exception(sprintf('Atividade não encontrada "%s" !!!', $atividade));
		
		#################################################################################
		## Resgata as informações do arquivo
		#################################################################################
		$nomeArquivo	= basename($arquivo);
		$tamArq			= filesize($arquivo);
		$numLinhas		= self::calculaNumLinhas($arquivo);
			
		
		#################################################################################
		## Salvar na fila
		#################################################################################
		$fila		= new \Entidades\ZgappFilaImportacao();
		$fila->setArquivo($arquivo);
		$fila->setBytes($tamArq);
		$fila->setCodAtividade($oAtividade);
		$fila->setCodModulo($oMod);
		$fila->setCodOrganizacao($oOrg);
		$fila->setCodStatus($oStatus);
		$fila->setCodTipoArquivo($oTipoArq);
		$fila->setCodUsuario($_user);
		$fila->setDataImportacao(new \DateTime());
		$fila->setNome($nomeArquivo);
		$fila->setNumLinhas($numLinhas);
		$fila->setVariavel($variavel);
		
		try {
			$em->persist($fila);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
			
	}
	
	/**
	 * Setar o status para reprocessar o item da fila
	 * @param number $codFila
	 */
	public static function reprocessar($codFila) {
		self::alteraStatus($codFila, "A");
	}
	
	/**
	 * Setar o status para cancelado no item da fila
	 * @param number $codFila
	 */
	public static function cancelar($codFila) {
		self::alteraStatus($codFila, "C");
	}
	
	/**
	 * Excluir o item da fila
	 * @param number $codFila
	 */
	public static function excluir($codFila) {
		global $em,$system,$_user,$log;
	
		#################################################################################
		## Buscar o registro da fila
		#################################################################################
		$fila		= $em->getRepository('Entidades\ZgappFilaImportacao')->findOneBy(array('codigo' => $codFila));
		if (!$fila)	throw new \Exception(sprintf('Fila não encontrada "%s" !!!', $codFila));
		$resumo		= $em->getRepository('Entidades\ZgappFilaImportacaoResumo')->findOneBy(array('codFila' => $codFila));

		try {
			
			if ($resumo) $em->remove($resumo);
			$em->remove($fila);
			$em->flush();
			$em->clear();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Alterar a linha atual do registro
	 * @param number $codFila
	 */
	public static function alteraLinhaAtual($codFila,$linha) {
		global $em,$system,$_user,$log;
	
		#################################################################################
		## Buscar o registro da fila
		#################################################################################
		$fila		= $em->getRepository('Entidades\ZgappFilaImportacao')->findOneBy(array('codigo' => $codFila));
		if (!$fila)	throw new \Exception(sprintf('Fila não encontrada "%s" !!!', $codFila));
	
	
		#################################################################################
		## Alterar a linha
		#################################################################################
		$fila->setLinhaAtual($linha);
	
		try {
			$em->persist($fila);
			$em->flush();
			$em->clear();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Alterar o status atual do registro
	 * @param number $codFila
	 */
	public static function alteraStatus($codFila,$codStatus) {
		global $em,$system,$_user,$log;
	
		#################################################################################
		## Buscar o registro da fila
		#################################################################################
		$fila		= $em->getRepository('Entidades\ZgappFilaImportacao')->findOneBy(array('codigo' => $codFila));
		if (!$fila)	throw new \Exception(sprintf('Fila não encontrada "%s" !!!', $codFila));
	
	
		#################################################################################
		## Buscar o objeto do status
		#################################################################################
		$oStatus		= $em->getRepository('Entidades\ZgappImportacaoStatusTipo')->findOneBy(array('codigo' => $codStatus));
		$fila->setCodStatus($oStatus);
		
		try {
			$em->persist($fila);
			$em->flush();
			$em->clear();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Salvar o resumo PDF 
	 * @param number $codFila
	 */
	public static function salvaResumo($codFila,$conteudo) {
		global $em,$system,$_user,$log;
	
		#################################################################################
		## Buscar o registro da fila
		#################################################################################
		$fila		= $em->getRepository('\Entidades\ZgappFilaImportacao')->findOneBy(array('codigo' => $codFila));
		if (!$fila)	throw new \Exception(sprintf('Fila não encontrada "%s" !!!', $codFila));
	
		#################################################################################
		## Verifica se já existe um resumo
		#################################################################################
		$resumo		= $em->getRepository('\Entidades\ZgappFilaImportacaoResumo')->findOneBy(array('codFila' => $codFila));
		
		if (!$resumo)	$resumo = new \Entidades\ZgappFilaImportacaoResumo();
		
		#################################################################################
		## Alterar a linha
		#################################################################################
		$resumo->setCodFila($fila);
		$resumo->setResumo($conteudo);
	
		try {
			$em->persist($resumo);
			$em->flush();
			$em->detach($resumo);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	
	
	/**
	 * Calcula o número de linhas do Arquivo
	 */
	public static function calculaNumLinhas ($arquivo) {
		//return (sizeof($this->registros));
		return (intval(exec('wc -l ' . $arquivo)));
	}
	

}
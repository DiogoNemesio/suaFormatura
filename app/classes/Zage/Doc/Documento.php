<?php

namespace Zage\Doc;


/**
 * Documento
 *
 * @package Documento
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Documento extends \Entidades\ZgdocDocumento {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
	 * Criar um novo documento
	 * @param int $Tipo
	 * @return int
	 */
	public static function cria ($tipo) {
		global $em,$log,$system;
		
		$doc		= new \Entidades\ZgdocDocumento();
		$docTipo	= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array( 'codigo' => $tipo));
		$status		= $em->getRepository('Entidades\ZgdocDocumentoStatus')->findOneBy(array( 'codigo' => 'A'));
		
		if (!$docTipo) {
			return('Tipo de Documento não encontrado ');
		}
		
		if (!$status) {
			return('Status do documento inválido');
		}
		
		try {
			$doc->setCodTipo($docTipo);
			$doc->setCodStatus($status);
			$doc->setIndFisico(0);
			$doc->setIndIndexado(0);
			$doc->setIndRevisado(0);
			$doc->setIndVirtual(0);
			
			/** Cria o objeto da data atual **/
			$dateTime	= new \DateTime("now");
				
			$doc->setDataCadastro($dateTime);
			
			$em->persist($doc);
			$em->flush();
			$em->detach($doc);
				
			return ($doc->getCodigo());
			
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	
	
	/**
	 * Associa um arquivo a um documento
	 * @param int $codDocumento
	 * @param string $arquivo
	 */
	public static function associaArquivo ($codDocumento,$tempFile,$nomeArquivo,$type) {
		global $em,$log;
		
		/** Verifica se o documento existe **/
		$doc	= $em->getRepository('Entidades\ZgdocDocumento')->findOneBy(array( 'codigo' => $codDocumento));
		
		if (!$doc) {
			return ($tr->trans("Documento %s não existe",array("%s" => $codDocumento)));
		}
		
		
		/** Verifica se o arquivo existe e pode ser lido **/
		if (file_exists($tempFile) && is_readable($tempFile)) {
			
			/** Resgata as informações do arquivo **/
			//$nomeArquivo	= basename($arquivo);
			$tamArq			= filesize($tempFile);
			$ext 			= pathinfo($nomeArquivo, PATHINFO_EXTENSION);
			
			
			
		}else{
			return ($tr->trans("Arquivo %s não existe ou não pode ser lido",array("%s" => $tempFile)));
		}
		
		try {

			/** Define os valores dos campos **/
			$doc->setIndVirtual(1);

			$em->persist($doc);
			
			/** Cria as instâncias das informações do arquivo **/
			$oInfo		= new \Entidades\ZgdocArquivoInfo();
			$oFile		= new \Entidades\ZgdocArquivo();
			$em->persist($oInfo);
			$em->persist($oFile);

			/** Cria o objeto da data atual **/
			$dateTime	= new \DateTime("now");
			
			/** Cria o objeto do tipo do Arquivo **/
			$tipoArq	= $em->getRepository('Entidades\ZgdocArquivoTipo')->findOneBy(array( 'extensao' => strtolower($ext)));
			
			if ($tipoArq)	{
				$oInfo->setCodTipoArquivo($tipoArq);
			}
				
			/** Define os valores dos campos **/
			$oInfo->setNome($nomeArquivo);
			$oInfo->setCodDocumento($doc);
			$oInfo->setDataCadastro($dateTime);
			$oInfo->setTamanho($tamArq);
			$oInfo->setMimetype($type);
			
			$data 	= fread(fopen($tempFile, 'r'), $tamArq);
			
			if ($type == 'application/pdf') {
				$num_pag = preg_match_all("/\/Page\W/", $data,$dummy);
				if ($num_pag > 0) {
					$oInfo->setPaginas($num_pag);
				}else{
					$oInfo->setPaginas(1);
				}
			}else{
				$oInfo->setPaginas(1);
			}

			
			//$log->debug("Codigo Gerado: ".$oInfo->getCodigo());
			
			/** Cria a instância do arquivo **/
			$oFile->setCodArquivoInfo($oInfo);
			$oFile->setArquivo($data);
				
			
			$em->flush();
			$em->detach($doc);
			$em->detach($oInfo);
			$em->detach($oFile);

		
			return ($oFile->getCodigo());
				
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	
	
	/**
	 * Lista Documentos
	 */
	public static function listaNaoIndexados ($codTipoDoc) {
		global $em,$system;

		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('a')
			->from('\Entidades\ZgdocDocumento','d')
			->leftJoin('\Entidades\ZgdocArquivoInfo', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = a.codDocumento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codTipo'		, ':codTipoDoc'),
					$qb->expr()->eq('d.indIndexado'	, ':indIndexado')
			))
			->orderBy('d.dataCadastro', 'ASC')
			->setParameter('codTipoDoc', $codTipoDoc)
			->setParameter('indIndexado', '0');
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	
	/**
	 * Busca documentos baseado nos índices
	 */
	public static function busca ($codTipoDoc,array $aIndiceValor) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('d.codigo,count(d.codigo) as num')
			->from('\Entidades\ZgdocDocumento','d')
			->leftJoin('\Entidades\ZgdocIndiceValor'	,'iv'	, \Doctrine\ORM\Query\Expr\Join::WITH, 'd.codigo = iv.codDocumento')
			->where($qb->expr()->andX(
					$qb->expr()->eq('d.codTipo'	, ':codTipoDoc')
			))
			->groupby('d.codigo')
			->orderBy('d.dataCadastro', 'ASC')
			->setParameter('codTipoDoc', $codTipoDoc);
			
			//$log->debug(serialize($aIndiceValor));
			//$log->debug('CodTipoDoc: '.$codTipoDoc);
			
			if (sizeof($aIndiceValor) > 0) {
				$num	= sizeof($aIndiceValor);
				$qb->having('num = '.$num);
				$sql	= "";
				$i		= 1;
				foreach ($aIndiceValor as $codIndice => $dados) {
					$sql .= ' ( iv.codIndice = :indice_'.$codIndice.' AND ';
					
					//$log->debug('Indice: '.$codIndice);
					if ($dados["COMP"] == "EQUAL") {
						$sql .= ' iv.valor = :valor_'.$codIndice.' ) ';
						$qb->setParameter('valor_'.$codIndice, $dados["VALOR"]);
					}elseif ($dados["COMP"] == "LIKE") {
						$sql .= ' UPPER(iv.valor) LIKE :valor_'.$codIndice.' ) ';
						//$qb->andWhere($qb->expr()->like($qb->expr()->upper('iv.valor'), ':valor_'.$codIndice));
						$qb->setParameter('valor_'.$codIndice, '%'.strtoupper($dados["VALOR"]).'%');
					}else{
						$sql .= ' iv.valor = :valor_'.$codIndice.' ) ';
						$qb->setParameter('valor_'.$codIndice, $dados["VALOR"]);
					}
					
					if ($i < $num) {
						$sql .= ' OR ';
					}
					$qb->setParameter('indice_'.$codIndice, $codIndice);
					$i++;
				}
				
				$qb->andWhere($sql);
				
				$log->debug("SQL: $sql");
			}
			
			
			/*
			   SELECT 	D.CODIGO,COUNT(*) AS NUM
			   FROM 	`ZGDOC_DOCUMENTO` AS D LEFT JOIN `ZGDOC_INDICE_VALOR` IV ON (D.CODIGO = IV.COD_DOCUMENTO)
			   WHERE 	D.COD_TIPO = 3 AND 
			   	(
    				(IV.COD_INDICE = '4' AND IV.VALOR = 'Saida') OR 
    				(IV.COD_INDICE = '6' AND IV.VALOR = '0') OR 
    				(IV.COD_INDICE = '7' AND IV.VALOR = '100,00')
    			)
				GROUP BY D.CODIGO
				HAVING NUM = 3
			 */
			
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	
	
}


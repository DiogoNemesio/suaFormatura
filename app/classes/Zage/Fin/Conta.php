<?php

namespace Zage\Fin;


/**
 * Conta
 *
 * @package Conta
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Conta extends \Entidades\ZgfinConta {

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
	 * Lista Contas
	 */
	public static function listaTodas () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('ca')
			->from('\Entidades\ZgfinConta','ca')
			->leftJoin('ca.codOrganizacao', 'c')
			->where($qb->expr()->andX(
				$qb->expr()->eq('ca.codOrganizacao'	, ':codOrganizacao')
			))
			->orderBy('ca.nome', 'ASC')
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
    
    /**
     * Resgata o saldo da conta
     */
    public static function getSaldo($codConta) {
    	global $em,$system,$log;
    
    	$qb 	= $em->createQueryBuilder();
    	
    	try {
    		$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
    		$rsm->addScalarResult('saldo', 'saldo');
	    	$query	= $em->createNativeQuery("
    			SELECT SUM(SALDO) AS saldo
    			FROM 	( 
    				SELECT	SALDO_INICIAL AS SALDO
    				FROM 	ZGFIN_CONTA WHERE CODIGO = :codConta
    			
    				UNION ALL 
    			
    				SELECT	SUM(M.VALOR) AS SALDO 
    				FROM 	ZGFIN_MOV_BANCARIA M
    				WHERE 	COD_CONTA = :codConta
    				AND		COD_TIPO_OPERACAO = 'C'
	    			
    				UNION ALL 
    				
	    			SELECT	SUM(M.VALOR)*-1 AS SALDO 
    				FROM 	ZGFIN_MOV_BANCARIA M
    				WHERE 	COD_CONTA = :codConta
    				AND		COD_TIPO_OPERACAO = 'D'
	    			
	    		) AS T
    			
	    	",$rsm);
    	
    		$query->setParameter('codConta', $codConta);
			return($query->getSingleResult());
			    		
    		
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }

    /**
     * Resgata o saldo da conta
     */
    public static function getSaldoDia($codConta,$dia) {
    	global $em,$system,$log;
    
    	
    	#################################################################################
    	## Verifica se a conta existe
    	#################################################################################
    	$conta	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo'=> $codConta));
    	
    	if (!$conta) return 0;

    	#################################################################################
    	## Formata a data
    	#################################################################################
    	$data	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dia);
    	 
    	#################################################################################
    	## Criar os objetos do QueryBuilder
    	#################################################################################
    	$qb1 	= $em->createQueryBuilder();
    	$qb2 	= $em->createQueryBuilder();
    	
    	try {
    		$qb1->select('SUM(m.valor) as VALOR')
    		->from('\Entidades\ZgfinMovBancaria','m')
    		->where($qb1->expr()->andX(
				$qb1->expr()->eq('m.codConta'			, ':codConta'),
    			$qb1->expr()->eq('m.codTipoOperacao'	, ':tipoOper'),
    			$qb1->expr()->lte('m.dataMovimentacao'	, ':data')
			))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('tipoOper'	,'C')
    		->setParameter('data'		,$data);

    		$query 		= $qb1->getQuery();
    		$credito	= $query->getSingleResult();

    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	 
    	try {
    		$qb2->select('SUM(m.valor) as VALOR')
    		->from('\Entidades\ZgfinMovBancaria','m')
    		->where($qb2->expr()->andX(
    				$qb2->expr()->eq('m.codConta'			, ':codConta'),
    				$qb2->expr()->eq('m.codTipoOperacao'	, ':tipoOper'),
    				$qb2->expr()->lte('m.dataMovimentacao'	, ':data')
    		))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('tipoOper'	,'D')
    		->setParameter('data'		,$data);
    	
    		$query 		= $qb2->getQuery();
    		$debito		= $query->getSingleResult();
    	
    	
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	 
    	
    	$saldo		= floatval($conta->getSaldoInicial()) + floatval($credito["VALOR"]) - floatval($debito["VALOR"]);
    	
    	return ($saldo);
    }    
    
    
    /**
     * Resgata o saldo projetado 
     */
    public static function getSaldoProjetadoDia($codConta,$dataBase,$dataProjecao) {
    	global $em,$system,$log;
    
    	#################################################################################
    	## Verifica se a conta existe
    	#################################################################################
    	$conta	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo'=> $codConta));

    	if (!$conta) return 0;
    	 
    	
    	#################################################################################
    	## Resgata o saldo da data Base
    	#################################################################################
    	$saldoDataBase	= self::getSaldoDia($codConta, $dataBase);
    	
    	#################################################################################
    	## Formata as datas
    	#################################################################################
    	$dtProj	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataProjecao);
    	$dtBase	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataBase);
    	 
    	#################################################################################
    	## Criar os objetos do QueryBuilder
    	#################################################################################
    	$qb1 	= $em->createQueryBuilder();
    	$qb2 	= $em->createQueryBuilder();
    	$qb3 	= $em->createQueryBuilder();
    	$qb4 	= $em->createQueryBuilder();
    	 
    	try {
    		$qb1->select('SUM(cp.valor + cp.valorJuros + cp.valorMora - cp.valorDesconto - cp.valorCancelado) as VALOR')
    		->from('\Entidades\ZgfinContaPagar','cp')
    		->where($qb1->expr()->andX(
    			$qb1->expr()->eq('cp.codConta'			, ':codConta'),
    			$qb1->expr()->gt('cp.dataVencimento'	, ':dataBase'),
    			$qb1->expr()->lte('cp.dataVencimento'	, ':dataProj'),
   				$qb1->expr()->In('cp.codStatus'			, array("A","L","P"))
    		))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('dataBase'	,$dtBase)
    		->setParameter('dataProj'	,$dtProj);
    
    		$query 		= $qb1->getQuery();
    		$debito		= $query->getSingleResult();
    
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    	try {
    		$qb2->select('SUM(cr.valor + cr.valorJuros + cr.valorMora - cr.valorDesconto - cr.valorCancelado) as VALOR')
    		->from('\Entidades\ZgfinContaReceber','cr')
    		->where($qb2->expr()->andX(
    			$qb2->expr()->eq('cr.codConta'			, ':codConta'),
    			$qb2->expr()->gt('cr.dataVencimento'	, ':dataBase'),
    			$qb2->expr()->lte('cr.dataVencimento'	, ':dataProj'),
   				$qb2->expr()->in('cr.codStatus'			, array("A","L","P"))
    		))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('dataBase'	,$dtBase)
    		->setParameter('dataProj'	,$dtProj);
    
    		$query 		= $qb2->getQuery();
    		$credito	= $query->getSingleResult();
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    
    	try {
    		$qb3->select('SUM(t.valor - t.valorCancelado) as VALOR')
    		->from('\Entidades\ZgfinTransferencia','t')
    		->where($qb3->expr()->andX(
    			$qb3->expr()->eq('t.codContaOrigem'		, ':codConta'),
    			$qb3->expr()->gt('t.dataTransferencia'	, ':dataBase'),
    			$qb3->expr()->lte('t.dataTransferencia'	, ':dataProj'),
    			$qb3->expr()->in('t.codStatus'			, array("P","R","PA"))
    		))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('dataBase'	,$dtBase)
    		->setParameter('dataProj'	,$dtProj);
    	
    		$query 		= $qb3->getQuery();
    		$transfDeb	= $query->getSingleResult();
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	 
    	try {
    		$qb4->select('SUM(t.valor - t.valorCancelado) as VALOR')
    		->from('\Entidades\ZgfinTransferencia','t')
    		->where($qb3->expr()->andX(
    			$qb4->expr()->eq('t.codContaDestino'	, ':codConta'),
    			$qb4->expr()->gt('t.dataTransferencia'	, ':dataBase'),
    			$qb4->expr()->lte('t.dataTransferencia'	, ':dataProj'),
    			$qb4->expr()->in('t.codStatus'			, array("P","R","PA"))
    		))
    		->setParameter('codConta'	,$codConta)
    		->setParameter('dataBase'	,$dtBase)
    		->setParameter('dataProj'	,$dtProj);
    		 
    		$query 		= $qb4->getQuery();
    		//$log->debug("SQL:".$query->getSQL());
    		$transfCre	= $query->getSingleResult();
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    	 
    	/*$log->debug("DataProj: ".$dataProjecao);
    	$log->debug("DataBase: ".$dataBase);
    	$log->debug("Saldo: ".floatval($saldoDataBase));
    	$log->debug("Creditos: ".floatval($credito["VALOR"]));
    	$log->debug("Débitos: ".floatval($debito["VALOR"]));
    	$log->debug("TransfCre: ".floatval($transfCre["VALOR"]));
    	$log->debug("TransfDeb: ".floatval($transfDeb["VALOR"]));
    	*/
    	$saldo		= floatval($saldoDataBase) + floatval($credito["VALOR"]) + floatval($transfCre["VALOR"]) - floatval($debito["VALOR"]) - floatval($transfDeb["VALOR"]);
    	 
    	return ($saldo);
    
    }
    
    
    /**
     * Resgata o resultado projetado de uma conta em um determinado período
     */
    public static function getResultadoProjetado($codConta,$dataIni,$dataFim) {
    	global $em,$system,$log;
    
    	#################################################################################
    	## Formata as datas
    	#################################################################################
    	$dtIni	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataIni);
    	$dtFim	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataFim);
    	 
    	
    	$qb 	= $em->createQueryBuilder();
    	 
    	try {
    		$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
    		$rsm->addScalarResult('VALOR', 'VALOR');
    		$rsm->addScalarResult('TIPO', 'TIPO');
    		$query	= $em->createNativeQuery("
    			SELECT T.TIPO AS TIPO, SUM(VALOR) AS VALOR
    			FROM 	(
    				SELECT	MAX('D') AS TIPO, SUM(P.VALOR + P.VALOR_JUROS + P.VALOR_MORA - P.VALOR_DESCONTO - P.VALOR_CANCELADO) AS VALOR
    				FROM 	ZGFIN_CONTA_PAGAR	P
    				WHERE 	P.COD_CONTA 		= :codConta
    				AND		P.DATA_VENCIMENTO	>= :DATAINI
    				AND		P.DATA_VENCIMENTO	<= :DATAFIM
    				AND		P.COD_STATUS		IN ('A','P','L')
    
    				UNION ALL
    
    				SELECT	MAX('C') AS TIPO, SUM(R.VALOR + R.VALOR_JUROS + R.VALOR_MORA - R.VALOR_DESCONTO - R.VALOR_CANCELADO) AS VALOR
    				FROM 	ZGFIN_CONTA_RECEBER	R
    				WHERE 	R.COD_CONTA 		= :codConta
    				AND		R.DATA_VENCIMENTO	>= :DATAINI
    				AND		R.DATA_VENCIMENTO	<= :DATAFIM
    				AND		R.COD_STATUS		IN ('A','P','L')
    				    				    
    				UNION ALL
    		
    				SELECT	MAX('D') AS TIPO, SUM(T.VALOR - T.VALOR_CANCELADO) AS VALOR
    				FROM 	ZGFIN_TRANSFERENCIA	T
    				WHERE 	T.COD_CONTA_ORIGEM		= :codConta
    				AND		T.DATA_TRANSFERENCIA	>= :DATAINI
    				AND		T.DATA_TRANSFERENCIA	<= :DATAFIM
    				AND		T.COD_STATUS		IN ('P','R','PA')
    				
    				UNION ALL
    		
    				SELECT	MAX('C') AS TIPO, SUM(T.VALOR - T.VALOR_CANCELADO) AS VALOR
    				FROM 	ZGFIN_TRANSFERENCIA	T
    				WHERE 	T.COD_CONTA_DESTINO		= :codConta
    				AND		T.DATA_TRANSFERENCIA	>= :DATAINI
    				AND		T.DATA_TRANSFERENCIA	<= :DATAFIM
    				AND		T.COD_STATUS		IN ('P','R','PA')
    		) AS T
    		WHERE	T.TIPO 	IS NOT NULL
    		GROUP	BY T.TIPO
    				
    
	    	",$rsm);
    		 
    		$query->setParameter('codConta', 	$codConta);
    		$query->setParameter('DATAINI', 	$dtIni);
    		$query->setParameter('DATAFIM', 	$dtFim);
    		return($query->getResult());
    		 
    
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
    
    /**
     * Busca o código da conta através da agencia e conta corrente
     */
    public static function busca ($codOrganizacao,$agencia,$contaCorrente) {
    	global $em,$system;
    
    	$qb 	= $em->createQueryBuilder();
    		
    	try {
    		$qb->select('c')
    		->from('\Entidades\ZgfinConta','c')
			->leftJoin('\Entidades\ZgfinAgencia', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'c.codAgencia = a.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('c.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->eq('a.agencia'			, ':agencia'),
				$qb->expr()->eq('c.ccorrente'		, ':contaCorrente')
			))
    		->setParameter('codOrganizacao'	, $codOrganizacao)
    		->setParameter('agencia'		, $agencia)
    		->setParameter('contaCorrente'	, $contaCorrente);
    		$query 		= $qb->getQuery();
    		return($query->getOneOrNullResult());
    	} catch (\Exception $e) {
    		\Zage\App\Erro::halt($e->getMessage());
    	}
    }
    
    
}

<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * Gerenciar as convites e convidados
 * 
 * @package: Rifa
 * @Author: Diogo Nemésio
 * @version: 1.0.1
 * 
 */

class Convite {

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
	 * Lista vendas por formando
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaVendaConviteFormando() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('v')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraEventoConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codigo 	= i.codConviteConf')
			->leftJoin('\Entidades\ZgadmOrganizacao'			,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'				, ':codOrganizacao')
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
				
			->groupBy('v.codFormando');
			//->orderBy('r.codigo', 'ASC');
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	* Lista Configuracoes validas
	*
	* @param integer $codOrganizacao
	* @return array
	*/
	public static function listaConviteAptoVenda() {
		global $em,$system, $log;
	
		$qb 	= $em->createQueryBuilder();
		$hoje 	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], date($system->config["data"]["dateFormat"]." 00:00:00"));
		
		try {
			$qb->select('c')
			->from('\Entidades\ZgfmtConviteExtraEventoConf','c')
				->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
				->where($qb->expr()->andx(
						$qb->expr()->eq('o.codigo'					, ':codOrganizacao'),
						$qb->expr()->lte('c.dataInicioPresencial'	, ':now'),
						$qb->expr()->gte('c.dataFimPresencial'		, ':now')
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', $hoje)
				
			->orderBy('c.codigo', 'ASC');
				
			$query 		= $qb->getQuery();
			
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Quantidade convite disponivel por configuração de evento
	 *
	 * @param integer $codPessoa (FORMANDO)
	 * @param integer $codEvento
	 * @return array
	 */
	public static function qtdeConviteDispFormando($codPessoa, $codEvento) {
		global $em,$system;
	
		
		#################################################################################
		## O Cálculo de quantidade disponível será :a quantidade máxima de convites por formando do evento
		## em questão, menos as vendas e as transferências de débito
		## mais as transferências de crédito.
		#################################################################################
		
		#################################################################################
		## Resgatar as configurações do evento, onde está configurado a quantidade máxima
		## por formando, caso não encontre retornar 0
		#################################################################################
		$oEveConf	= $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codEvento' => $codEvento));
		if (!$oEveConf)	return 0;
		$qtdeMax	= $oEveConf->getQtdeMaxAluno();
		
		
		#################################################################################
		## Calcular as vendas já efetuadas
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
			$qb1->select('sum(i.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('v.codOrganizacao'		, ':codOrganizacao'),
				$qb1->expr()->eq('v.codFormando'		, ':codFormando'),
				$qb1->expr()->eq('i.codEvento'			, ':codEvento')
			))
	
			->setParameter('codOrganizacao'	,$system->getCodOrganizacao())
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento);
				
			$query 			= $qb1->getQuery();
			$qtdeVendida	= $query->getSingleScalarResult(); 
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular as transferências de crédito
		#################################################################################
		$qb2 	= $em->createQueryBuilder();
		
		try {
			$qb2->select('sum(t.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraTransf','t')
			->where($qb2->expr()->andx(
				$qb2->expr()->eq('t.codEvento'			, ':codEvento'),
				$qb2->expr()->eq('t.codFormandoDestino'	, ':codFormando')
			))
		
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento);
		
			$query 			= $qb2->getQuery();
			$qtdeRecebida	= $query->getSingleScalarResult();
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular as transferências de Débito
		#################################################################################
		$qb3 	= $em->createQueryBuilder();
		
		try {
			$qb3->select('sum(t.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraTransf','t')
			->where($qb3->expr()->andx(
				$qb3->expr()->eq('t.codEvento'			, ':codEvento'),
				$qb3->expr()->eq('t.codFormandoOrigem'	, ':codFormando')
			))
		
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento);
		
			$query 			= $qb3->getQuery();
			$qtdeCedida	= $query->getSingleScalarResult();
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		$qtde		= $qtdeMax - $qtdeVendida - $qtdeCedida + $qtdeRecebida;
		
		return ($qtde);
		
	}
}

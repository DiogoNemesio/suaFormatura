<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;


/**
 * Gerenciar Desistências
 * 
 * @package: ContaReceberFormatura
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Desistencia {

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		$log->debug(__CLASS__.": nova Instância");
	}

	/**
	 * Listar mensalidades em aberto e pendente
	 */
	public static function listaMensalidadeACancelar($codOrganizacao,$cpf) {
		global $em,$system,$log;
	
		#################################################################################
		## Resgatar a categoria de mensalidades
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$aCat						= array($codCatMensalidade);
		
		#################################################################################
		## Configurar os status das contas que serão canceladas
		#################################################################################
		$aStatus					= array("A","P");
		
		$qb 	= $em->createQueryBuilder();
		$qbCat 	= $em->createQueryBuilder();
		
		try {
			
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->in('cr.codStatus'		, ':aStatus'),
				$qb->expr()->eq('p.cgc'				, ':cpf')
			))
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('cpf'			, $cpf)
			->setParameter('aStatus'		, $aStatus);
				
			$qb->andWhere(
				$qb->expr()->exists(
					$qbCat->select('cpr2')
					->from('\Entidades\ZgfinContaReceberRateio','cpr2')
					->where($qbCat->expr()->andX(
						$qbCat->expr()->eq('cpr2.codContaRec'		, 'cr.codigo'),
						$qbCat->expr()->in('cpr2.codCategoria'		, $aCat)
					))->getDQL()
				)
			);
		
			$query 		= $qb->getQuery();
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		
		
		
		
		
		
		
		
		
		
		
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinContaReceberRateio', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codigo = r.codContaRec')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('p.cgc'				, ':cpf'),
					$qb->expr()->in('r.codCategoria'	, ':codCategoria')
			))
				
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codCategoria', $aCat)
			->setParameter('cpf', $cpf)
			->setParameter('codOrganizacao', $system->getCodOrganizacao());
				
			$query 		= $qb->getQuery();
				
			return($query->getResult());
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}
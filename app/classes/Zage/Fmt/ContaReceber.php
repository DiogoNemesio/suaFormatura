<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;


/**
 * Gerenciar as contas a receber da formatura
 * 
 * @package: ContaReceberFormatura
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class ContaReceber extends \Entidades\ZgfinContaReceber {

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
	 *
	 * Listar contas de mensalidade de um formando
	 */
	public static function listaMensalidadeFormando ($cpf) {
		global $em,$system,$log;
	
		
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$aCat						= array($codCatMensalidade);
		
		$qb 	= $em->createQueryBuilder();
	
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
<?php

namespace Zage\Fmt;

/**
 * Formando
 * 
 * @package: Formando
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Formando {

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
	 * Lista pessoas ativas do tipo formando retiranto o usuário passado com parâmetro
	 *	
	 * @param int $codUsuário (Úsuário do sistema)
	 * @return array
	 */
	public static function ListaPessoaFormandoRetiraSelec($codOrganizacao,$codUsuario){
		global $em,$system, $log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('p')
			->from('\Entidades\ZgfinPessoa','p')
			->leftJoin('\Entidades\ZgfinPessoaOrganizacao'	,'po',	\Doctrine\ORM\Query\Expr\Join::WITH, 'po.codPessoa 	= p.codigo')
			->leftJoin('\Entidades\ZgsegUsuario'			,'u',	\Doctrine\ORM\Query\Expr\Join::WITH, 'u.cpf = p.cgc')
			->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= po.codOrganizacao')
			->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'					, ':codOrganizacao'),
					$qb->expr()->eq('po.indFormando'			, ':indFormando'),
					$qb->expr()->eq('po.indAtivo'				, ':indAtivo'),
					$qb->expr()->neq('u.codigo'					, ':codUsuario')
				)
			)
	
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('indFormando'	, '1')
			->setParameter('codUsuario'		, $codUsuario)
			->setParameter('indAtivo'		, '1');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

	/**
	 * Lista os pagamentos atrasado de um formando em uma determinada formatura
	 * @param int $codOrganizacao
	 * @param int $cpf
	 */
	public static function listaPagamentosAtrasados ($codOrganizacao,$cpf) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb->expr()->eq('p.cgc'				, ':cpf'),
				$qb->expr()->in('cr.codStatus'		, ':codStatus'),
				$qb->expr()->lt('cr.dataVencimento'	, ':vencimento')
			))
			->orderBy('cr.dataVencimento','DESC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('codStatus'		, array("A","P"))
			->setParameter('vencimento'		, \DateTime::createFromFormat( $system->config["data"]["dateFormat"], date($system->config["data"]["dateFormat"]) ), \Doctrine\DBAL\Types\Type::DATE)
			->setParameter('cpf'			, $cpf);
	
			if (isset($aCategoria) && !empty($aCategoria)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb2->select('crr2')
						->from('\Entidades\ZgfinContaReceberRateio','crr2')
						->where($qb2->expr()->andX(
								$qb2->expr()->eq('crr2.codContaPag'		, 'cr.codigo'),
								$qb2->expr()->in('crr2.codCategoria'		, $aCategoria)
						)
						)->getDQL()
					)
				);
			}
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Lista os pagamentos em aberto de um formando em uma determinada formatura
	 * @param int $codOrganizacao
	 * @param int $cpf
	 */
	public static function listaPagamentosAVencer ($codOrganizacao,$cpf) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('p.cgc'				, ':cpf'),
					$qb->expr()->in('cr.codStatus'		, ':codStatus'),
					$qb->expr()->gte('cr.dataVencimento'	, ':vencimento')
			))
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('codStatus'		, array("A","P"))
			->setParameter('vencimento'		, \DateTime::createFromFormat( $system->config["data"]["dateFormat"], date($system->config["data"]["dateFormat"]) ), \Doctrine\DBAL\Types\Type::DATE)
			->setParameter('cpf'			, $cpf);
	
			if (isset($aCategoria) && !empty($aCategoria)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb2->select('crr2')
						->from('\Entidades\ZgfinContaReceberRateio','crr2')
						->where($qb2->expr()->andX(
								$qb2->expr()->eq('crr2.codContaPag'		, 'cr.codigo'),
								$qb2->expr()->in('crr2.codCategoria'		, $aCategoria)
						)
						)->getDQL()
					)
				);
			}
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Lista os pagamentos Realizados de um formando em uma determinada formatura
	 * @param int $codOrganizacao
	 * @param int $cpf
	 */
	public static function listaPagamentosRealizados ($codOrganizacao,$cpf) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('cr')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
					$qb->expr()->eq('p.cgc'				, ':cpf'),
					$qb->expr()->in('cr.codStatus'		, ':codStatus')
			))
			->orderBy('cr.dataVencimento','ASC')
			->addOrderBy('cr.descricao,cr.parcela,cr.dataEmissao','ASC')
			->setParameter('codOrganizacao'	, $codOrganizacao)
			->setParameter('codStatus'		, array("L"))
			->setParameter('cpf'			, $cpf);
	
			if (isset($aCategoria) && !empty($aCategoria)) {
				$qb2 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->exists(
						$qb2->select('crr2')
						->from('\Entidades\ZgfinContaReceberRateio','crr2')
						->where($qb2->expr()->andX(
								$qb2->expr()->eq('crr2.codContaPag'		, 'cr.codigo'),
								$qb2->expr()->in('crr2.codCategoria'		, $aCategoria)
						)
						)->getDQL()
					)
				);
			}
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}
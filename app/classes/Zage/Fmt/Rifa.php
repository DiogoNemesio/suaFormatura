<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * Gerenciar as rifas
 * 
 * @package: Rifa
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Rifa {

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
	 * Lista todas as rifas aptas para o sorteio
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaRifaAptaSorteio() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		
		$data = new \DateTime("+10 day");
		$data = $data->format($system->config["data"]["datetimeSimplesFormat"]);
		
		//$log->debug($data);
		
		try {
			$qb->select('r')
			->from('\Entidades\ZgfmtRifa','r')
			->leftJoin('\Entidades\ZgadmOrganizacao'	,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= r.codOrganizacao')			
			->where($qb->expr()->orx(
					$qb->expr()->andx(
							$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
							$qb->expr()->eq('r.indRifaEletronica'	, ':indRifaEletronica'),
							$qb->expr()->lte('r.dataSorteio'		, ':now'),
							$qb->expr()->isNull('r.numeroVencedor')
							),
							
					 $qb->expr()->andx(
								$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
								$qb->expr()->eq('r.indRifaEletronica'	, ':indRifaEletronica'),								
								$qb->expr()->gte('r.dataSorteio'		, ':limite'),
					 			$qb->expr()->isNotNull('r.numeroVencedor')
								)
					)
			)

			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('indRifaEletronica', 1)
			->setParameter('now', new \DateTime("now"))
			->setParameter('limite', new \DateTime("-5 day"))
			
			->orderBy('r.dataSorteio', 'DESC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Lista de rifas aptas para venda
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaRifaAptaVenda() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('r')
			->from('\Entidades\ZgfmtRifa','r')
			->leftJoin('\Entidades\ZgadmOrganizacao'	,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= r.codOrganizacao')
			->where($qb->expr()->andx(
							$qb->expr()->eq('o.codigo'				, ':codOrganizacao'),
							$qb->expr()->eq('r.indRifaEletronica'	, ':indRifaEletronica'),
							$qb->expr()->eq('r.indRifaGerada'		, ':indRifaEletronica'),
							$qb->expr()->gte('r.dataSorteio'		, ':now')
					)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('indRifaEletronica', '1')
			
			->setParameter('now', new \DateTime("now"))
				
			->orderBy('r.dataSorteio', 'DESC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Lista todos os formandos ATIVOS e o número de rifas geradas
	 */
	public static function listaNumRifasPorFormando ($codOrganizacao,$codRifa) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		$qb->select('us.codigo,us.nome,r.qtdeObrigatorio,r.valorUnitario, '.$qb->expr()->count('rn.codigo'). " as num")
		->from('\Entidades\ZgsegUsuario','us')
		->leftJoin('\Entidades\ZgsegUsuarioOrganizacao',		'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'us.codigo 		= uo.codUsuario')
		->leftJoin('\Entidades\ZgsegPerfil',					'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codPerfil		= p.codigo')
		->leftJoin('\Entidades\ZgfmtRifaNumero',				'rn',	\Doctrine\ORM\Query\Expr\Join::WITH, 'rn.codFormando	= us.codigo')
		->leftJoin('\Entidades\ZgfmtRifa',						'r',	\Doctrine\ORM\Query\Expr\Join::WITH, 'r.codigo			= rn.codRifa')
		->where($qb->expr()->andX(
			$qb->expr()->eq('uo.codOrganizacao'		, ':codOrganizacao'),
			$qb->expr()->eq('p.codTipoUsuario'		, ':codTipoUsuario'),
			$qb->expr()->eq('uo.codStatus'			, ':codStatusAtivo'),
			$qb->expr()->eq('rn.codRifa'			, ':codRifa'))
		)
		
		->addGroupBy("us.codigo,us.nome")
		->orderBy('us.nome', 'ASC')
		->setParameter('codOrganizacao', $codOrganizacao)
		->setParameter('codStatusAtivo', 'A')
		->setParameter('codTipoUsuario', 'F')
		->setParameter('codRifa'	   , $codRifa);
		
		$query 		= $qb->getQuery();
		return($query->getResult());
	
	}
}
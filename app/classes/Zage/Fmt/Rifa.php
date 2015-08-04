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
	 * Lista todas as formaturas aptas para o sorteio
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
	 * Lista todos os formandos ATIVOS e o número de rifas geradas
	 */
	public static function listaUsuarioAtivo ($codRifa) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		$qb->select('rn')
		->from('\Entidades\ZgsegUsuario','us')
		->leftJoin('\Entidades\ZgsegUsuarioOrganizacao',		'uo',	\Doctrine\ORM\Query\Expr\Join::WITH, 'us.codigo 		= uo.codUsuario')
		->leftJoin('\Entidades\ZgsegPerfil',					'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'uo.codPerfil		= p.codigo')
		->leftJoin('\Entidades\ZgfmtRifaNumero',				'rn',	\Doctrine\ORM\Query\Expr\Join::WITH, 'rn.codFormando	= us.codigo')
		->where($qb->expr()->andX(
				$qb->expr()->eq('uo.codOrganizacao'		, ':codOrganizacao'),
				$qb->expr()->eq('p.codTipoUsuario'		, ':codTipoUsuario'),
				$qb->expr()->eq('uo.codStatus'			, ':codStatusAtivo'),
				$qb->expr()->eq('rn.codRifa'			, ':codRifa'))
		)
		
		
		//->groupBy('rn.codFormando')
		->orderBy('us.nome', 'ASC')
		->setParameter('codOrganizacao', $system->getCodOrganizacao())
		->setParameter('codStatusAtivo', A)
		->setParameter('codTipoUsuario', F)
		->setParameter('codRifa'	   , $codRifa);
		
		$query 		= $qb->getQuery();
		return($query->getResult());
	
	}
	
	
	
	/**
	 * Retorna o próximo número da Rifa
	 *
	 * @param integer $codRifa
	 * @return integer
	 */
	public static function numeroAtual ($codRifa) {
		global $em;

		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('max(n.numero)')
			->from('\Entidades\ZgfmtRifaNumero','n')
			->where($qb->expr()->andX(
				$qb->expr()->eq('n.codRifa'	, ':codRifa')
			))
			->setParameter('codRifa', $codRifa);
		
			$query 		= $qb->getQuery();
			$query->setLockMode(LockMode::PESSIMISTIC_WRITE);

			$numeroAtual = $query->getSingleScalarResult();
			
			if (!$numeroAtual)	$numeroAtual = 0;
			return($numeroAtual);
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	
	/**
	 * Retorna o próximo número da Rifa
	 *
	 * @param integer $codRifa
	 * @return integer
	 */
	public static function proximoNumero ($codRifa) {
		$numeroAtual  	= self::numeroAtual($codRifa);
		$proximo		= $numeroAtual + 1;
			
		return($proximo);
	}
	

}
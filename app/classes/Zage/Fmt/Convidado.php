<?php

namespace Zage\Fmt;

/**
 * Contar convidados
 * 
 * @package: Convidado
 * @Author: Jalon Vitor Cerqueira Silva
 * @version: 1.0.1
 * 
 */

class Convidado {

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
	 *	Retorna quantidade total de convidados retirando o grupo selecionado
	 * 
	 */
	public static function zgCountLista ($codGrupo) {
		global $em,$system;
		
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('count(c.codigo)')
			->from('\Entidades\ZgfmtListaConvidado'			,'c')
			->leftJoin('\Entidades\ZgfmtConvidadoGrupo'		,'g',	\Doctrine\ORM\Query\Expr\Join::WITH, 'g.codigo 	= c.codGrupo')
			->leftJoin('\Entidades\ZgsegUsuario'			,'u',	\Doctrine\ORM\Query\Expr\Join::WITH, 'u.codigo 	= c.codUsuario')
			->where($qb->expr()->andx(
					$qb->expr()->eq('u.codigo'			, ':codUsuario'),
					$qb->expr()->neq('g.codigo'			, ':codrupo')
				)
			)
			->setParameter('codUsuario' , $system->getCodUsuario())
			->setParameter('codrupo'	, $codGrupo);
			
			$query 		= $qb->getQuery();
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}

}
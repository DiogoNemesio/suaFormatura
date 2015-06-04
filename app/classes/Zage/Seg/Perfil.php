<?php

namespace Zage\Seg;


/**
 * Usuário
 *
 * @package Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Perfil extends \Entidades\ZgsegPerfil {

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
     * Lista as empresas que o usuário tem acesso
     */
    public static function listaPerfilOrganizacao ($codOrganizacao) {
    	global $em;
    	 
    	$qb 	= $em->createQueryBuilder();
    	
    	$qb->select('p')
    	->from('\Entidades\ZgsegPerfil','p')
    	->leftJoin('\Entidades\ZgsegPerfilOrganizacaoTipo',	'ot',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codigo 		= ot.codPerfil')
    	->where($qb->expr()->andX(
    		$qb->expr()->eq('ot.codOrganizacaoTipo'		, ':codOrganizacao'),
    		$qb->expr()->eq('p.indAtivo'				, '1')
    	))
    	->orderBy('p.nome', 'ASC')
    	->setParameter('codOrganizacao', $codOrganizacao);
    	 
    	$query 		= $qb->getQuery();
    	return($query->getResult());
    	
    }
}

<?php

namespace Zage\Wap;


/**
 * WhatsApp
 *
 * @package Local
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Numero extends \Entidades\ZgwapNumero {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		//parent::__construct();
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
	 * Lista os números
	 */
	public static function listaTodos () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		try {
			$qb->select('n')
			->from('\Entidades\ZgwapNumero','n')
			->orderBy('n.ddd', 'ASC')
			->addOrderBy('n.numero','ASC');
			 
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		 
	}
	
	/**
	 * Lista os números
	 */
	public static function listaNaoConsultados($servidor,$chip,$numRegistros) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('n')
			->from('\Entidades\ZgwapNumero','n')
			->leftJoin('\Entidades\ZgwapPrefixo', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'n.codPrefixo = p.codigo')
			->leftJoin('\Entidades\ZgwapFila', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'f.codPrefixo = p.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->isNull('n.indTemWa'),
				$qb->expr()->eq('f.codServidor'		, ':codServidor'),
				$qb->expr()->eq('f.codChip'			, ':codChip')
			))
			->setMaxResults($numRegistros)
			->setParameter('codServidor', $servidor)
			->setParameter('codChip', $chip)
			->orderBy('n.ddd', 'ASC')
			->addOrderBy('n.numero','ASC');

			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Lista os números não enviados
	 */
	public static function listaNaoEnviados($servidor = null,$chips,$numRegistros) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('n')
			->addSelect('substring(n.numero,5,4)/(substring(n.numero,6,1)+1)*(substring(n.numero,4,1)+1) as HIDDEN rand')
			->from('\Entidades\ZgwapNumero','n')
			->leftJoin('\Entidades\ZgwapPrefixo', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'n.codPrefixo = p.codigo')
			->leftJoin('\Entidades\ZgwapFila', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'f.codPrefixo = p.codigo')
			->where($qb->expr()->andX(
					$qb->expr()->eq('n.indTemWa'		, 1)
			))
			->andWhere($qb->expr()->orX(
					$qb->expr()->isNull('n.indEnviado'),
					$qb->expr()->eq('n.indEnviado','0')
			))
			->setMaxResults($numRegistros)
			
			->orderBy('rand', 'DESC');
			
			if ($servidor != null) {
				$qb->andWhere(
					$qb->expr()->eq('f.codServidor'		, ':codServidor')
				);
				$qb->setParameter('codServidor', $servidor);
			}

			if ($chips != null) {
				$qb->andWhere(
						$qb->expr()->in('f.codChip'			, ':codChip')
				);
				$qb->setParameter('codChip', $chips);
			}
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	
	/**
	 * Lista os prefixos na fila de um Servidor
	 */
	public static function listaPrefixosServidor($servidor,$chips = array()) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('f')
			->from('\Entidades\ZgwapFila','f');
			$qb->andWhere($qb->expr()->in('f.codServidor'		, ':codServidor'));
			$qb->setParameter('codServidor', $servidor);
			
			if (sizeof($chips) > 0) {
				$qb->andWhere($qb->expr()->in('f.codChip'			, ':codChip'));
				$qb->setParameter('codChip', $chips);
			}
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Lista os prefixos de um servidor 
	 */
	public static function listaPrefixos() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('distinct substr(n.numero,1,4) as prefixo')
			->from('\Entidades\ZgwapNumero','n');
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	
	/**
	 * Lista os números
	 */
	public static function lista($servidor = null,$chip =  null) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('n')
			->from('\Entidades\ZgwapNumero','n')
			->leftJoin('\Entidades\ZgwapFila', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'f.codPrefixo = n.codPrefixo')
			->orderBy('n.ddd', 'ASC')
			->addOrderBy('n.numero','ASC');
			
			if ($servidor) {
				$qb->andWhere($qb->expr()->eq('f.codServidor'		, ':codServidor'));
				$qb->setParameter('codServidor', $servidor);
			}
			
			if ($chip) {
				$qb->andWhere($qb->expr()->eq('f.codChip'			, ':codChip'));
				$qb->setParameter('codChip', $chip);
			}
				
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	
	/**
	 * Lista os números com WhatsApp
	 */
	public static function listaComWhatsApp () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('n.ddd,n.numero')
			->from('\Entidades\ZgwapNumero','n')
			->where($qb->expr()->andX(
					$qb->expr()->eq('n.indTemWa'	, '1')
			))
			->orderBy('n.ddd', 'ASC')
			->addOrderBy('n.numero','ASC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}

	/**
	 * Lista os números com WhatsApp
	 */
	public static function listaSemWhatsApp () {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		try {
			$qb->select('n')
			->from('\Entidades\ZgwapNumero','n')
			->where($qb->expr()->andX(
					$qb->expr()->eq('n.indTemWa'	, '0')
			))
			->orderBy('n.ddd', 'ASC')
			->addOrderBy('n.numero','ASC');
	
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
			
	}
	
	/**
	 * Criar um novo Número
	 * @param string $numero
	 * @return int
	 */
	public static function cria ($numero) {
		global $em,$log,$system;
	
		if (strlen($numero) > 11 || strlen($numero) < 10) {
			return 2;
		}
	
		$ddd 		= substr($numero,0,2);
		$celular	= substr($numero,2);
		
		
		/** Verifica se existe **/
		$num	= $em->getRepository('Entidades\ZgwapNumero')->findOneBy(array( 'ddd' => $ddd,'numero' => $celular));
		
		if ($num) {
			return 1;
		}else{
			$num	= new \Entidades\ZgwapNumero();
		}
	
		try {
			
			$num->setDdd($ddd);
			$num->setNumero($celular);
			$num->setPais("55");
			//$num->setIndTemWa(0);

			$em->persist($num);
			$em->flush();
			$em->detach($num);
			
			return null;
	
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	
	
	/**
	 * Indicar que tem WhatsApp
	 * @param string $numero
	 * @return int
	 */
	public static function setNaoTemWa ($numero) {
		global $em,$log,$system;
	
		$num		= new \Entidades\ZgwapNumero();
	
		if (strlen($numero) > 11 || strlen($numero) < 10) {
			return 2;
		}
		
		$ddd 		= substr($numero,0,2);
		$celular	= substr($numero,2);
			
		/** Verifica se existe **/
		$num	= $em->getRepository('Entidades\ZgwapNumero')->findOneBy(array( 'ddd' => $ddd,'numero' => $celular));
	
		if (!$num) {
			return 0;
		}
			
		try {

			/** Cria o objeto da data atual **/
			$dateTime	= new \DateTime("now");
				
			
			$num->setIndTemWa(0);
			$num->setDataUltVerificacao($dateTime);
	
			$em->persist($num);
			$em->flush();
			$em->detach($num);
				
			return null;
	
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	

	/**
	 * Indicar que tem WhatsApp
	 * @param string $numero
	 * @return int
	 */
	public static function setTemWa ($numero) {
		global $em,$log,$system;

		if (strlen($numero) > 11 || strlen($numero) < 10) {
			return 2;
		}
		
		$ddd 		= substr($numero,0,2);
		$celular	= substr($numero,2);
		
		/** Verifica se existe **/
		$num	= $em->getRepository('Entidades\ZgwapNumero')->findOneBy(array( 'ddd' => $ddd,'numero' => $celular));
	
		if (!$num) {
			return 0;
		}
	
		try {
	
			/** Cria o objeto da data atual **/
			$dateTime	= new \DateTime("now");
	
				
			$num->setIndTemWa(1);
			$num->setDataUltVerificacao($dateTime);
	
			$em->persist($num);
			$em->flush();
			$em->detach($num);
	
			return null;
	
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	
	/**
	 * Indicar que foi enviada a mensagem
	 * @param string $numero
	 * @return int
	 */
	public static function setEnviada ($numero) {
		global $em,$log,$system;
	
		if (strlen($numero) > 11 || strlen($numero) < 10) {
			return 2;
		}
	
		$ddd 		= substr($numero,0,2);
		$celular	= substr($numero,2);
	
		/** Verifica se existe **/
		$num	= $em->getRepository('Entidades\ZgwapNumero')->findOneBy(array( 'ddd' => $ddd,'numero' => $celular));
	
		if (!$num) {
			return 0;
		}
	
		try {
	
			$num->setIndEnviado(1);
			$em->persist($num);
			$em->flush();
			$em->detach($num);
	
			return null;
	
		} catch (\Exception $e) {
			return($e->getMessage());
		}
	}
	

}



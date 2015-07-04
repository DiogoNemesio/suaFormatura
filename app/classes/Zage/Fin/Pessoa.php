<?php

namespace Zage\Fin;


/**
 * Pessoa
 *
 * @package Pessoa
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Pessoa extends \Entidades\ZgfinPessoa {

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
	 * Excluir uma pessoa
	 */
	public static function exclui ($codPessoa) {
		global $em,$system,$log,$tr;
	
		#################################################################################
		## Verificar se a pessoa existe e excluir
		#################################################################################
		$em->getConnection()->beginTransaction();
		try {
		
			if (!isset($codPessoa) || (!$codPessoa)) {
				return ($tr->trans('Falta de Parâmetros'));
			}
		
			$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa));
		
			if (!$oPessoa) {
				return ($tr->trans('Pessoa não encontrada'));
			}
		
			/** Remoção em cascata **/
			
			/** Telefones **/
			$qb 	= $em->createQueryBuilder();
			$qb->delete('Entidades\ZgfinPessoaTelefone', 't');
			$qb->andWhere($qb->expr()->eq('t.codProprietario', ':codPessoa'));
			$qb->setParameter(':codPessoa', $codPessoa);
			$numDeleted = $qb->getQuery()->execute();
		
			/** Endereços **/
			$qb 	= $em->createQueryBuilder();
			$qb->delete('Entidades\ZgfinPessoaEndereco', 'e');
			$qb->andWhere($qb->expr()->eq('e.codPessoa', ':codPessoa'));
			$qb->setParameter(':codPessoa', $codPessoa);
			$numDeleted = $qb->getQuery()->execute();
				
			/** Segmentos **/
			$qb 	= $em->createQueryBuilder();
			$qb->delete('Entidades\ZgfinPessoaSegmento', 's');
			$qb->andWhere($qb->expr()->eq('s.codPessoa', ':codPessoa'));
			$qb->setParameter(':codPessoa', $codPessoa);
			$numDeleted = $qb->getQuery()->execute();
	
			/** Contas **/
			$qb 	= $em->createQueryBuilder();
			$qb->delete('Entidades\ZgfinPessoaConta', 'c');
			$qb->andWhere($qb->expr()->eq('c.codPessoa', ':codPessoa'));
			$qb->setParameter(':codPessoa', $codPessoa);
			$numDeleted = $qb->getQuery()->execute();
				
			$em->remove($oPessoa);
			$em->flush();
			$em->getConnection()->commit();
			
			return null;
		
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			return $e->getMessage();
		}
	}

	/**
	 * Lista os segmentos da Pessoa
	 */
	public static function listaSegmentos ($codPessoa) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		 
		$qb->select('ps')
		->from('\Entidades\ZgfinPessoaSegmento','ps')
		->leftJoin('\Entidades\ZgfinSegmentoMercado', 's', \Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codSegmento = s.codigo')
		->where($qb->expr()->andX(
			$qb->expr()->eq('ps.codPessoa'	, ':codPessoa')
		))
		 
		->addOrderBy('s.descricao', 'ASC')
		 
		->setParameter('codPessoa', $codPessoa);
		$query 		= $qb->getQuery();
		return($query->getResult());
	}
	
	/**
	 * Lista os segmentos que não estão associados a Pessoa
	 */
	public static function listaSegmentosNaoAssociados ($codPessoa) {
		global $em,$system;

		$qb 	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
		
		
		/** Sub Query para retirar os segmentos já associados **/
		$qb2->select('s2.codigo')
		->from('\Entidades\ZgfinPessoaSegmento','ps')
		->leftJoin('\Entidades\ZgfinSegmentoMercado', 's2', \Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codSegmento = s2.codigo')
		->where($qb2->expr()->eq('ps.codPessoa',':codPessoa'));
		
		$qb->select('s')
		->from('\Entidades\ZgfinSegmentoMercado','s')
		->where($qb->expr()->andX(
			$qb->expr()->notIn('s.codigo', $qb2->getDQL())
		))
		->orderBy('s.descricao', 'ASC')
		->setParameter('codPessoa', $codPessoa);
		
		$query 		= $qb->getQuery();
		return($query->getResult());
	}
	
	
	/**
	 *
	 * Busca por Pessoas
	 */
	public static function busca ($string = null,$indCliente 	= false,$indFornec	= false, $indTransp	= false) {
		global $em,$system,$log;
	
		$qb 	= $em->createQueryBuilder();
	
		
		$ic		= ($indCliente	== true) ? 1 : 0; 
		$if		= ($indFornec	== true) ? 1 : 0;
		$it		= ($indTransp	== true) ? 1 : 0;
		
		//$log->debug("IC: ".$ic." IF: ".$if." IT: ".$it);
		
		try {
			$qb->select('p')
			->from('\Entidades\ZgfinPessoa','p')
			->where(
				$qb->expr()->orx(
					$qb->expr()->like($qb->expr()->upper('p.nome'),':string'),
					$qb->expr()->like($qb->expr()->upper('p.cgc'),':string')
				)
			)
			->andWhere($qb->expr()->andX(
				$qb->expr()->eq('p.indAtivo'			,'1'),
				$qb->expr()->eq('p.codOrganizacao'		,':codOrg')
			))
			->orderBy('p.nome','ASC')
			->setParameter('codOrg',		$system->getCodOrganizacao())
			->setParameter('string',		'%'.strtoupper($string).'%');
			
			if ($ic	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('p.indCliente'			,':indCliente')
				));
				$qb->setParameter('indCliente',	$ic);
			}
			if ($if	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('p.indFornecedor'		,':indFornec')
				));
				$qb->setParameter('indFornec',	$if);
			}
			if ($it	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('p.indTransportadora'	,':indTransp')
				));
				$qb->setParameter('indTransp',	$it);
			}
			
			
			$query 		= $qb->getQuery();
			//$log->debug("SQL: ".$query->getSQL());
			//echo $query->getSQL();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Resgata o endereço de uma pessoa, com a seguinte precedência:
	 * 1 -> Endereço de cobrança
	 * 2 -> Endereço de Faturamento
	 * 3 -> Endereço de Entrega
	 * @param number $codPessoa
	 */
	public static function getEndereco($codPessoa) {
		global $em,$system;
		
		#################################################################################
		## Busca o Endereço de cobrança
		#################################################################################
		$oEnd			= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $codPessoa,'codTipoEndereco' => "C"));
		
		if (!$oEnd)		{
			#################################################################################
			## Caso não encontre Busca o Endereço de Faturamento
			#################################################################################
			$oEnd			= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $oConta->getCodPessoa()->getCodigo(),'codTipoEndereco' => "F"));
		
			if (!$oEnd)		{
				#################################################################################
				## Caso não encontre Busca o Endereço de Entrega
				#################################################################################
				$oEnd			= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $oConta->getCodPessoa()->getCodigo(),'codTipoEndereco' => "E"));
			}
		}
		
		return $oEnd;
		
	}

}


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
	 * Inativar uma pessoa
	 */
	public static function inativa ($codPessoa) {
		global $em,$system,$log,$tr;
	
		#################################################################################
		## Verificar se a pessoa inativar
		#################################################################################
		
		try {
	
			if (!isset($codPessoa) || (!$codPessoa)) {
				return ($tr->trans('Falta de Parâmetros'));
			}
	
			$oPessoa	= $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa));
	
			if (!$oPessoa) {
				return ($tr->trans('Pessoa não encontrada'));
			}
	
			/** Inativar **/
				
			$oPessoa->setIndAtivo(0);

			$em->persist($oPessoa);
				
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
	 * Lista os segmentos de uma pessoa associado a uma organizacao
	 */
	public static function listaSegmentosOrganizacao ($codPessoa,$codOrganizacao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
			
		$qb->select('ps')
		->from('\Entidades\ZgfinPessoaSegmentoOrganizacao','ps')
		->leftJoin('\Entidades\ZgfinSegmentoMercado', 's', \Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codSegmento = s.codigo')
		->where($qb->expr()->andX(
				$qb->expr()->eq('ps.codPessoa'		, ':codPessoa'),
				$qb->expr()->eq('ps.codOrganizacao'	, ':codOrganizacao')
		))
			
		->addOrderBy('s.descricao', 'ASC')
		->setParameter('codOrganizacao'	, $codOrganizacao)
		->setParameter('codPessoa'		, $codPessoa);
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
	 * Lista os segmentos que não estão associados a Pessoa
	 */
	public static function listaSegmentosOrganizacaoNaoAssociados ($codPessoa,$codOrganizacao) {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
	
	
		/** Sub Query para retirar os segmentos já associados **/
		$qb2->select('s2.codigo')
		->from('\Entidades\ZgfinPessoaSegmentoOrganizacao','ps')
		->leftJoin('\Entidades\ZgfinSegmentoMercado', 's2', \Doctrine\ORM\Query\Expr\Join::WITH, 'ps.codSegmento = s2.codigo')
		->where($qb->expr()->andX(
				$qb2->expr()->eq('ps.codPessoa',':codPessoa'),
				$qb2->expr()->eq('ps.codOrganizacao',':codOrganizacao')
		));
	
		$qb->select('s')
		->from('\Entidades\ZgfinSegmentoMercado','s')
		->where($qb->expr()->andX(
				$qb->expr()->notIn('s.codigo', $qb2->getDQL())
		))
		->orderBy('s.descricao', 'ASC')
		->setParameter('codOrganizacao', $codOrganizacao)
		->setParameter('codPessoa', $codPessoa);
	
		$query 		= $qb->getQuery();
		return($query->getResult());
	}
	
	
	/**
	 *
	 * Busca por Pessoas
	 */
	public static function busca ($codOrganizacao,$string = null,$indCliente 	= false,$indFornec	= false, $indTransp	= false) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Resgata as informações da Organização
		#################################################################################
		$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
		$codTipoOrg		= $oOrg->getCodTipo()->getCodigo();

		
		
		#################################################################################
		## Verifica se a formatura está sendo administrada por um Cerimonial,
		## para resgatar os fornecedores do cerimonial tb
		#################################################################################
		$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($codOrganizacao);
		$aOrg			= array($codOrganizacao);
		if ($oFmtAdm && $indCliente != true)	{
			$aOrg[]			= $oFmtAdm->getCodigo();
		}
		
		$qb 	= $em->createQueryBuilder();
	
		
		$ic		= ($indCliente	== true) ? 1 : 0; 
		$if		= ($indFornec	== true) ? 1 : 0;
		$it		= ($indTransp	== true) ? 1 : 0;
		
		//$log->debug("IC: ".$ic." IF: ".$if." IT: ".$it);
		
		try {
			$qb->select('p')
			->from('\Entidades\ZgfinPessoa','p')
			->leftJoin('\Entidades\ZgfinPessoaOrganizacao', 'po', \Doctrine\ORM\Query\Expr\Join::WITH, 'po.codPessoa = p.codigo')
			->where(
				$qb->expr()->orx(
					$qb->expr()->like($qb->expr()->upper('p.nome'),':string'),
					$qb->expr()->like($qb->expr()->upper('p.cgc'),':string')
				)
			)
			->andWhere($qb->expr()->andX(
				$qb->expr()->eq('po.indAtivo'			,'1'),
				$qb->expr()->in('po.codOrganizacao'		,':aOrg')
			))
			->orderBy('p.nome','ASC')
			->setParameter('aOrg'			,$aOrg)
			->setParameter('string'			,'%'.strtoupper($string).'%');
			
			if ($ic	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('po.indCliente'			,':indCliente')
				));
				$qb->setParameter('indCliente',	$ic);
			}
			if ($if	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('po.indFornecedor'		,':indFornec')
				));
				$qb->setParameter('indFornec',	$if);
			}
			if ($it	== 1) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('po.indTransportadora'	,':indTransp')
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
			$oEnd			= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $codPessoa,'codTipoEndereco' => "F"));
		
			if (!$oEnd)		{
				#################################################################################
				## Caso não encontre Busca o Endereço de Entrega
				#################################################################################
				$oEnd			= $em->getRepository('Entidades\ZgfinPessoaEndereco')->findOneBy(array('codPessoa' => $codPessoa,'codTipoEndereco' => "E"));
			}
		}
		
		return $oEnd;
		
	}

	/**
	 * Busca um pessoa pelo CGC 
	 * @param unknown $codOrganizacao
	 * @param unknown $cgc
	 */
	public static function buscaPorCgc($codOrganizacao,$cgc) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Cria a instância do QueryBuilder
		#################################################################################
		$qb 	= $em->createQueryBuilder();
	
	
		try {
			$qb->select('p')
			->from('\Entidades\ZgfinPessoa','p')
			->leftJoin('\Entidades\ZgfinPessoaOrganizacao', 'po', \Doctrine\ORM\Query\Expr\Join::WITH, 'po.codPessoa = p.codigo')
			->where(
				$qb->expr()->andX(
					$qb->expr()->eq('po.codOrganizacao'		,':codOrg'),
					$qb->expr()->eq('p.cgc'					,':cgc')
				)
			)
			->setParameter('codOrg'			,$codOrganizacao)
			->setParameter('cgc'			,$cgc);
				
			$query 		= $qb->getQuery();
			return($query->getOneOrNullResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	
	
	
	}
	
	/**
	 * Resgata a pessoa (financeiro) associada ao CGC do usuário na organizacação 
	 * @param unknown $codOrganizacao
	 * @param unknown $codUsuario
	 */
	public static function getPessoaUsuario($codOrganizacao,$codUsuario) {
		global $em,$system,$log,$tr;
		
		#################################################################################
		## Resgata as informações do usuário
		#################################################################################
		$oUsuario			= $em->getRepository('Entidades\ZgsegUsuario')->findOneBy(array('codigo' => $codUsuario));
		
		if (!$oUsuario)		throw new \Exception("Usuário não encontrado em getPessoaUsuario");
		
		return self::buscaPorCgc($codOrganizacao, $oUsuario->getCpf());
	}
	
	/**
	 * 
	 * @param integer $codOrganizacao
	 * @param array $codTipoPessoa
	 * @param string $indTipo
	 * @param array $aCodSegMerc
	 * @param array $aCodCat
	 * @throws \Exception
	 * @return multitype:
	 */
	public static function lista($codOrganizacao,$codTipoPessoa,$indTipo,$aCodSegMerc = null,$aCodCat = null,$dataCadIni = null,$dataCadFim = null,$indAtivo = 2) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		#################################################################################
		## Verifica se a formatura está sendo administrada por um Cerimonial,
		## para resgatar os fornecedores do cerimonial tb
		#################################################################################
		$oFmtAdm		= \Zage\Fmt\Formatura::getCerimonalAdm($codOrganizacao);
		$aOrg			= array($codOrganizacao);
		if ($oFmtAdm && $indTipo != "indCliente")	{
			$aOrg[]			= $oFmtAdm->getCodigo();			
		}

		#################################################################################
		## Segmento de Mercado, caso seja informado, deve ser um array
		#################################################################################
		if ($aCodSegMerc && !is_array($aCodSegMerc)) throw new \Exception("Parâmetro aCodSegMerc deve ser um array");
		
		#################################################################################
		## Categoria, caso seja informado, deve ser um array
		#################################################################################
		if ($aCodCat && !is_array($aCodCat)) throw new \Exception("Parâmetro aCodCat deve ser um array");
		
		#################################################################################
		## Objeto do query builder
		#################################################################################
		$qb 	= $em->createQueryBuilder();
		
		try {
			$qb->select('distinct p')
			->from('\Entidades\ZgfinPessoa','p')
			->leftJoin('\Entidades\ZgfinPessoaOrganizacao', 'po', \Doctrine\ORM\Query\Expr\Join::WITH, 'po.codPessoa = p.codigo')
			->where($qb->expr()->andX(
				$qb->expr()->in('po.codOrganizacao'	, ':codOrganizacao')
			))

			->orderBy('p.nome','ASC')
			->setParameter('codOrganizacao'		,$aOrg);

			if ($codTipoPessoa) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->in('p.codTipoPessoa'	, ':codTipoPessoa')
				));
				$qb->setParameter('codTipoPessoa'		,$codTipoPessoa);
			}
				
			
			if ($indTipo) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('po.'.$indTipo, ':indTipo')
				));
				$qb->setParameter('indTipo'			,1);
			}
			

			if ($indAtivo !== false && $indAtivo !== 2) 	{
				$qb->andWhere($qb->expr()->andX(
					$qb->expr()->eq('po.indAtivo', ':indAtivo')
				));
				$qb->setParameter('indAtivo'			,$indAtivo);
			}
				
			
			if (!empty($aCodSegMerc)) {
				$qb2 	= $em->createQueryBuilder();
				$qb3 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->orX(
						$qb->expr()->exists(
							$qb2->select('ps1')
							->from('\Entidades\ZgfinPessoaSegmento','ps1')
							->leftJoin('\Entidades\ZgfinPessoa', 'p11', \Doctrine\ORM\Query\Expr\Join::WITH, 'p11.codigo = ps1.codPessoa')								
							->where($qb2->expr()->andX(
								$qb2->expr()->eq('ps1.codPessoa'		, 'p.codigo'),
								$qb2->expr()->in('ps1.codSegmento'		, $aCodSegMerc),
								$qb2->expr()->isNotNull('p11.codParceiro')
							))->getDQL()
						),
						$qb->expr()->exists(
							$qb3->select('pso1')
							->from('\Entidades\ZgfinPessoaSegmentoOrganizacao','pso1')
							->leftJoin('\Entidades\ZgfinPessoa', 'p12', \Doctrine\ORM\Query\Expr\Join::WITH, 'p12.codigo = ps1.codPessoa')
							->where($qb3->expr()->andX(
								$qb3->expr()->eq('pso1.codPessoa'		, 'p.codigo'),
								$qb3->expr()->in('pso1.codSegmento'		, $aCodSegMerc),
								$qb3->expr()->isNull('p12.codParceiro')
							))->getDQL()
						)
					)
				);
			}
				
			if (!empty($aCodCat)) {
				$qb4 	= $em->createQueryBuilder();
				$qb5 	= $em->createQueryBuilder();
				$qb->andWhere(
					$qb->expr()->orX(
						$qb->expr()->exists(
							$qb4->select('ps2')
							->from('\Entidades\ZgfinPessoaSegmento','ps2')
							->leftJoin('\Entidades\ZgfinPessoa', 'p21', \Doctrine\ORM\Query\Expr\Join::WITH, 'p21.codigo = ps2.codPessoa')
							->leftJoin('\Entidades\ZgfinSegmentoCategoria', 'sc1', \Doctrine\ORM\Query\Expr\Join::WITH, 'ps2.codSegmento = sc1.codSegmento')
							->where($qb4->expr()->andX(
								$qb4->expr()->eq('ps2.codPessoa'		, 'p.codigo'),
								$qb4->expr()->in('sc1.codCategoria'		, $aCodCat),
								$qb4->expr()->isNotNull('p21.codParceiro')
							))->getDQL()
						),
						$qb->expr()->exists(
							$qb5->select('pso2')
							->from('\Entidades\ZgfinPessoaSegmentoOrganizacao','pso2')
							->leftJoin('\Entidades\ZgfinPessoa', 'p22', \Doctrine\ORM\Query\Expr\Join::WITH, 'p22.codigo = pso2.codPessoa')
							->leftJoin('\Entidades\ZgfinSegmentoCategoria', 'sc2', \Doctrine\ORM\Query\Expr\Join::WITH, 'pso2.codSegmento = sc2.codSegmento')
							->where($qb5->expr()->andX(
								$qb5->expr()->eq('pso2.codPessoa'		, 'p.codigo'),
								$qb5->expr()->in('sc2.codCategoria'		, $aCodCat),
								$qb5->expr()->isNull('p22.codParceiro')
							))->getDQL()
						)
					)
				);
			}
				
			
			$query 		= $qb->getQuery();
			return ($query->getResult()); 
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}


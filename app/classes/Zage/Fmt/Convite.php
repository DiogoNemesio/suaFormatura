<?php

namespace Zage\Fmt;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;

/**
 * Gerenciar as convites e convidados
 * 
 * @package: Rifa
 * @Author: Diogo Nemésio
 * @version: 1.0.1
 * 
 */

class Convite {

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
	 * Lista vendas por formando
	 *
	 * @param integer $codOrganizacao
	 * @return array
	 */
	public static function listaVendaConviteFormando() {
		global $em,$system;
	
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('v')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 	= i.codVenda')
			->leftJoin('\Entidades\ZgfmtConviteExtraEventoConf'	,'c',	\Doctrine\ORM\Query\Expr\Join::WITH, 'c.codEvento 	= i.codEvento')
			->leftJoin('\Entidades\ZgadmOrganizacao'			,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'				, ':codOrganizacao')
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
				
			->groupBy('v.codFormando');
			//->orderBy('r.codigo', 'ASC');
			
			$query 		= $qb->getQuery();
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Resgata o código da Pessoa de um formando
	 *
	 * @param 
	 * @return código
	 */
	public static function getCodigoUsuarioPessoa($codUsuario) {
		global $em,$system, $log;
		
		$qb 	= $em->createQueryBuilder();
	
		try {
			$qb->select('p.codigo')
			->from('\Entidades\ZgfinPessoa','p')
			->leftJoin('\Entidades\ZgsegUsuario','u',	\Doctrine\ORM\Query\Expr\Join::WITH, 'u.cpf = p.cgc')
			->where($qb->expr()->andx(
					$qb->expr()->eq('u.codigo' , ':codUsuario')
				)
			)
			->setParameter('codUsuario', $codUsuario);
	
			$query 		= $qb->getQuery();
			return($query->getSingleScalarResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	* Lista os eventos que estão aptp para a venda presencial
	*
	* @param 
	* @return array
	*/
	public static function listaConviteAptoVenda() {
		global $em,$system, $log;
	
		$qb 	= $em->createQueryBuilder();
		$hoje 	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], date($system->config["data"]["dateFormat"]." 00:00:00"));
		
		try {
			$qb->select('c')
			->from('\Entidades\ZgfmtConviteExtraEventoConf','c')
				->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
				->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'					, ':codOrganizacao'),
					$qb->expr()->andx(
						$qb->expr()->orX(
							$qb->expr()->isNull('c.dataInicioPresencial'),
							$qb->expr()->lte('c.dataInicioPresencial'	, ':now')
						)
					),
					$qb->expr()->andx(
						$qb->expr()->orX(
							$qb->expr()->isNull('c.dataFimPresencial'),
							$qb->expr()->gte('c.dataFimPresencial'		, ':now')
						)
					)
				)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', $hoje)
				
			->orderBy('c.codigo', 'ASC');
				
			$query 		= $qb->getQuery();
			
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Lista os eventos que estão aptp para a venda na internet
	 *
	 * @param
	 * @return array
	 */
	public static function listaConviteAptoVendaInternet() {
		global $em,$system, $log;
	
		$qb 	= $em->createQueryBuilder();
		$hoje 	= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], date($system->config["data"]["dateFormat"]." 00:00:00"));
	
		try {
			$qb->select('c')
			->from('\Entidades\ZgfmtConviteExtraEventoConf','c')
			->leftJoin('\Entidades\ZgadmOrganizacao'		,'o',	\Doctrine\ORM\Query\Expr\Join::WITH, 'o.codigo 	= c.codOrganizacao')
			->where($qb->expr()->andx(
					$qb->expr()->eq('o.codigo'					, ':codOrganizacao'),
					$qb->expr()->lte('c.dataInicioInternet'	, ':now'),
					$qb->expr()->gte('c.dataFimInternet'	, ':now')
			)
			)
	
			->setParameter('codOrganizacao', $system->getCodOrganizacao())
			->setParameter('now', $hoje)
	
			->orderBy('c.codigo', 'ASC');
	
			$query 		= $qb->getQuery();
				
			return($query->getResult());
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Quantidade convite disponivel por configuração de evento
	 *
	 * @param integer $codPessoa (FORMANDO)
	 * @param integer $codEvento
	 * @return array
	 */
	public static function qtdeConviteDispFormando($codPessoa, $codEvento) {
		global $em,$system;
	
		
		#################################################################################
		## O Cálculo de quantidade disponível será :a quantidade máxima de convites por formando do evento
		## em questão, menos as vendas e as transferências de débito
		## mais as transferências de crédito.
		#################################################################################
		
		#################################################################################
		## Resgatar as configurações do evento, onde está configurado a quantidade máxima
		## por formando, caso não encontre retornar 0
		#################################################################################
		$oEveConf	= $em->getRepository('Entidades\ZgfmtConviteExtraEventoConf')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codEvento' => $codEvento));
		if (!$oEveConf)	return 0;
		$qtdeMax	= $oEveConf->getQtdeMaxAluno();
		
		
		#################################################################################
		## Calcular as vendas já efetuadas
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
			$qb1->select('sum(i.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraVendaItem','i')
			->leftJoin('\Entidades\ZgfmtConviteExtraVenda'		,'v',	\Doctrine\ORM\Query\Expr\Join::WITH, 'v.codigo 			= i.codVenda')
			->leftJoin('\Entidades\ZgfinContaReceber'			,'cr',	\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codTransacao 	= v.codTransacao')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('v.codOrganizacao'		, ':codOrganizacao'),
				$qb1->expr()->eq('v.codFormando'		, ':codFormando'),
				$qb1->expr()->eq('i.codEvento'			, ':codEvento'),
				$qb1->expr()->notIn('cr.codStatus'		, ':codStatus')
			))
	
			->setParameter('codOrganizacao'	,$system->getCodOrganizacao())
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento)
			->setParameter('codStatus'		,array("C"));
				
			$query 			= $qb1->getQuery();
			$qtdeVendida	= $query->getSingleScalarResult(); 
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular as transferências de crédito
		#################################################################################
		$qb2 	= $em->createQueryBuilder();
		
		try {
			$qb2->select('sum(t.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraTransf','t')
			->where($qb2->expr()->andx(
				$qb2->expr()->eq('t.codEvento'			, ':codEvento'),
				$qb2->expr()->eq('t.codFormandoDestino'	, ':codFormando')
			))
		
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento);
		
			$query 			= $qb2->getQuery();
			$qtdeRecebida	= $query->getSingleScalarResult();
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular as transferências de Débito
		#################################################################################
		$qb3 	= $em->createQueryBuilder();
		
		try {
			$qb3->select('sum(t.quantidade) as qtde')
			->from('\Entidades\ZgfmtConviteExtraTransf','t')
			->where($qb3->expr()->andx(
				$qb3->expr()->eq('t.codEvento'			, ':codEvento'),
				$qb3->expr()->eq('t.codFormandoOrigem'	, ':codFormando')
			))
		
			->setParameter('codFormando'  	,$codPessoa)
			->setParameter('codEvento'		,$codEvento);
		
			$query 			= $qb3->getQuery();
			$qtdeCedida	= $query->getSingleScalarResult();
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		$qtde		= $qtdeMax - $qtdeVendida - $qtdeCedida + $qtdeRecebida;
		
		return ($qtde);
		
	}
	
	/**
	 * Valor da taxa de conveniência
	 *
	 * @param integer $codFormaVenda (Presencial ou internet)
	 * @param integer $codContaRec   (Conta de recebimemento)
	 * @param integer $codFormaPag   (Forma de pagamento)
	 * @return array
	 */
	public static function calcTaxaConveniencia($codFormaVenda, $codContaRec, $codFormaPag) {
		global $em,$system;
	
	
		#################################################################################
		## O Cálculo da taxa de convêniencia será: o valor da taxa de administração
		## configurado para o forma da venda, mais o valor do custo do boleto, caso
		## esteja configurado para adicionar.
		#################################################################################
	
		#################################################################################
		## Resgatar as configurações da venda para o tipo
		#################################################################################
		$oVendaConf	= $em->getRepository('Entidades\ZgfmtConviteExtraVendaConf')->findOneBy(array('codFormatura' => $system->getCodOrganizacao(),'codVendaTipo' => $codFormaVenda));
		if (!$oVendaConf)	return 0;
		
		$taxaAdm 			=($oVendaConf->getTaxaAdministracao()) ? $oVendaConf->getTaxaAdministracao() : 0;
		$indAddTaxaBoleto	=($oVendaConf->getIndAdicionarTaxaBoleto()) ? $oVendaConf->getIndAdicionarTaxaBoleto() : 0;
		$codContaBoleto		=($oVendaConf->getCodContaBoleto()) ? $oVendaConf->getCodContaBoleto()->getCodigo() : 0;
		
		$taxaConv['COVENIENCIA'] = $taxaAdm;
	
		#################################################################################
		## Analisar a conta de boleto
		#################################################################################
		if ($codFormaPag == 'BOL'){
			if ($indAddTaxaBoleto == 1){
				$oConta	= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codContaRec));
				
				$taxaBol	= ($oConta->getValorBoleto()) ? $oConta->getValorBoleto() : 0;
				$taxaConv['BOLETO'] = $taxaBol;
			}else{
				$taxaConv['BOLETO'] = 0;
			}
			
		}else{
			$taxaConv['BOLETO'] = 0;
		}
		
		#################################################################################
		## Retornar o valor da taxa de coveniência
		#################################################################################
		return ($taxaConv);
	
	}
	
	
	/**
	 * Resgatar o valor de sistema de uma determinada conta
	 * @param integer $codConta
	 */
	function contaEhDeConviteExtra($codConta) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Resgatar a categoria de Convite extra
		#################################################################################
		$codCatConviteExtra			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_CONVITE_EXTRA");
		$aCatConv					= array($codCatConviteExtra);
		
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
	
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('count(crr.codigo)')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('crr.codContaRec'		, ':codConta'),
				$qb1->expr()->in('crr.codCategoria'		, ':codCategoria')
			))
			->setParameter('codConta'		, $codConta)
			->setParameter('codCategoria'	, $aCatConv);
	
			$query 				= $qb1->getQuery();
			$count				= (int) $query->getSingleScalarResult();
			
			if ($count > 0) {
				return true;
			}else{
				return false;
			}
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	
}

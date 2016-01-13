<?php

namespace Zage\Fin;

/**
 * Gerenciar Adiantamentos
 * 
 * @package: Adiantamento
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Adiantamento {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
	}
	
	/**
	 * Salvar uma movimentação de adiantamento
	 * @param unknown $codOrganizacao
	 * @param unknown $codOrigem
	 * @param unknown $codTipoOper
	 * @param unknown $codPessoa
	 * @param unknown $codContaRec
	 * @param unknown $codContaPag
	 * @param unknown $data
	 * @param unknown $valor
	 * @param unknown $codGrupoMov
	 */
	public static function salva($codOrganizacao,$codOrigem,$codTipoOper,$codPessoa,$codContaRec,$codContaPag,$data,$valor,$codGrupoMov) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$log,$tr,$system;
		
		#################################################################################
		## Resgatar os objetos das chaves estrangeiras
		#################################################################################
		$oOrg		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codOrganizacao));
		$oOrigem	= $em->getRepository('Entidades\ZgadmOrigem')->findOneBy(array('codigo' => $codOrigem));
		$oTipoOper	= $em->getRepository('Entidades\ZgfinOperacaoTipo')->findOneBy(array('codigo' => $codTipoOper));
		$oPessoa	= ($codPessoa)		? $em->getRepository('Entidades\ZgfinPessoa')->findOneBy(array('codigo' => $codPessoa)) : null;
		
		if (is_object($codContaPag))	{
			$oContaPag	= $codContaPag;
		}else{
			$oContaPag	= ($codContaPag)	? $em->getRepository('Entidades\ZgfinContaPagar')->findOneBy(array('codigo' => $codContaPag)) : null;
		}
		
		if (is_object($codContaRec))	{
			$oContaRec	= $codContaRec;
		}else{
			$oContaRec	= ($codContaRec)	? $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codContaRec)) : null;
		}
		
		#################################################################################
		## Validação da data
		#################################################################################
		if (!isset($data) || empty($data)) 		throw new \Exception($tr->trans("Data do adiantamento deve ser informada"));
		$valData	= new \Zage\App\Validador\DataBR();
		if ($valData->isValid($data) == false) 	throw new \Exception($tr->trans("Data do adiantamento inválida"));
		
		#################################################################################
		## Criar o objeto da data
		#################################################################################
		if (!empty($data)) {
			$oData 		= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $data);
		}else{
			$oData		= null;
		}
		
		#################################################################################
		## Validação do valor
		#################################################################################
		if ( (!isset($valor)) || empty($valor))  	throw new \Exception($tr->trans("Valor do adiantamento deve ser informado"));
		if ($valor 			< 0)					throw new \Exception($tr->trans("Valor do adiantamento não pode ser negativo"));
		
		#################################################################################
		## Validação da organização
		## 1) a organização é obrigatória
		## 2) se for inforamada conta a pagar / receber, a organização deve ser a mesma da conta
		#################################################################################
		if (!$codOrganizacao)	throw new \Exception($tr->trans("Organização não informada"));
		if (!$oOrg)				throw new \Exception($tr->trans("Organização não encontrada"));
		if (($oContaPag) && ($oContaPag->getCodOrganizacao()->getCodigo() != $codOrganizacao ))	throw new \Exception($tr->trans("Organização deve ser a mesma da conta a pagar"));
		if (($oContaRec) && ($oContaRec->getCodOrganizacao()->getCodigo() != $codOrganizacao ))	throw new \Exception($tr->trans("Organização deve ser a mesma da conta a receber"));
		

		#################################################################################
		## Validação da origem
		#################################################################################
		if (!$codOrigem)			throw new \Exception($tr->trans("Origem não informada"));
		if (!$oOrigem)				throw new \Exception($tr->trans("Origem não encontrada"));
		
		#################################################################################
		## Validação do tipo de operacção
		#################################################################################
		if (!$codTipoOper)			throw new \Exception($tr->trans("Tipo de operação não informada"));
		if (!$oTipoOper)			throw new \Exception($tr->trans("Tipo de operação não encontrada"));
		
		#################################################################################
		## Validação da conta
		## Não pode informar a conta a pagar e a receber na mesma movimentação
		#################################################################################
		if ($oContaPag && $oContaRec)				throw new \Exception($tr->trans("Movimentação deve pertencer a apenas uma conta, foi informada uma conta a pagar e uma a receber"));
		
		#################################################################################
		## Validação geral
		## uma movimentação deve pertencer a uma pessoa, caso seja oriunda de uma conta a receber
		## ou a pagar
		#################################################################################
		if (($oContaPag || $oContaRec) && (!$codPessoa)) throw new \Exception($tr->trans("Movimentação deve pertencer a uma pessoa quando for informado uma conta a pagar/receber"));
		
		#################################################################################
		## Validação da pessoa
		#################################################################################
		if ($codPessoa && (is_object($oContaRec)) && ($oContaRec->getCodPessoa()) ) {
			$log->info("codPessoa: ".$codPessoa." Pessoa da conta: ".$oContaRec->getCodPessoa()->getCodigo());
			if ($codPessoa != $oContaRec->getCodPessoa()->getCodigo())	throw new \Exception($tr->trans("Código da Pessoa difere do cliente da conta a receber"));
		}
		if ($codPessoa && (is_object($oContaPag)) && ($oContaPag->getCodPessoa()) ) {
			if ($codPessoa != $oContaPag->getCodPessoa()->getCodigo())	throw new \Exception($tr->trans("Código da Pessoa difere do fornecedor da conta a pagar"));
		}
		
		#################################################################################
		## Cria o adiantamento
		#################################################################################
		try {
			$oAdiant	= new \Entidades\ZgfinMovAdiantamento();
			$oAdiant->setCodOrganizacao($oOrg);
			$oAdiant->setCodOrigem($oOrigem);
			$oAdiant->setCodTipoOperacao($oTipoOper);
			$oAdiant->setCodPessoa($oPessoa);
			$oAdiant->setCodContaRec($oContaRec);
			$oAdiant->setCodContaPag($oContaPag);
			$oAdiant->setDataAdiantamento($oData);
			$oAdiant->setDataTransacao(new \DateTime());
			$oAdiant->setValor($valor);
			$oAdiant->setCodGrupoMov($codGrupoMov);
				
			$em->persist($oAdiant);
		} catch (\Exception $e) {
			throw new \Exception('Erro ao salvar adiantamento: '.$e->getMessage());
		}
		
	}
	
	/**
	 * Calcular o saldo de adiantamento de uma Pessoa
	 * @param integer $codOrganizacao
	 * @param integer $codPessoa
	 */
	public static function getSaldo ($codOrganizacao,$codPessoa) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		$qb1	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
		

		#################################################################################
		## Calcular os créditos de adiantamento
		#################################################################################
		try {
			$qb1->select('sum(h.valor) as valor')
			->from('\Entidades\ZgfinMovAdiantamento','h')
			->where($qb1->expr()->andX(
				$qb1->expr()->eq('h.codOrganizacao'	, ':codOrganizacao'),
				$qb1->expr()->eq('h.codPessoa'		, ':codPessoa'),
				$qb1->expr()->eq('h.codTipoOperacao', ':codTipoOperacao')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codTipoOperacao'	, "C")
			->setParameter('codPessoa'			, $codPessoa);
			$query 		= $qb1->getQuery();
			$creditos	= $query->getSingleScalarResult();
		
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		

		#################################################################################
		## Calcular os Débitos de adiantamento
		#################################################################################
		try {
			$qb2->select('sum(h.valor) as valor')
			->from('\Entidades\ZgfinMovAdiantamento','h')
			->where($qb1->expr()->andX(
				$qb2->expr()->eq('h.codOrganizacao'	, ':codOrganizacao'),
				$qb2->expr()->eq('h.codPessoa'		, ':codPessoa'),
				$qb2->expr()->eq('h.codTipoOperacao', ':codTipoOperacao')
			))
			->setParameter('codOrganizacao'		, $codOrganizacao)
			->setParameter('codTipoOperacao'	, "D")
			->setParameter('codPessoa'			, $codPessoa);
			$query 		= $qb2->getQuery();
			$debitos	= $query->getSingleScalarResult();
		
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Calcular o saldo
		#################################################################################
		$saldo		= round(floatval($creditos),2) - round(floatval($debitos),2);
		return $saldo;
	}



	/**
	 * Calcular o saldo de adiantamento por Pessoa de uma organização
	 * @param integer $codOrganizacao
	 */
	public static function listaSaldoPorPessoa($codOrganizacao) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('Entidades\ZgfinPessoa'	, 'P');
			$rsm->addScalarResult('COD_PESSOA'				, 'COD_PESSOA');
			$rsm->addScalarResult('NOME'					, 'NOME');
			$rsm->addScalarResult('CGC'						, 'CGC');
			$rsm->addScalarResult('SALDO'					, 'SALDO');
		
			$query 	= $em->createNativeQuery("
			SELECT	P.CODIGO AS COD_PESSOA,P.CGC,P.NOME, SUM(IF(A.COD_TIPO_OPERACAO = 'C',IFNULL(A.VALOR,0),IFNULL(A.VALOR,0)*-1)) SALDO 
			FROM 	ZGFIN_MOV_ADIANTAMENTO 		A
			LEFT OUTER JOIN ZGFIN_PESSOA P ON (A.COD_PESSOA = P.CODIGO)
			WHERE 	A.COD_ORGANIZACAO 		= :codOrg
			GROUP 	BY P.CODIGO,P.CGC,P.NOME
			ORDER	BY P.NOME
			", $rsm);
			$query->setParameter('codOrg'		, $codOrganizacao);

			return($query->getResult());
		
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		
	}
	
}
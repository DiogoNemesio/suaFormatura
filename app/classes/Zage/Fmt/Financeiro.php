<?php

namespace Zage\Fmt;

/**
 * Informações gerenciais financeiras da formatura
 * 
 * @package: Financeiro
 * @Author: Daniel Cassela
 * @version: 1.0.1
 * 
 */

class Financeiro {

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
	 * Calcular o valor arrecadado por uma formatura
	 * @param int $codFormatura
	 */
	public static function calcValorArrecadadoFormatura($codFormatura) {
		
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		
		#################################################################################
		## Verifica se a organização existe e é uma formatura
		#################################################################################
		$oOrg											= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $codFormatura)); 
		if (!$oOrg)										throw new \Exception("Organização não encontrada em ".__FUNCTION__);
		if ($oOrg->getCodTipo()->getCodigo() !== "FMT")	throw new \Exception("Não foi possível calcular o total arrecadado, pois a organização não é uma formatura");
				
		#################################################################################
		## Carrega as configurações da formatura
		#################################################################################
		$oOrgFmt										= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
		if ($oOrgFmt) {
			$pctJuros		= $oOrgFmt->getPctJurosTurma();
			$pctMora		= $oOrgFmt->getPctMoraTurma();
			$pctConvite		= $oOrgFmt->getPctConviteExtraTurma();
			$pctConviteCer	= 100 - $pctConvite;
		}else{
			$pctJuros		= 0;
			$pctMora		= 0;
			$pctConvite		= 0;
			$pctConviteCer	= 100;
		}
		
		#################################################################################
		## Resgatar a categoria de Convite extra
		#################################################################################
		$codCatConviteExtra			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_CONVITE_EXTRA");
		$aCatConv					= array($codCatConviteExtra);
		
		#################################################################################
		## Resgatar as categorias de boleto e taxa de administração
		#################################################################################
		$codCatBoleto				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
		$codCatTxAdm				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
		$aCatBolTx					= array($codCatBoleto,$codCatTxAdm);
		
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
		$qb3 	= $em->createQueryBuilder();
	
		try {
			
			#################################################################################
			## O Valor Arrecadado, consiste no valor recebido sem o júros / mora +
			## o Percentual de júros / mora configurado para ficar com a formatura -
			## o Percentual dos convites extras configurado para ficar com  o cerimonial -
			## o valor do boleto -
			## o valor de taxa de administração
			#################################################################################
					
			
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('SUM(hr.valorRecebido + hr.valorOutros - (hr.valorDesconto) ) as total, sum(hr.valorJuros) as juros, sum(hr.valorMora) as mora')
			->from('\Entidades\ZgfinHistoricoRec','hr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'hr.codContaRec = cr.codigo')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb1->expr()->in('cr.codStatus'			, ':status')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('status'			, array("L","P"));
	
			$query 				= $qb1->getQuery();
			$rec				= $query->getOneOrNullResult();
			$valorPrincipal		= \Zage\App\Util::to_float($rec["total"]);
			
			#################################################################################
			## Valor de júros / multa da formatura
			#################################################################################
			$valorJuros			= (\Zage\App\Util::to_float($rec["juros"])	* $pctJuros	/ 100);
			$valorMora			= (\Zage\App\Util::to_float($rec["mora"]) 	* $pctMora	/ 100);
				
			#################################################################################
			## Somatório dos valores de convite extra
			#################################################################################
			try {
				$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
				$rsm->addEntityResult('Entidades\ZgfinContaReceber'	, 'r');
				$rsm->addScalarResult('VALOR'					, 'VALOR');
			
				$query 	= $em->createNativeQuery("
					SELECT	SUM(IF((REC.VALOR > RAT.VALOR),RAT.VALOR,REC.VALOR)) VALOR   
					FROM 	ZGFIN_CONTA_RECEBER R,
					        (SELECT		HR1.COD_CONTA_REC,SUM(HR1.VALOR_RECEBIDO - HR1.VALOR_DESCONTO + HR1.VALOR_OUTROS) VALOR
					         FROM		ZGFIN_CONTA_RECEBER			R1,
										ZGFIN_HISTORICO_REC 		HR1
							 WHERE		R1.CODIGO					= HR1.COD_CONTA_REC
							 AND		R1.COD_ORGANIZACAO			= :codOrganizacao
							 AND		R1.COD_STATUS				IN (:status)
					         GROUP BY	HR1.COD_CONTA_REC
					        ) REC,
					        (SELECT		RR2.COD_CONTA_REC,SUM(RR2.VALOR) VALOR
					         FROM		ZGFIN_CONTA_RECEBER			R2,
										ZGFIN_CONTA_RECEBER_RATEIO	RR2
							 WHERE		R2.CODIGO					= RR2.COD_CONTA_REC
							 AND		R2.COD_ORGANIZACAO			= :codOrganizacao
							 AND		RR2.COD_CATEGORIA 			IN (:aCatConv)
							 AND		R2.COD_STATUS				IN (:status)
					         GROUP BY 	RR2.COD_CONTA_REC
					        ) RAT
					WHERE   R.CODIGO 				= REC.COD_CONTA_REC
					AND	    R.CODIGO 				= RAT.COD_CONTA_REC
					AND		R.COD_ORGANIZACAO		= :codOrganizacao
					AND		R.COD_STATUS			IN (:status)
					AND		EXISTS (
							SELECT 1
							FROM	ZGFIN_CONTA_RECEBER_RATEIO RR3
							WHERE	RR3.COD_CONTA_REC		= R.CODIGO
							AND		RR3.COD_CATEGORIA 		IN (:aCatConv)
					)
				", $rsm);
				$query->setParameter('codOrganizacao'	, $codFormatura);
				$query->setParameter('status'			, array("L","P"));
				$query->setParameter('aCatConv'			, $aCatConv);
				
			
				$valorConvite	= \Zage\App\Util::to_float($query->getSingleScalarResult());
				$valorConvCer	= round(($valorConvite * $pctConviteCer) /100,2);
				
			
			} catch (\Exception $e) {
				\Zage\App\Erro::halt($e->getMessage());
			}

			#################################################################################
			## Somatório de boleto e taxa de adm
			#################################################################################
			$qb3->select('SUM(crr.valor) valor')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codContaRec = cr.codigo')
			->where($qb3->expr()->andx(
				$qb3->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb3->expr()->in('cr.codStatus'			, ':status'),
				$qb3->expr()->in('crr.codCategoria'		, ':aCatBol')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('status'			, array("L"))
			->setParameter('aCatBol'		, $aCatBolTx);
				
			$query 			= $qb3->getQuery();
			$valorBolTx		= \Zage\App\Util::to_float($query->getSingleScalarResult());

			#################################################################################
			## Valor total
			#################################################################################
			$valorTotal			= $valorPrincipal + $valorJuros + $valorMora - $valorConvCer - $valorBolTx;
			
			return ($valorTotal);
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	/**
	 * Calcular o valor gasto por uma formatura
	 * @param int $codFormatura
	 */
	public static function calcValorGastoFormatura($codFormatura) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
	
		#################################################################################
		## Resgata os dados de previsão orcamentária
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('SUM(hp.valorPago + hp.valorOutros + hp.valorJuros + hp.valorMora - (hp.valorDesconto) ) as total')
			->from('\Entidades\ZgfinHistoricoPag','hp')
			->leftJoin('\Entidades\ZgfinContaPagar', 'cp', \Doctrine\ORM\Query\Expr\Join::WITH, 'hp.codContaPag = cp.codigo')
			->where($qb1->expr()->andx(
			$qb1->expr()->eq('cp.codOrganizacao'	, ':codOrganizacao'),
			$qb1->expr()->in('cp.codStatus'			, ':status')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('status'			, array("L","P"));
			
			$query 				= $qb1->getQuery();
			$rec				= $query->getOneOrNullResult();
			$valorPrincipal		= \Zage\App\Util::to_float($rec["total"]);
				
			return ($valorPrincipal);

		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
	
	/**
	 * Calcular o valor do boleto para aquela organização
	 * @param int $codOrganizacao
	 */
	public static function getValorBoleto($codOrganizacao,$codConta = null) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system;
		
		#################################################################################
		## Validações
		#################################################################################
		if (!$codConta)	throw new \Exception('Parâmetro codConta não pode ser nulo');
		
		#################################################################################
		## Resgatar o valor do boleto 
		#################################################################################
		$oConta		= $em->getRepository('Entidades\ZgfinConta')->findOneBy(array('codigo' => $codConta));
		
		if ($oConta)	{
			return \Zage\App\Util::to_float($oConta->getValorBoleto());
		}else{
			return 0;
		}
		
	}
	
	/**
	 * Resgata o valor total já provisionado para todos os formandos de uma formatura
	 * @param unknown $codFormatura
	 */
	public static function getValorProvisionadoPorFormando($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array de status que não serão calculados
		#################################################################################
		$aStatusCanc	= array ("S","C");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
		$aCat						= array($codCatMensalidade,$codCatSistema);

		#################################################################################
		## Somar os valores dividos por categora
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('mensalidade'					, 'mensalidade');
			$rsm->addScalarResult('sistema'						, 'sistema');
				
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,SUM(IF((CRR.COD_CATEGORIA = :catMensalidade),CRR.VALOR,0)) as mensalidade, SUM(IF((CRR.COD_CATEGORIA = :catSistema),CRR.VALOR,0)) as sistema
					FROM 	ZGFIN_CONTA_RECEBER 		CR,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_RECEBER_RATEIO	CRR
					WHERE   CR.CODIGO 				= CRR.COD_CONTA_REC
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		CR.COD_STATUS			NOT IN (:status)
					AND		CRR.COD_CATEGORIA		IN (:categoria)
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatusCanc);
			$query->setParameter('catMensalidade'	,$codCatMensalidade);
			$query->setParameter('catSistema'		,$codCatSistema);
			$query->setParameter('categoria'		,$aCat);
				
			$info		= $query->getResult();
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		return ($info);
				
			#################################################################################
			## SubQuery para filtrar apenas as contas que estão nas categorias configuradas acima
			#################################################################################
			/*$qbEx->select('crr')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qbEx->expr()->andx(
			$qbEx->expr()->eq('crr.codContaRec'		, 'cr.codigo'),
			$qbEx->expr()->in('crr.codCategoria'	, ':categoria')
			));
				
			$qb->select('p','SUM(cr.valor + cr.valorOutros - cr.valorDesconto) as total')
			->from('\Entidades\ZgfinContaReceber','cr')
			->leftJoin('\Entidades\ZgfinPessoa'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa 	= p.codigo')
			->where($qb->expr()->andx(
			$qb->expr()->eq('cr.codOrganizacao'		, ':codOrganizacao'),
			$qb->expr()->notIn('cr.codStatus'		, ':status'),
			$qb->expr()->exists($qbEx->getDql())
			))
			->groupBy("p.cgc")
				
			->setParameter('codOrganizacao'	,$codFormatura)
			->setParameter('status'			,$aStatusCanc)
			->setParameter('categoria'		,$aCat);
				

			$query 		= $qb->getQuery();
			$info		= $query->getResult();

			return ($info);*/
	}
	
	/**
	 * Resgata o valor total já provisionado por formando
	 * @param unknown $codFormatura
	 */
	public static function getValorProvisionadoUnicoFormando($codFormatura, $cpf) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array de status que não serão calculados
		#################################################################################
		$aStatusCanc	= array ("S","C");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
		$aCat						= array($codCatMensalidade,$codCatSistema);

		#################################################################################
		## Criação dos objetos do querybuilder
		#################################################################################
		$qb 	= $em->createQueryBuilder();
		$qbEx	= $em->createQueryBuilder();

		try {

			$qb->select('SUM(crr.valor) as total')
			->from('\Entidades\ZgfinContaReceber'			,'cr')
			->leftJoin('\Entidades\ZgfinPessoa'				,'p'	,\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codPessoa 	= p.codigo')
			->leftJoin('\Entidades\ZgfinContaReceberRateio'	,'crr'	,\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codigo	 	= crr.codContaRec')
			->where($qb->expr()->andx(
					$qb->expr()->eq('cr.codOrganizacao'		, ':codOrganizacao'),
					$qb->expr()->eq('p.cgc'					, ':cpf'),
					$qb->expr()->notIn('cr.codStatus'		, ':status'),
					$qb->expr()->in('crr.codCategoria'		, ':categoria')
			))
			
			->setParameter('codOrganizacao'	,$codFormatura)
			->setParameter('cpf'			,$cpf)
			->setParameter('status'			,$aStatusCanc)
			->setParameter('categoria'		,$aCat);

			$query 		= $qb->getQuery();
			$info		= $query->getSingleScalarResult();

			return ($info);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	
}

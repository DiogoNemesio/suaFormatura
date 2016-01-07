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
			$pctJuros		= \Zage\App\Util::to_float($oOrgFmt->getPctJurosTurma());
			$pctMora		= \Zage\App\Util::to_float($oOrgFmt->getPctMoraTurma());
			$pctConvite		= \Zage\App\Util::to_float($oOrgFmt->getPctConviteExtraTurma());
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
		## Resgatar as categorias 
		#################################################################################
		$codCatBoleto				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
		$codCatTxAdm				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
		$aCatBolTx					= array($codCatBoleto,$codCatTxAdm);
		
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
	
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
			$qb1->select('SUM(hr.valorRecebido + hr.valorOutros - (hr.valorDesconto) ) as total, sum(hr.valorJuros - hr.valorDescontoJuros) as juros, sum(hr.valorMora - hr.valorDescontoMora) as mora')
			//$qb1->select('SUM(hr.valorRecebido + hr.valorOutros - (hr.valorDesconto) ) as total, sum(hr.valorJuros) as juros, sum(hr.valorMora) as mora')
			->from('\Entidades\ZgfinHistoricoRec','hr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'hr.codContaRec = cr.codigo')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb1->expr()->notIn('cr.codStatus'		, ':statusCanc')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('statusCanc'		, array("S","C"));
	
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
			$qb2->select('SUM(crr.valor) valor')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codContaRec = cr.codigo')
			->where($qb2->expr()->andx(
				$qb2->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb2->expr()->in('cr.codStatus'			, ':status'),
				$qb2->expr()->in('crr.codCategoria'		, ':aCatBol')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('status'			, array("L"))
			->setParameter('aCatBol'		, $aCatBolTx);
				
			$query 			= $qb2->getQuery();
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
				$qb1->expr()->notIn('cp.codStatus'		, ':statusCanc')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('statusCanc'		, array("S","C"));
			
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
	public static function getValorBoleto($codConta) {

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
			return ($info);
			
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
				
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
		## Somar os valores dividos por categora
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addScalarResult('mensalidade'					, 'mensalidade');
			$rsm->addScalarResult('sistema'						, 'sistema');
				
			$query 	= $em->createNativeQuery("
					SELECT	SUM(IF((CRR.COD_CATEGORIA = :catMensalidade),CRR.VALOR,0)) as mensalidade, SUM(IF((CRR.COD_CATEGORIA = :catSistema),CRR.VALOR,0)) as sistema
					FROM 	ZGFIN_CONTA_RECEBER 		CR,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_RECEBER_RATEIO	CRR
					WHERE   CR.CODIGO 				= CRR.COD_CONTA_REC
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		CR.COD_STATUS			NOT IN (:status)
					AND		CRR.COD_CATEGORIA		IN (:categoria)
					AND		P.CGC					= :cpf
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatusCanc);
			$query->setParameter('catMensalidade'	,$codCatMensalidade);
			$query->setParameter('catSistema'		,$codCatSistema);
			$query->setParameter('categoria'		,$aCat);
			$query->setParameter('cpf'				,$cpf);
				
			$info		= $query->getOneOrNullResult();
			return ($info);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	
	}
	

	/**
	 * Resgata o valor de mensalidades já pago por um Formando
	 * @param unknown $codFormatura
	 */
	public static function getValorPagoFormando($codFormatura, $cpf) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array de status que serão calculados
		#################################################################################
		$aStatus					= array ("L","P");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
		$aCat						= array($codCatMensalidade);

		#################################################################################
		## Somar os valores recebidos de contas que possuam a categora de mensalidades
		## calcular os juros e mora separados
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addScalarResult('mensalidade'			, 'mensalidade');
			$rsm->addScalarResult('juros'				, 'juros');
			$rsm->addScalarResult('mora'				, 'mora');
	
			$query 	= $em->createNativeQuery("
					SELECT	SUM(HR.VALOR_RECEBIDO - HR.VALOR_DESCONTO) as mensalidade, SUM(HR.VALOR_JUROS) as juros,SUM(HR.VALOR_MORA) as mora
					FROM 	ZGFIN_CONTA_RECEBER 	CR,
							ZGFIN_PESSOA			P,
							ZGFIN_HISTORICO_REC		HR
					WHERE   CR.CODIGO 				= HR.COD_CONTA_REC
					AND		EXISTS	(
							SELECT	1
							FROM	ZGFIN_CONTA_RECEBER_RATEIO CRR
							WHERE	CRR.COD_CONTA_REC			= CR.CODIGO
							AND		CRR.COD_CATEGORIA			IN (:categoria)
					)
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_STATUS			IN (:status)
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		P.CGC					= :cpf
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatus);
			$query->setParameter('categoria'		,$aCat);
			$query->setParameter('cpf'				,$cpf);
	
			//$log->info("SQL:".$query->getSQL());
			$return		= $query->getOneOrNullResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}

		
		#################################################################################
		## Somar os valores da categora de sistema
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addScalarResult('sistema'						, 'sistema');
		
			$query 	= $em->createNativeQuery("
					SELECT	SUM(CRR.VALOR) as sistema
					FROM 	ZGFIN_CONTA_RECEBER 		CR,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_RECEBER_RATEIO	CRR
					WHERE   CR.CODIGO 				= CRR.COD_CONTA_REC
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		CR.COD_STATUS			= :status
					AND		CRR.COD_CATEGORIA		= :categoria
					AND		P.CGC					= :cpf
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,"L");
			$query->setParameter('categoria'		,$codCatSistema);
			$query->setParameter('cpf'				,$cpf);
		
			$valSistema		= $query->getSingleScalarResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		$return["sistema"]	 	= floatval($valSistema);
		return $return;
		
	
	}
	
	/**
	 * Resgata o valor já pago por Formando
	 * @param unknown $codFormatura
	 */
	public static function getValorPagoPorFormando($codFormatura) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array de status que serão calculados
		#################################################################################
		$aStatus					= array ("L","P");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
		$codCatRifa					= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_RIFA");
		$codCatConvite				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_CONVITE_EXTRA");
		$aCat						= array($codCatMensalidade,$codCatConvite,$codCatRifa,$codCatSistema);
	
		#################################################################################
		## Somar os valores recebidos de contas que possuam a categora de mensalidades
		## calcular os juros e mora separados
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('mensalidade'			, 'mensalidade');
			$rsm->addScalarResult('juros'				, 'juros');
			$rsm->addScalarResult('mora'				, 'mora');
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,SUM(HR.VALOR_RECEBIDO - HR.VALOR_DESCONTO) as mensalidade,SUM(HR.VALOR_JUROS) as juros,SUM(HR.VALOR_MORA) as mora
					FROM 	ZGFIN_CONTA_RECEBER 	CR,
							ZGFIN_PESSOA			P,
							ZGFIN_HISTORICO_REC		HR
					WHERE   CR.CODIGO 				= HR.COD_CONTA_REC
					AND		EXISTS	(
							SELECT	1
							FROM	ZGFIN_CONTA_RECEBER_RATEIO CRR
							WHERE	CRR.COD_CONTA_REC			= CR.CODIGO
							AND		CRR.COD_CATEGORIA			IN (:categoria)
					)
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_STATUS			IN (:status)
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatus);
			$query->setParameter('categoria'		,array($codCatMensalidade));
			$mensalidades		= $query->getResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
		#################################################################################
		## Somar os valores recebidos de contas que possuam a categora de de rifas
		## calcular os juros e mora separados
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('rifas'				, 'rifas');
			$rsm->addScalarResult('juros'				, 'juros');
			$rsm->addScalarResult('mora'				, 'mora');
		
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,
							SUM(HR.VALOR_RECEBIDO - HR.VALOR_DESCONTO) as rifas,
							SUM(HR.VALOR_JUROS) as juros,
							SUM(HR.VALOR_MORA) as mora
					FROM 	ZGFIN_CONTA_RECEBER 	CR,
							ZGFIN_PESSOA			P,
							ZGFIN_HISTORICO_REC		HR
					WHERE   CR.CODIGO 				= HR.COD_CONTA_REC
					AND		EXISTS	(
							SELECT	1
							FROM	ZGFIN_CONTA_RECEBER_RATEIO CRR
							WHERE	CRR.COD_CONTA_REC			= CR.CODIGO
							AND		CRR.COD_CATEGORIA			IN (:categoria)
					)
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_STATUS			IN (:status)
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatus);
			$query->setParameter('categoria'		,array($codCatRifa));
		
			$rifas		= $query->getResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}

		#################################################################################
		## Somar os valores recebidos de contas que possuam a categora de Convites extras
		## calcular os juros e mora separados
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('convites'			, 'convites');
			$rsm->addScalarResult('juros'				, 'juros');
			$rsm->addScalarResult('mora'				, 'mora');
		
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,
							SUM(HR.VALOR_RECEBIDO - HR.VALOR_DESCONTO) as convites,
							SUM(HR.VALOR_JUROS) as juros,
							SUM(HR.VALOR_MORA) as mora
					FROM 	ZGFIN_CONTA_RECEBER 	CR,
							ZGFIN_PESSOA			P,
							ZGFIN_HISTORICO_REC		HR
					WHERE   CR.CODIGO 				= HR.COD_CONTA_REC
					AND		EXISTS	(
							SELECT	1
							FROM	ZGFIN_CONTA_RECEBER_RATEIO CRR
							WHERE	CRR.COD_CONTA_REC			= CR.CODIGO
							AND		CRR.COD_CATEGORIA			IN (:categoria)
					)
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_STATUS			IN (:status)
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatus);
			$query->setParameter('categoria'		,array($codCatConvite));
		
			$convites		= $query->getResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Somar os valores recebidos de contas que possuam a categoria diferente das
		## previstas calcular os juros e mora separados
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('outros'						, 'outros');
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,
							SUM(IF((CRR.COD_CATEGORIA NOT IN (:categorias)),CRR.VALOR,0)) as outros
					FROM 	ZGFIN_CONTA_RECEBER			CR,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_RECEBER_RATEIO	CRR
					WHERE   CR.CODIGO 				= CRR.COD_CONTA_REC
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		CR.COD_STATUS			IN (:status)
					AND		CRR.COD_CATEGORIA		NOT IN (:categorias)
					AND		P.COD_TIPO_PESSOA		= :codTipoPessoa
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,array("L"));
			$query->setParameter('categorias'		,$aCat);
			$query->setParameter('codTipoPessoa'	,'O');
		
			$outros		= $query->getResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Somar os valores da categora de sistema
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('sistema'						, 'sistema');
	
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,SUM(CRR.VALOR) as sistema
					FROM 	ZGFIN_CONTA_RECEBER 		CR,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_RECEBER_RATEIO	CRR
					WHERE   CR.CODIGO 				= CRR.COD_CONTA_REC
					AND	    CR.COD_PESSOA			= P.CODIGO
					AND		CR.COD_ORGANIZACAO		= :codOrganizacao
					AND		CR.COD_STATUS			= :status
					AND		CRR.COD_CATEGORIA		= :categoria
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,"L");
			$query->setParameter('categoria'		,$codCatSistema);
	
			$sistemas	= $query->getResult();
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
		#################################################################################
		## Formatar o resultado combinando os arrays
		#################################################################################
		$result		= array();
		
		for ($i = 0; $i < sizeof($mensalidades); $i++) {
			$cpf							= $mensalidades[$i][0]->getCgc();
			$result[$cpf]["mensalidade"]	= $mensalidades[$i]["mensalidade"];
			$result[$cpf]["juros"]			= $mensalidades[$i]["juros"];
			$result[$cpf]["mora"]			= $mensalidades[$i]["mora"];
		}
	
		for ($i = 0; $i < sizeof($rifas); $i++) {
			$cpf							= $rifas[$i][0]->getCgc();
			if (!isset($result[$cpf]["juros"]))	$result[$cpf]["juros"]	= 0;
			if (!isset($result[$cpf]["mora"]))	$result[$cpf]["mora"]	= 0;
			$result[$cpf]["rifas"]			= $rifas[$i]["rifas"];
			$result[$cpf]["juros"]			+= $rifas[$i]["juros"];
			$result[$cpf]["mora"]			+= $rifas[$i]["mora"];
		}
		
		for ($i = 0; $i < sizeof($convites); $i++) {
			$cpf							= $convites[$i][0]->getCgc();
			if (!isset($result[$cpf]["juros"]))	$result[$cpf]["juros"]	= 0;
			if (!isset($result[$cpf]["mora"]))	$result[$cpf]["mora"]	= 0;
			$result[$cpf]["convites"]		= $convites[$i]["convites"];
			$result[$cpf]["juros"]			+= $convites[$i]["juros"];
			$result[$cpf]["mora"]			+= $convites[$i]["mora"];
		}
		
		for ($i = 0; $i < sizeof($outros); $i++) {
			$cpf							= $outros[$i][0]->getCgc();
			$result[$cpf]["outros"]				= $outros[$i]["outros"];
		}
		
		for ($i = 0; $i < sizeof($sistemas); $i++) {
			$cpf								= $sistemas[$i][0]->getCgc();
			$result[$cpf]["sistema"]			= $sistemas[$i]["sistema"];
		}
		
		return $result;
	}
	


	/**
	 * Resgata o valor devido por formando (inadimplência)
	 * @param unknown $codFormatura
	 */
	public static function getValorInadimplenciaPorFormando($codFormatura) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_MENSALIDADE");
		$aCat						= array($codCatMensalidade);
	
		#################################################################################
		## Data base da inadimplência
		#################################################################################
		$oData					= new \DateTime;
		$hoje					= $oData->format("Y-m-d");
		
		#################################################################################
		## Somar os valores dividos por categora
		#################################################################################
		try {
			$rsm 	= new \Doctrine\ORM\Query\ResultSetMapping();
			$rsm->addEntityResult('\Entidades\ZgfinPessoa'		, 'P');
			$rsm->addFieldResult('P', 'CGC', 'cgc');
			$rsm->addFieldResult('P', 'CODIGO', 'codigo');
			$rsm->addScalarResult('valor'					, 'valor');
			$rsm->addScalarResult('valor_pago'				, 'valor_pago');
			
			$query 	= $em->createNativeQuery("
				SELECT  P.CODIGO,P.CGC,SUM(IFNULL(R.VALOR,0) + IFNULL(R.VALOR_JUROS,0) + IFNULL(R.VALOR_MORA,0) + IFNULL(R.VALOR_OUTROS,0) - IFNULL(R.VALOR_DESCONTO,0) - IFNULL(R.VALOR_CANCELADO,0)) AS valor, SUM(IFNULL(H.VALOR_RECEBIDO,0) + IFNULL(H.VALOR_JUROS,0) + IFNULL(H.VALOR_MORA,0) + IFNULL(H.VALOR_OUTROS,0) - IFNULL(H.VALOR_DESCONTO,0)) as valor_pago
				FROM	ZGFIN_CONTA_RECEBER 		R
				LEFT OUTER JOIN ZGFIN_HISTORICO_REC	H	ON (R.CODIGO		= H.COD_CONTA_REC)
				LEFT JOIN ZGFIN_PESSOA 				P	ON (R.COD_PESSOA	= P.CODIGO)
		        LEFT JOIN ZGFIN_CONTA_STATUS_TIPO	ST	ON (R.COD_STATUS	= ST.CODIGO)
				WHERE	R.COD_ORGANIZACAO			= :codOrg
				AND		R.COD_STATUS				IN ('A','P')
				AND		R.DATA_VENCIMENTO			< :dataVenc
				AND		EXISTS (
		            	SELECT 1
		            	FROM	ZGFIN_CONTA_RECEBER_RATEIO 	RR
		            	WHERE	RR.COD_CONTA_REC			= R.CODIGO
		         		AND		RR.COD_CATEGORIA			IN (:codCat)
		        )
				GROUP BY P.CODIGO,P.CGC
				ORDER	BY 1
			", $rsm);
			$query->setParameter('codOrg'	, $codFormatura);
			$query->setParameter('codCat'	, $aCat);
			$query->setParameter('dataVenc'	, $hoje);
				
			$info		= $query->getResult();
			return ($info);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Resgata os valores devolvidos por Formando
	 * @param unknown $codFormatura
	 */
	public static function getValorDevolvidoPorFormando($codFormatura) {
	
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Array de status que serão calculados
		#################################################################################
		$aStatus					= array ("L");
	
		#################################################################################
		## Array com as categorias que serão usados no calculo
		#################################################################################
		$codCatDevMensalidade			= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_MENSALIDADE");
		$codCatDevSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_SISTEMA");
		$codCatDevOutras				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_DEVOLUCAO_OUTRAS");
		$aCat							= array($codCatDevMensalidade,$codCatDevSistema,$codCatDevOutras);
		
		
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
			$rsm->addScalarResult('outras'						, 'outras');
				
			$query 	= $em->createNativeQuery("
					SELECT	P.CGC,P.CODIGO,
							SUM(IF((CPR.COD_CATEGORIA = :catMensalidade),CPR.VALOR,0)) as mensalidade,
							SUM(IF((CPR.COD_CATEGORIA = :catSistema),CPR.VALOR,0)) as sistema,
							SUM(IF((CPR.COD_CATEGORIA = :catOutras),CPR.VALOR,0)) as outras
					FROM 	ZGFIN_CONTA_PAGAR			CP,
							ZGFIN_PESSOA				P,
							ZGFIN_CONTA_PAGAR_RATEIO	CPR
					WHERE   CP.CODIGO 				= CPR.COD_CONTA_PAG
					AND	    CP.COD_PESSOA			= P.CODIGO
					AND		CP.COD_ORGANIZACAO		= :codOrganizacao
					AND		CP.COD_STATUS			IN (:status)
					AND		CPR.COD_CATEGORIA		IN (:categoria)
					AND		P.COD_TIPO_PESSOA		= :codTipoPessoa
					GROUP	BY P.CGC,P.CODIGO
				", $rsm);
			$query->setParameter('codOrganizacao'	,$codFormatura);
			$query->setParameter('status'			,$aStatus);
			$query->setParameter('catMensalidade'	,$codCatDevMensalidade);
			$query->setParameter('catSistema'		,$codCatDevSistema);
			$query->setParameter('catOutras'		,$codCatDevOutras);
			$query->setParameter('categoria'		,$aCat);
			$query->setParameter('codTipoPessoa'	,'O');
		
			$info		= $query->getResult();
			return ($info);
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}

		
	}
	

	
	/**
	 * Calcular o saldo da Formatura em uma dataBase
	 * @param int $codFormatura
	 */
	public static function calcSaldoFormaturaPorDataBase($codFormatura,$dataBase) {
	
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
		## Resgatar as categorias
		#################################################################################
		$codCatBoleto				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
		$codCatTxAdm				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
		$aCatBolTx					= array($codCatBoleto,$codCatTxAdm);
	
		#################################################################################
		## Criar o objeto da dataBase
		#################################################################################
		$oDataBase					= \DateTime::createFromFormat($system->config["data"]["datetimeFormat"], $dataBase . " 23:59:59");
		$dtBaseMysql				= $oDataBase->format("Y-m-d");
		
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
		$qb2 	= $em->createQueryBuilder();
		$qb3 	= $em->createQueryBuilder();
	
		try {
				
			#################################################################################
			## O Saldo da formatura, consiste em (Valor Arrecadado até a dataBase) - 
			## (Valor Gasto até a dataBase) o Valor arrecadado até a dataBae consisten em:
			## o valor recebido sem o júros / mora +
			## o Percentual de júros / mora configurado para ficar com a formatura -
			## o Percentual dos convites extras configurado para ficar com  o cerimonial -
			## o valor do boleto -
			## o valor de taxa de administração 
			## (Valor Gasto atá dataBase) consiste em: 
			## As saídas (contas a pagar) inferior a dataBase
			#################################################################################
			
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('SUM(hr.valorRecebido + hr.valorOutros - (hr.valorDesconto) ) as total, sum(hr.valorJuros - hr.valorDescontoJuros) as juros, sum(hr.valorMora - hr.valorDescontoMora) as mora')
			->from('\Entidades\ZgfinHistoricoRec','hr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'hr.codContaRec = cr.codigo')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('cr.codOrganizacao'		, ':codOrganizacao'),
				$qb1->expr()->in('cr.codStatus'				, ':status'),
				$qb1->expr()->lte('hr.dataRecebimento'		, ':dataBase')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('dataBase'		, $oDataBase)
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
							 AND		HR1.DATA_RECEBIMENTO		<= :dataBase
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
				$query->setParameter('dataBase'			, $dtBaseMysql);
	
					
				$valorConvite	= \Zage\App\Util::to_float($query->getSingleScalarResult());
				$valorConvCer	= round(($valorConvite * $pctConviteCer) /100,2);
	
					
			} catch (\Exception $e) {
				\Zage\App\Erro::halt($e->getMessage());
			}
	
			#################################################################################
			## Somatório de boleto e taxa de adm
			#################################################################################
			$qb2->select('SUM(crr.valor) valor')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->leftJoin('\Entidades\ZgfinContaReceber', 'cr', \Doctrine\ORM\Query\Expr\Join::WITH, 'crr.codContaRec = cr.codigo')
			->where($qb2->expr()->andx(
				$qb2->expr()->eq('cr.codOrganizacao'	, ':codOrganizacao'),
				$qb2->expr()->in('cr.codStatus'			, ':status'),
				$qb2->expr()->in('crr.codCategoria'		, ':aCatBol'),
				$qb2->expr()->lte('cr.dataLiquidacao'	, ':dataBase')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('status'			, array("L"))
			->setParameter('dataBase'		, $oDataBase)
			->setParameter('aCatBol'		, $aCatBolTx);
	
			$query 			= $qb2->getQuery();
			$valorBolTx		= \Zage\App\Util::to_float($query->getSingleScalarResult());

			#################################################################################
			## Calculo do Valor Arrecadado 
			#################################################################################
			$valorArrecadado	= $valorPrincipal + $valorJuros + $valorMora - $valorConvCer - $valorBolTx;
				
			#################################################################################
			## Calcular o valor gasto agora
			#################################################################################
			#################################################################################
			## Somatório dos Pagamentos
			#################################################################################
			$qb3->select('SUM(hp.valorPago + hp.valorOutros + hp.valorJuros + hp.valorMora - (hp.valorDesconto) ) as total')
			->from('\Entidades\ZgfinHistoricoPag','hp')
			->leftJoin('\Entidades\ZgfinContaPagar', 'cp', \Doctrine\ORM\Query\Expr\Join::WITH, 'hp.codContaPag = cp.codigo')
			->where($qb3->expr()->andx(
				$qb3->expr()->eq('cp.codOrganizacao'		, ':codOrganizacao'),
				$qb3->expr()->in('cp.codStatus'				, ':status'),
				$qb3->expr()->lte('hp.dataPagamento'		, ':dataBase')
			))
			->setParameter('codOrganizacao'	, $codFormatura)
			->setParameter('dataBase'		, $oDataBase)
			->setParameter('status'			, array("L","P"));
			
			$query 				= $qb3->getQuery();
			$pag				= $query->getOneOrNullResult();
			$valorGasto			= \Zage\App\Util::to_float($pag["total"]);
				
			#################################################################################
			## Calcular o Saldo (ValorArrecadado - ValorGasto)
			#################################################################################
			$saldo				= $valorArrecadado - $valorGasto;
					
			
			return ($saldo);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	}
	

	/**
	 * Resgatar o valor do boleto de uma determinada conta
	 * @param integer $codConta
	 */
	function getValorBoletoConta($codConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		
		#################################################################################
		## Resgatar a categoria de Boleto
		#################################################################################
		$codCatBoleto				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
		
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
		
		try {
		
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('sum(crr.valor)')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('crr.codContaRec'		, ':codConta'),
				$qb1->expr()->eq('crr.codCategoria'		, ':codCategoria')
			))
			->setParameter('codConta'		, $codConta)
			->setParameter('codCategoria'	, $codCatBoleto);
		
			$query 				= $qb1->getQuery();
			$valor				= \Zage\App\Util::to_float($query->getSingleScalarResult());
			return ($valor);
					
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
		
	}
	

	/**
	 * Resgatar o valor de sistema de uma determinada conta
	 * @param integer $codConta
	 */
	function getValorSistemaConta($codConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		#################################################################################
		## Resgatar a categoria de Sistema
		#################################################################################
		$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");	
	
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
	
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('sum(crr.valor)')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qb1->expr()->andx(
			$qb1->expr()->eq('crr.codContaRec'		, ':codConta'),
			$qb1->expr()->eq('crr.codCategoria'		, ':codCategoria')
			))
			->setParameter('codConta'		, $codConta)
			->setParameter('codCategoria'	, $codCatSistema);
	
			$query 				= $qb1->getQuery();
			$valor				= \Zage\App\Util::to_float($query->getSingleScalarResult());
			return ($valor);
				
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	

	/**
	 * Resgatar o valor de sistema de uma determinada conta
	 * @param integer $codConta
	 */
	function getValorTaxaAdmConta($codConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Resgatar a categoria de taxa de administração
		#################################################################################
		$codCatTxAdm				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
	
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
	
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('sum(crr.valor)')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qb1->expr()->andx(
			$qb1->expr()->eq('crr.codContaRec'		, ':codConta'),
			$qb1->expr()->eq('crr.codCategoria'		, ':codCategoria')
			))
			->setParameter('codConta'		, $codConta)
			->setParameter('codCategoria'	, $codCatTxAdm);
	
			$query 				= $qb1->getQuery();
			$valor				= \Zage\App\Util::to_float($query->getSingleScalarResult());
			return ($valor);
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	
	/**
	 * Resgatar o valor não liíquido de uma determinada conta
	 *	Os valores não líquidos são eles:
	 *  Boleto, Sistema e Taxa de Administração
	 * 
	 * @param integer $codConta
	 */
	function getValorNaoLiquidoConta($codConta) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		#################################################################################
		## Calcular o valor não líquido de uma conta
		## Os valores não líquidos são eles:
		## Boleto, Sistema e Taxa de Administração
		#################################################################################
		
		
		#################################################################################
		## Resgatar as categorias de sistema, boleto e taxa de administração
		#################################################################################
		$codCatTxAdm				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_OUTRAS_TAXAS");
		//$codCatSistema				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_USO_SISTEMA");
		$codCatBoleto				= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
		$aCat						= array($codCatBoleto,$codCatTxAdm);
	
		#################################################################################
		## Criar os objetos do Query builde, um para cada consulta
		#################################################################################
		$qb1 	= $em->createQueryBuilder();
	
		try {
	
			#################################################################################
			## Somatório dos recebimentos
			#################################################################################
			$qb1->select('sum(crr.valor)')
			->from('\Entidades\ZgfinContaReceberRateio','crr')
			->where($qb1->expr()->andx(
				$qb1->expr()->eq('crr.codContaRec'		, ':codConta'),
				$qb1->expr()->in('crr.codCategoria'		, ':codCategoria')
			))
			->setParameter('codConta'		, $codConta)
			->setParameter('codCategoria'	, $aCat);
	
			$query 				= $qb1->getQuery();
			$valor				= \Zage\App\Util::to_float($query->getSingleScalarResult());
			return ($valor);
	
		} catch (\Exception $e) {
			\Zage\App\Erro::halt($e->getMessage());
		}
	
	}
	
	
	/**
	 * Remover o registro de rateio de boleto
	 * @param int $codConta
	 */
	public static function excluiRateioBoleto($codConta) {
		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
		
		try {
			
			#################################################################################
			## Resgatar a categorias de boleto
			#################################################################################
			$codCatBoleto	= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");
			
			#################################################################################
			## Localizar o rateio do boleto na conta
			#################################################################################
			$ratBoleto		= $em->getRepository('Entidades\ZgfinContaReceberRateio')->findOneBy(array('codContaRec' => $codConta,'codCategoria' => $codCatBoleto)); 
			
			if ($ratBoleto)	{
				$em->remove($ratBoleto);
			}
		
			$em->flush();
			
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	
		return null;
	}
	
	/**
	 * Incluir um registro de rateio de boleto
	 * @param int $codConta
	 * @param floar $valor
	 */
	public static function criaRateioBoleto($codConta,$valor) {

		#################################################################################
		## Variáveis globais
		#################################################################################
		global $em,$system,$log;
	
		try {
				
			#################################################################################
			## Resgatar a categorias de boleto
			#################################################################################
			$codCatBoleto	= \Zage\Adm\Parametro::getValorSistema("APP_COD_CAT_BOLETO");

			#################################################################################
			## Resgatar os objetos do doctrine
			#################################################################################
			$oCatBol		= $em->getRepository('Entidades\ZgfinCategoria')->findOneBy(array('codigo' => $codCatBoleto)); 
			$oConta			= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codigo' => $codConta));
				
			
			#################################################################################
			## Verificar se a conta e a categoria existem
			#################################################################################
			if (!$oConta)	throw new \Exception("Conta para inclusão de rateio não encontrada em ".__FUNCTION__);	
			if (!$oCatBol)	throw new \Exception("Categoria de boleto para inclusão de rateio não encontrada em ".__FUNCTION__);
			
			#################################################################################
			## Localizar o rateio do boleto na conta
			#################################################################################
			$ratBoleto		= new \Entidades\ZgfinContaReceberRateio();
			$ratBoleto->setCodCategoria($oCatBol);
			$ratBoleto->setCodCentroCusto(null);
			$ratBoleto->setCodContaRec($oConta);
			$ratBoleto->setValor($valor);
			$ratBoleto->setPctValor(0);
			$em->persist($ratBoleto);
	
			$em->flush();
				
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	
		return null;
	}
	

}


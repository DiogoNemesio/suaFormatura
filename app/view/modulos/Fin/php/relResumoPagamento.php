<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$log;

#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\App\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\App\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\App\Util::antiInjection($id);
}else{
	\Zage\App\Erro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);


#################################################################################
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['mesRef'])) 		$mesRef			= \Zage\App\Util::antiInjection($_POST['mesRef']);
if (isset($_POST['geraPdf'])) 		$geraPdf		= \Zage\App\Util::antiInjection($_POST['geraPdf']);

#################################################################################
## Resgata as informações do Relatório
#################################################################################
$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $_codMenu_));
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
if (!$oOrgFmt)	\Zage\App\Erro::halt("Organização não é uma formatura");

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio();

#################################################################################
## Criação do cabeçalho
#################################################################################
$rel->adicionaCabecalho($info->getNome());

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Montar o array de meses
#################################################################################
if (!isset($mesRef) || !$mesRef) $mesRef = date('m/Y');
list ($mes, $ano) = split ('[/.-]', $mesRef);
$_mesIni			= date("m",mktime(0, 0, 0, $mes - 12, 1 , $ano));
$_anoIni			= date("Y",mktime(0, 0, 0, $mes - 12, 1 , $ano));
$mesIni				= date("m/Y",mktime(0, 0, 0, $_mesIni, 1 , $_anoIni));

date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');
$log->info("MesRef: ".$mesRef." MesIni: ".$mesIni);

$dtVencIni	= \DateTime::createFromFormat("m/Y", $mesIni);
$dtVencFim	= \DateTime::createFromFormat("m/Y", $mesRef);


#################################################################################
## Resgata a categoria de mensalidades
#################################################################################
$catMen		= \Zage\Adm\Parametro::getValorSistema('APP_COD_CAT_MENSALIDADE');


#################################################################################
## Resgata os dados do relatório
#################################################################################

//$qb1 	= $em->createQueryBuilder();
//$qb2 	= $em->createQueryBuilder();

try {

	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$rsm->addEntityResult('Entidades\ZgfinPessoa', 'p');
	$rsm->addFieldResult('p', 'CODIGO', 'codigo');
	$rsm->addFieldResult('p', 'NOME', 'nome');
	$rsm->addScalarResult('MES_REF', 'MES_REF');
	$rsm->addScalarResult('VALOR', 'VALOR');
	
	$query 	= $em->createNativeQuery("
		SELECT  P.CODIGO AS CODIGO,P.NOME AS NOME,DATE_FORMAT(R.DATA_VENCIMENTO,'%Y%m') AS MES_REF,SUM(RR.VALOR) AS VALOR
		FROM	ZGFIN_CONTA_RECEBER 		R,
				ZGFIN_CONTA_RECEBER_RATEIO 	RR,
		       	ZGFIN_PESSOA				P,
		        ZGFIN_CONTA_STATUS_TIPO		ST
		WHERE	R.CODIGO					= RR.COD_CONTA_REC
		AND		R.COD_PESSOA				= P.CODIGO
		AND		R.COD_STATUS				= ST.CODIGO
		AND		R.COD_ORGANIZACAO			= ?
		AND		R.COD_STATUS				IN ('A','P','L')
		AND		RR.COD_CATEGORIA			= ?
		GROUP	BY P.CODIGO,P.NOME,DATE_FORMAT(R.DATA_VENCIMENTO,'%Y%m')
	", $rsm);
	$query->setParameter(1	, $system->getCodOrganizacao());
	$query->setParameter(2	, $catMen);
	
	$contas = $query->getResult();
	
	
	/*$qb1->select("p.codigo AS CODIGO_PESSOA,p.nome AS NOME_PESSOA,DATE_FORMAT(r.dataVencimento,'%Y%m') AS MES_REF,SUM(cr.Valor) as VALOR")
	->from('\Entidades\ZgfinContaReceberRateio'	,'cr')
	->leftJoin('\Entidades\ZgfinContaReceber'	,'r',	\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codContaRec 		= r.codigo')
	->leftJoin('\Entidades\ZgfinPessoa'			,'pe',	\Doctrine\ORM\Query\Expr\Join::WITH, 'r.codPessoa 			= pe.codigo')
	->leftJoin('\Entidades\ZgfinContaStatusTipo','st',	\Doctrine\ORM\Query\Expr\Join::WITH, 'r.codStatus 			= st.codigo')
	->where($qb1->expr()->andX(
		$qb1->expr()->eq('r.codOrganizacao'	, ':codOrganizacao'),
		$qb1->expr()->in('r.codStatus'		, ':aStatus'),
		$qb1->expr()->in('cr.codCategoria'	, ':aCat')
	))
	->addGroupBy("p.codigo,p.nome,DATE_FORMAT(r.dataVencimento,'%Y%m')")
	->orderBy('r.dataVencimento','ASC')
	->addOrderBy('r.codigo','ASC')
	->setParameter('codOrganizacao'		, $system->getCodOrganizacao())
	->setParameter('aStatus'			, array('A','P','L'))
	->setParameter('aCat'				, array($catMen));
	
	
	$query1		= $qb1->getQuery();
	$contas		= $query1->getResult();
	*/

} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Iniciar os totalizadores
#################################################################################
$valTotal		= 0;


#################################################################################
## Montar o array de meses
#################################################################################
$_mes		=	$_mesIni;
$_ano		=	$_anoIni;
$aMeses		= array();
$aHtml		= array();
for ($i = 0; $i < 12; $i++) {
	$_mesAtual			= (int) date("m",mktime(0, 0, 0, $_mes + $i, 1 , $_ano));
	$_anoAtual			= (int) date("Y",mktime(0, 0, 0, $_mes + $i, 1 , $_ano));
	$mesDesc			= gmstrftime("%b-%y",mktime(0, 0, 0, $_mesAtual, 1 , $_anoAtual));
	$mesIndex			= gmstrftime("%Y%m",mktime(0, 0, 0, $_mesAtual, 1 , $_anoAtual));
	$aMeses[]			= $mesDesc;
	$aHtml[$mesIndex]	= '<td style="text-align: right;">%MES_'.$i.'%</td>';
}


if (sizeof($contas) > 0) {
	$table	= '<table class="table table-condensed">';
	$table .= '<thead>
				<tr><th style="text-align: center;" colspan="6"><h6>'.$oOrg->getNome().' - '.$oOrgFmt->getDataConclusao()->format('Y').'</h6></th></tr>
				<tr>
					<th style="text-align: left;"><strong>FORMANDO</strong></th>
					<th style="text-align: center;"><strong>MESES ANTERIORES</strong></th>
					';
	$_mes		=	$_mesIni;
	$_ano		=	$_anoIni;
	foreach ($aMeses as $mesAtual) {
		$table .= '<th style="text-align: center;"><strong>'.$mesAtual.'</strong></th>';
	}
	$table .='		<th style="text-align: right;"><strong>TOTAL PAGO</strong></th>
					<th style="text-align: right;"><strong>A PAGAR</strong></th>
				</tr>
				</thead><tbody>';

	foreach ($contas as $lanc) {
		#################################################################################
		## Formatar os dados
		#################################################################################
		$venc	= ($lanc["DATA_VENCIMENTO"] != null) ? $lanc["DATA_VENCIMENTO"]->format($system->config["data"]["dateFormat"]) : null;
		$valor	= \Zage\App\Util::to_money($lanc["VALOR"]);
		$parc	= $lanc["PARCELA"].'/'.$lanc["NUM_PARCELAS"];
		
		$table .= '<tr>
			<td style="text-align: left;">'.$lanc["PESSOA_NOME"].'</td>
			<td style="text-align: center;">'.$parc.'</td>
			<td style="text-align: center;">'.$lanc["STATUS_DESCRICAO"].'</td>
			<td style="text-align: left;">'.$lanc["PESSOA_NOME"].'</td>
			<td style="text-align: right;">'.$venc.'</td>
			<td style="text-align: right;">'.$valor.'</td>
			</tr>';
		
		#################################################################################
		## Atualizar os totalizadores
		#################################################################################
		$valTotal		+= \Zage\App\Util::to_float($lanc["VALOR"]);
		
	}
	$table .= '</tbody><tfoot><tr><th style="text-align: right;" colspan="5"><strong>Total dos débitos:</strong></th><th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valDeb).'</strong></th></tfoot><tbody>';
	$table	.= "</tbody>";

	$table .= '<thead>
				<tr><th style="text-align: center;" colspan="6"><h6>CRÉDITOS</h6></th></tr>
				<tr>
					<th style="text-align: left;"><strong>DESCRIÇÃO</strong></th>
					<th style="text-align: center;"><strong>PARCELA</strong></th>
					<th style="text-align: center;"><strong>STATUS</strong></th>
					<th style="text-align: left;"><strong>CLIENTE</strong></th>
					<th style="text-align: right;"><strong>VENCIMENTO</strong></th>
					<th style="text-align: right;"><strong>VALOR</strong></th>
				</tr>
				</thead><tbody>';
	
}else{
	$table	= "<center>nenhuma informação encontrada !!!</center>";
}


$html	= '
<body class="no-skin">
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';

echo $html;


//$rel->WriteHTML($html);
//$rel->Output();

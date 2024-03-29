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
if (isset($_POST['dataVencIni'])) 		$dataVencIni		= \Zage\App\Util::antiInjection($_POST['dataVencIni']);
if (isset($_POST['dataVencFim'])) 		$dataVencFim		= \Zage\App\Util::antiInjection($_POST['dataVencFim']);
if (isset($_POST['dataEmisIni'])) 		$dataEmisIni		= \Zage\App\Util::antiInjection($_POST['dataEmisIni']);
if (isset($_POST['dataEmisFim'])) 		$dataEmisFim		= \Zage\App\Util::antiInjection($_POST['dataEmisFim']);
if (isset($_POST['descricao'])) 		$descricao			= \Zage\App\Util::antiInjection($_POST['descricao']);
if (isset($_POST['valorIni'])) 			$valorIni			= \Zage\App\Util::antiInjection($_POST['valorIni']);
if (isset($_POST['valorFim'])) 			$valorFim			= \Zage\App\Util::antiInjection($_POST['valorFim']);
if (isset($_POST['fornecedor'])) 		$fornecedor			= \Zage\App\Util::antiInjection($_POST['fornecedor']);

#################################################################################
## Resgata os parâmetros (ARRAYS) passados pelo formulario
#################################################################################
if (isset($_POST['codFormaPag'])) 		$codFormaPag		= $_POST['codFormaPag'];
if (isset($_POST['codCategoria'])) 		$codCategoria		= $_POST['codCategoria'];
if (isset($_POST['codCentroCusto'])) 	$codCentroCusto		= $_POST['codCentroCusto'];
if (isset($_POST['codConta'])) 			$codConta			= $_POST['codConta'];
if (isset($_POST['codStatus'])) 		$codStatus			= $_POST['codStatus'];


if (!isset($codFormaPag))				$codFormaPag		= array();
if (!isset($codCategoria))				$codCategoria		= array();
if (!isset($codCentroCusto))			$codCentroCusto		= array();
if (!isset($codConta))					$codConta			= array();
if (!isset($codStatus))					$codStatus			= array();


#################################################################################
## Resgata as informações do Relatório
#################################################################################
$info			= $em->getRepository('Entidades\ZgappMenu')->findOneBy(array('codigo' => $_codMenu_));


#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio();


#################################################################################
## Criação dos filtros
#################################################################################
if (!empty($dataVencIni)	|| (!empty($dataVencFim)))		$rel->adicionaFiltro("Data Vencimento",$dataVencIni . " a ".$dataVencFim);
if (!empty($dataEmisIni)	|| (!empty($dataEmisFim)))		$rel->adicionaFiltro("Data Emissão",$dataEmisIni . " a ".$dataEmisFim);
if (!empty($valorIni)		|| (!empty($valorFim)))			$rel->adicionaFiltro("Valor",\Zage\App\Util::to_money($valorIni) . " a ".\Zage\App\Util::to_money($valorFim));

if (!empty($descricao))			$rel->adicionaFiltro("Descrição",$descricao);
if (!empty($fornecedor))		$rel->adicionaFiltro("Fornecedor",$fornecedor);

$countForma			= sizeof($codFormaPag);
$countCat			= sizeof($codCategoria);
$countCentro		= sizeof($codCentroCusto);
$countConta			= sizeof($codConta);
$countStatus		= sizeof($codStatus);


if ($countForma > 0)	{
	if ($countForma > 2) {
		$textFormaPag	= "$countForma Selecionadas";
	}else{
		$textFormaPag	= implode(",", $codFormaPag);
	}
	
	$rel->adicionaFiltro("Forma Pag",$textFormaPag);
}

if ($countCat > 0)	{
	if ($countCat > 2) {
		$textoCentro	= "$countCat Selecionadas";
	}else{
		$textoCentro	= implode(",", $codCategoria);;
	}

	$rel->adicionaFiltro("Categoria",$textoCentro);
}

if ($countCentro > 0)	{
	if ($countCentro > 2) {
		$textoCentro	= "$countCentro Selecionadas";
	}else{
		$textoCentro	= implode(",", $codCentroCusto);
	}

	$rel->adicionaFiltro("Centro Custo",$textoCentro);
}

if ($countConta > 0)	{
	if ($countConta > 2) {
		$textoConta	= "$countConta Selecionadas";
	}else{
		$textoConta	= implode(",", $codConta);
	}

	$rel->adicionaFiltro("Conta",$textoConta);
}

if ($countStatus > 0)	{
	if ($countStatus > 2) {
		$textoStatus	= "$countStatus Selecionados";
	}else{
		$textoStatus	= implode(",", $codStatus);
	}

	$rel->adicionaFiltro("Status",$textoStatus);
}



#################################################################################
## Criação do cabeçalho
#################################################################################
$rel->adicionaCabecalho($info->getNome());

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();


#################################################################################
## Formata as datas
#################################################################################
if (!empty($dataVencIni)) $dtVencIni	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataVencIni);
if (!empty($dataVencFim)) $dtVencFim	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataVencFim);
if (!empty($dataEmisIni)) $dtEmisIni	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataEmisIni);
if (!empty($dataEmisFim)) $dtEmisFim	= \DateTime::createFromFormat($system->config["data"]["dateFormat"], $dataEmisFim);

#################################################################################
## Resgata os dados do relatório
#################################################################################
$qb1 	= $em->createQueryBuilder();
$qb2 	= $em->createQueryBuilder();

try {

	$qb2->select('st.codigo as COD_STATUS, st.descricao AS STATUS_DESCRICAO, p.descricao AS DESCRICAO, pe.codigo AS COD_PESSOA,pe.nome as PESSOA_NOME,p.parcela AS PARCELA,p.numParcelas AS NUM_PARCELAS,cr.valor AS VALOR,p.dataVencimento AS DATA_VENCIMENTO')
	->from('\Entidades\ZgfinContaReceberRateio'	,'cr')
	->leftJoin('\Entidades\ZgfinContaReceber'	,'p',	\Doctrine\ORM\Query\Expr\Join::WITH, 'cr.codContaRec 		= p.codigo')
	->leftJoin('\Entidades\ZgfinPessoa'			,'pe',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codPessoa 			= pe.codigo')
	->leftJoin('\Entidades\ZgfinContaStatusTipo','st',	\Doctrine\ORM\Query\Expr\Join::WITH, 'p.codStatus 			= st.codigo')
	->where($qb2->expr()->andX(
		$qb2->expr()->eq('p.codOrganizacao'	, ':codOrganizacao')
	))
	->orderBy('p.dataVencimento','ASC')
	->addOrderBy('p.codigo','ASC')
	->setParameter('codOrganizacao', $system->getCodOrganizacao());
	
	
	if (!empty($valorIni)) {
		$qb2->andWhere($qb2->expr()->gte("p.valor", ':valorIni'));
		$qb2->setParameter('valorIni', \Zage\App\Util::to_float($valorIni));
	}
	
	if (!empty($valorFim)) {
		$qb2->andWhere($qb2->expr()->lte("p.valor", ':valorFim'));
		$qb2->setParameter('valorFim', \Zage\App\Util::to_float($valorFim));
	}
			
	if (!empty($dataVencIni)) {
		$qb2->andWhere($qb2->expr()->gte('p.dataVencimento', ':dataVencIni'));
		$qb2->setParameter('dataVencIni', $dtVencIni, \Doctrine\DBAL\Types\Type::DATE);
	}
		
	if (!empty($dataVencFim)) {
		$qb2->andWhere($qb2->expr()->lte('p.dataVencimento', ':dataVencFim'));
		$qb2->setParameter('dataVencFim', $dtVencFim, \Doctrine\DBAL\Types\Type::DATE);
	}
	
	if (!empty($dataEmisIni)) {
		$qb2->andWhere($qb2->expr()->gte('p.dataEmissao', ':dataEmisIni'));
		$qb2->setParameter('dataEmisIni', $dtVencIni, \Doctrine\DBAL\Types\Type::DATE);
	}
	
	if (!empty($dataEmisFim)) {
		$qb2->andWhere($qb2->expr()->lte('p.dataEmissao', ':dataEmisFim'));
		$qb2->setParameter('dataEmisFim', $dtEmisFim, \Doctrine\DBAL\Types\Type::DATE);
	}
	
	if (!empty($codStatus)) {
		$qb2->andWhere($qb2->expr()->in('p.codStatus'	, ':aStatus'));
		$qb2->setParameter('aStatus', $codStatus);
	}
	
	if (!empty($codCentroCusto)) {
		$qb2->andWhere($qb2->expr()->in('cr.codCentroCusto'	, ':aCentroCusto'));
		$qb2->setParameter('aCentroCusto', $codCentroCusto);
	}
	
	if (!empty($codCategoria)) {
		$qb2->andWhere($qb2->expr()->in('cr.codCategoria'	, ':aCat'));
		$qb2->setParameter('aCat', $codCategoria);
	}
	
	if (!empty($codFormaPag)) {
		$qb2->andWhere($qb2->expr()->in('p.codFormaPagamento'	, ':aForma'));
		$qb2->setParameter('aForma', $codFormaPag);
	}
	
	if (!empty($codConta)) {
		$qb2->andWhere($qb2->expr()->in('p.codConta'	, ':aConta'));
		$qb2->setParameter('aConta', $codConta);
	}
	
	if (!empty($descricao)) {
		$qb2->andWhere($qb2->expr()->like($qb2->expr()->upper('p.descricao')	, ':descricao'));
		$qb2->setParameter('descricao', strtoupper('%'.$descricao.'%'));
	}
	
	if (!empty($fornecedor)) {
		$qb2->andWhere($qb2->expr()->like($qb2->expr()->upper('pe.nome')	, ':fornecedor'));
		$qb2->setParameter('fornecedor', strtoupper('%'.$fornecedor.'%'));
	}
	
	$query2		= $qb2->getQuery();
	$creditos	= $query2->getResult();
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Iniciar os totalizadores
#################################################################################
$valTotal		= 0;

	
if (sizeof($creditos) > 0) {
	$table	= '<table class="table table-condensed">';
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
	
	foreach ($creditos as $lanc) {
		#################################################################################
		## Formatar os dados
		#################################################################################
		$venc	= ($lanc["DATA_VENCIMENTO"] != null) ? $lanc["DATA_VENCIMENTO"]->format($system->config["data"]["dateFormat"]) : null;
		$valor	= \Zage\App\Util::to_money($lanc["VALOR"]);
		$parc	= $lanc["PARCELA"].'/'.$lanc["NUM_PARCELAS"];
		
		$table .= '<tr>
			<td style="text-align: left;">'.$lanc["DESCRICAO"].'</td>
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
	$table .= '</tbody><tfoot><tr><th style="text-align: right;" colspan="5"><strong>Total:</strong></th><th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valTotal).'</strong></th>';
	$table	.= "</tfoot></table>";
	
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


$rel->WriteHTML($html);
$rel->Output();

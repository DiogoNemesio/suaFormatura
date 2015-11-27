<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$_user;


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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

try {
	$rsm 	= new Doctrine\ORM\Query\ResultSetMapping();
	$rsm->addScalarResult('COD_FORMANDO'		, 'COD_FORMANDO');
	$rsm->addScalarResult('NOME'				, 'NOME');
	$rsm->addScalarResult('VALOR_TOTAL'			, 'VALOR_TOTAL');
	$rsm->addScalarResult('COD_TIPO_EVENTO'		, 'COD_TIPO_EVENTO');
	$rsm->addScalarResult('QTDE'				, 'QTDE');
	$rsm->addScalarResult('VALOR_UNITARIO'		, 'VALOR_UNITARIO');
	$rsm->addScalarResult('DESCRICAO'			, 'DESCRICAO');
	$rsm->addScalarResult('TAXA_COVENIENCIA'	, 'TAXA_CONVENIENCIA');
	
	$query 	= $em->createNativeQuery("
		SELECT V.COD_FORMANDO, P.NOME, SUM(V.VALOR_TOTAL) VALOR_TOTAL, E.COD_TIPO_EVENTO, T.DESCRICAO, V.TAXA_CONVENIENCIA, SUM(I.QUANTIDADE) QTDE, I.VALOR_UNITARIO 
			FROM `ZGFMT_CONVITE_EXTRA_VENDA` V
			LEFT OUTER JOIN `ZGFIN_PESSOA` P ON (V.COD_FORMANDO = P.CODIGO) 
			LEFT OUTER JOIN `ZGFIN_CONTA_RECEBER` C ON (V.COD_TRANSACAO = C.COD_TRANSACAO) 
			LEFT OUTER JOIN `ZGFMT_CONVITE_EXTRA_VENDA_ITEM` I ON (V.CODIGO = I.COD_VENDA)
			LEFT OUTER JOIN `ZGFMT_EVENTO` E ON (I.COD_EVENTO = E.CODIGO) 
			LEFT OUTER JOIN `ZGFMT_EVENTO_TIPO` T ON (E.COD_TIPO_EVENTO = T.CODIGO) 
			WHERE C.COD_STATUS = :codStatus AND  C.COD_ORGANIZACAO = :codOrg
			GROUP BY E.COD_TIPO_EVENTO, V.COD_FORMANDO", $rsm);
	$query->setParameter('codOrg'		, $system->getCodOrganizacao());
	$query->setParameter('codStatus'	, "L");
	//$query->setParameter('dataVenc'	, $dtVenc);
	
	
	$vendas = $query->getResult();
	$log->info($vendas);
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$dadosRes		= array();
for ($i = 0; $i < sizeof($vendas); $i++) {

	if (!isset($dadosRes[$vendas[$i]["COD_FORMANDO"]])) {
		$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"]	= array();
		$dadosRes[$vendas[$i]["COD_FORMANDO"]]["NOME"]		= $vendas[$i]["NOME"];
	}

	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["DESCRICAO"]		= $vendas[$i]["DESCRICAO"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["QUANTIDADE"]		= $vendas[$i]["QUANTIDADE"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["VALOR_UNITARIO"]	= $vendas[$i]["VALOR_UNITARIO"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["QTDE"]			= $vendas[$i]["QTDE"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["TAXA"]			= $vendas[$i]["TAXA_CONVENIENCIA"];
	$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VENDAS"][$vendas[$i]["COD_TIPO_EVENTO"]]["VALOR_TOTAL"]		= $vendas[$i]["VALOR_TOTAL"];
	//$dadosRes[$vendas[$i]["COD_FORMANDO"]]["VALOR_APAGAR"]										+= ($vendas[$i]["VALOR"] - $vendas[$i]["VALOR_PAGO"]);
	$valTotal																							+= $vendas[$i]["VALOR_TOTAL"];

}

$table	= '<table class="table table-condensed">';
$table .= '<thead>
				<tr><th style="text-align: center;" colspan="6"><h4>TURMA: </h4></th></tr>
				</thead><tbody>';

foreach ($dadosRes as $dados) {
	$table .= '<tr style="background-color:#EEEEEE">
						<th style="text-align: left; width: 10%;"><strong>FORMANDO:&nbsp;'.$dados["NOME"].'</strong></th>
						<th style="text-align: left; width: 20%;"><strong>EVENTO</strong></th>
						<th style="text-align: center; width: 10%;"><strong>QUANTIDADE</strong></th>
						<th style="text-align: center; width: 10%;"><strong>VALOR UNITARIO</strong></th>
						<th style="text-align: center; width: 10%;"><strong>TAXA</strong></th>
						<th style="text-align: right; width: 10%;"><strong>VALOR TOTAL</strong></th>
					</tr>';
	//foreach ($dados["COD_TIPO_EVENTO"] as $codConta => $info) {
	foreach ($dados["VENDAS"] as $info) {
		$table .= '<tr>
					<th style="text-align: left; width: 10%;">&nbsp;</th>
					<th style="text-align: left; width: 20%;">'.$info["DESCRICAO"].'</th>
					<th style="text-align: center; width: 10%;">'.$info["QTDE"].'</th>
					<th style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($info["VALOR_UNITARIO"]).'</th>
					<th style="text-align: center; width: 10%;">'.\Zage\App\Util::to_money($info["TAXA"]).'</th>
					<th style="text-align: right; width: 10%;">'.\Zage\App\Util::to_money($info["VALOR_TOTAL"]).'</th>
					</tr>';
		
	}
}
$table .= '<tr style="background-color:#FFFFFF">
				<th style="text-align: right;" colspan="5"><strong>TOTAL GERAL:&nbsp;</strong></th>
				<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($valTotal).'</strong></th>
				</tr>
			</tbody>
			</table>';

$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';

echo $htmlTable;
#################################################################################
## Gerar a url de histórico de pagamentos
#################################################################################
$urlVoltar				= ROOT_URL."/Fmt/conviteExtraTransf.php?id=".$id;

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('IC'				,$_icone_);
$tpl->set('ID'				,$id);
$tpl->set('URL_VOLTAR'		,$urlVoltar);
$tpl->set('TABLE'			,$table);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
global $em,$tr,$system;

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
	\Zage\App\Erro::halt('FALTA PARÂMENTRO : ID');
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

#################################################################################
## Resgata informações da rifa
#################################################################################
if (!isset($codRifa)) \Zage\App\Erro::halt('FALTA PARÂMENTRO : COD_RIFA');

$infoRifa 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));

if (!$infoRifa)	\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$formandos		= \Zage\Fmt\Rifa::listaNumRifasPorFormando($system->getCodOrganizacao(),$codRifa);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Resgata as informações do Relatório
#################################################################################
$oOrg			= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));
$oOrgFmt		= $em->getRepository('Entidades\ZgfmtOrganizacaoFormatura')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao()));
if (!$oOrgFmt)	\Zage\App\Erro::halt("Organização não é uma formatura");

#################################################################################
## Criar o relatório
#################################################################################
$rel	= new \Zage\App\Relatorio(''	,'A4-L',10,'',15,15,16,16,9,9,'L');

#################################################################################
## Criação do cabeçalho
#################################################################################
$rel->adicionaCabecalho("Resumo financeiro de Rifa");
$rel->NaoExibeFiltrosNulo();

#################################################################################
## Criação do Rodapé
#################################################################################
$rel->adicionaRodape();

#################################################################################
## Ajustar o timezone
#################################################################################
date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');

#################################################################################
## Formatar os dados do relatório
#################################################################################
$tTotal			= 0;
$tRecebido		= 0;
$tVendido		= 0;
$tAreceber		= 0;
$tQtdeGerada	= 0;
$tQtdeVendida	= 0;
$dadosRel		= array();
for ($i = 0; $i < sizeof($formandos); $i++) {
	
	#################################################################################
	## Resgatar a quantidade vendida
	#################################################################################
	if ($infoRifa->getIndRifaEletronica() != 1) {
		$infoVenda		= $em->getRepository('Entidades\ZgfmtRifaFormando')->findOneBy(array('codRifa' => $codRifa, 'codFormando' => $formandos[$i]["CODIGO"]));
		if ($infoVenda)	{
			$qtdeVendida		= $infoVenda->getQtdeVendida();
		}else{
			$qtdeVendida 		= $formandos[$i]["NUM"];
		}
	}else{
		$qtdeVendida 			= $formandos[$i]["NUM"];
	}
	
	#################################################################################
	## Calcula o valor total que o formando deve pagar
	#################################################################################
	if ($qtdeVendida >= $infoRifa->getQtdeObrigatorio()){
		$total = $qtdeVendida * $infoRifa->getValorUnitario();
	}else{
		$total = $infoRifa->getQtdeObrigatorio() * $infoRifa->getValorUnitario();
	}
	
	#################################################################################
	## Calcula o valor que o formando conseguiu arrecadar
	#################################################################################
	$valorVendido			= $qtdeVendida * $infoRifa->getValorUnitario();
	
	#################################################################################
	## Grupo de Associação da rifa com a conta
	#################################################################################
	$codGrpAssociacao	= "RIFA_".$infoRifa->getCodigo(). "_".$formandos[$i]["CODIGO"];
	
	#################################################################################
	## Verificar se a conta já foi gerada
	#################################################################################
	$oConta				= $em->getRepository('Entidades\ZgfinContaReceber')->findOneBy(array('codOrganizacao' => $system->getCodOrganizacao(),'codGrupoAssociacao' => $codGrpAssociacao));
	
	if (!$oConta)		{
		$totalPago		= 0;
		$valAPagar		= $total;
	}else{
		$totalPago		= \Zage\Fin\ContaReceber::getValorJaRecebido($oConta->getCodigo());
		$valAPagar		= $total - $totalPago;
	}
	
	#################################################################################
	## Totalizadores
	#################################################################################
	$tTotal			+= $total;
	$tRecebido		+= $totalPago;
	$tVendido		+= ($qtdeVendida * $infoRifa->getValorUnitario());
	$tAreceber		+= $valAPagar;	
	$tQtdeGerada	+= $formandos[$i]["NUM"];
	$tQtdeVendida	+= $qtdeVendida;
	
	$dadosRel[$formandos[$i]["CODIGO"]]["NOME"]				= $formandos[$i]["NOME"];
	$dadosRel[$formandos[$i]["CODIGO"]]["VAL_TOTAL"]		= $total;
	$dadosRel[$formandos[$i]["CODIGO"]]["VAL_PAGO"]			= $totalPago;
	$dadosRel[$formandos[$i]["CODIGO"]]["VAL_APAGAR"]		= $valAPagar;
	$dadosRel[$formandos[$i]["CODIGO"]]["VAL_VENDIDO"]		= $qtdeVendida * $infoRifa->getValorUnitario();
	$dadosRel[$formandos[$i]["CODIGO"]]["QTDE_GERADA"]		= $formandos[$i]["NUM"];
	$dadosRel[$formandos[$i]["CODIGO"]]["QTDE_VENDIDA"]		= $qtdeVendida;
	
}

if (sizeof($dadosRel) > 0) {
	
	#################################################################################
	## Não colocar os tamanhos do campo caso não seja para gerar o PDF
	#################################################################################
	$w1			= "width: 30%;";
	$w2			= "width: 10%;";
	$w3			= "width: 10%;";
	$w4			= "width: 10%;";
	$w5			= "width: 10%;";
	$w6			= "width: 10%;";
	$w7			= "width: 10%;";
	
	$table	= '<table class="table table-condensed">';
	$table .= '<thead>
				<tr><th style="text-align: center;" colspan="7"><h4>TURMA: '.$oOrg->getNome().' - '.$oOrgFmt->getDataConclusao()->format('Y').'</h4></th></tr>
				<tr><th style="text-align: center;" colspan="7"><h6>RIFA: '.$infoRifa->getNome().', SORTEIO: '.$infoRifa->getDataSorteio()->format($system->config["data"]["datetimeSimplesFormat"]).', QTDE MÍNIMA DE BILHETES: '.$infoRifa->getQtdeObrigatorio().'</h6></th></tr>
				<tr>
					<th style="text-align: left; '.$w1.'"><strong>FORMANDO</strong></th>
					<th style="text-align: center; '.$w2.'"><strong># GERADA</strong></th>
					<th style="text-align: center; '.$w3.'"><strong># VENDIDA</strong></th>
					<th style="text-align: right; '.$w4.'"><strong>R$ TOTAL</strong></th>
					<th style="text-align: right; '.$w5.'"><strong>R$ VENDIDO</strong></th>
					<th style="text-align: right; '.$w6.'"><strong>R$ PAGO</strong></th>
					<th style="text-align: right; '.$w7.'"><strong>R$ A PAGAR</strong></th>
				</tr>
				</thead><tbody>';
				
	$n		= 0;
	foreach ($dadosRel as $info) {
		$bgcolor	= ($n%2 == 0) ? "#EEEEEE" : "#FFFFFF";
		$table .= '<tr style="background-color:'.$bgcolor.'">
					<td style="text-align: left; '.$w1.'">'.$info["NOME"].'</td>
					<td style="text-align: center; '.$w2.'">'.$info["QTDE_GERADA"].'</td>
					<td style="text-align: center; '.$w3.'">'.$info["QTDE_VENDIDA"].'</td>
					<td style="text-align: right; '.$w4.'">'.\Zage\App\Util::to_money($info["VAL_TOTAL"]).'</td>
					<td style="text-align: right; '.$w5.'">'.\Zage\App\Util::to_money($info["VAL_VENDIDO"]).'</td>
					<td style="text-align: right; '.$w6.'">'.\Zage\App\Util::to_money($info["VAL_PAGO"]).'</td>
					<td style="text-align: right; '.$w7.'">'.\Zage\App\Util::to_money($info["VAL_APAGAR"]).'</td>
				</tr>
					';
		$n++;
		
	}
	$table .= '<tr style="background-color:#FFFFFF">
					<th style="text-align: right;"><strong>TOTAL GERAL:&nbsp;</strong></th>
					<th style="text-align: center;"><strong>'.$tQtdeGerada.'</strong></th>
					<th style="text-align: center;"><strong>'.$tQtdeVendida.'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($tTotal).'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($tVendido).'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($tRecebido).'</strong></th>
					<th style="text-align: right;"><strong>'.\Zage\App\Util::to_money($tAreceber).'</strong></th>
				</tr>
				</tbody>
				</table>';
	
}else{
	$table	= "<center>nenhuma informação encontrada !!!</center>";
}


$html	= '<body class="no-skin">';
$htmlTable	= '
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">'.$table.'</div><!--/span-->
		</div>
	</div>
</div>
</body>';

$html		.= $htmlTable;
$relName	= "ResumoFinanceiroRifa_".str_replace(" ", "_", $infoRifa->getNome()).".pdf";

$rel->WriteHTML($html);
$rel->Output($relName,'D');


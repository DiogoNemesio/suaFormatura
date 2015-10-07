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


try {
	$infoRifa 		= $em->getRepository('Entidades\ZgfmtRifa')->findOneBy(array('codigo' => $codRifa));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

if (!$infoRifa)	\Zage\App\Erro::halt($tr->trans('Rifa não encontrada').' (COD_RIFA)');

#################################################################################
## Resgata as informações da geração da rifa
#################################################################################
try {
	$numeros		= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $codRifa,'codGeracao' => $codGeracao));

	if (!$numeros)	\Zage\App\Erro::halt($tr->trans('Números não encontrada').' (COD_GERACAO)');
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Criar o PDF
#################################################################################
$mpdf	= new \mPDF(''	,'A4-L',10,'',15,15,16,16,9,9,'L');

#################################################################################
## Ajustar o timezone
#################################################################################
date_default_timezone_set($system->config["data"]["timezone"]);
setlocale (LC_ALL, 'ptb');

#################################################################################
## Formatar os dados do relatório
#################################################################################
$numNumeros		= sizeof($numeros);

$table	= '<table style="border-spacing: 4px 4px; border-collapse: separate;">';

for ($i = 0; $i < $numNumeros; $i++) {
	
	$valor		= $numeros[$i]->getCodRifa()->getValorUnitario();
	$turma		= $numeros[$i]->getCodRifa()->getCodOrganizacao()->getNome();
	$premio		= $numeros[$i]->getCodRifa()->getPremio();
	$local		= $numeros[$i]->getCodRifa()->getLocalSorteio();
	$data		= $numeros[$i]->getCodRifa()->getDataSorteio()->format($system->config["data"]["dateFormat"]);
	$numero		= $numeros[$i]->getNumero(); 
	$formando	= $numeros[$i]->getCodFormando()->getApelido();
	$nome		= $numeros[$i]->getCodRifa()->getNome();
	
	if ($i%2 == 0) {
		//$table .= '<tr style="border: 1px dotted #000000;">';
		$table .= '<tr style="width: 1300px;">';
	}
	
	$table .= '<td style="border: 1px dotted #000000; width: 840px;">';
	$table .= '<table style="width: 100%;"><tr>';

	
	$tab1	= '<table style="width: 100%;">';
	$tab1	.= '<tr><td><img src="'.IMG_URL.'/logo_sf_rifa.png" border=0 style="border: 0;"></td><td style="text-align: right; font-family: Trebuchet MS,Verdana; font-size:18px; font-weight: bold; color: #000000">#&nbsp;'.$numero.'</td></tr>';
	$tab1	.= '<tr><td colspan="2"><p style="font-family: Trebuchet MS,Verdana; font-size:14px; font-weight: normal; color: #000000">Nome:&nbsp;___________________________________ </p></td></tr>';
	$tab1	.= '<tr><td colspan="2"><p style="font-family: Trebuchet MS,Verdana; font-size:14px; font-weight: normal; color: #000000">Fone:&nbsp;____________________________________ </p></td></tr>';
	$tab1	.= '<tr><td colspan="2"><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: normal; color: #000000">Formando: '.$formando.' </p></td></tr>';
	$tab1	.= "</table>";	
	
	$tab2	= '<table style="width: 100%;">';
	$tab2	.= '<tr><td><p style="font-family: Trebuchet MS,Verdana; font-size:18px; font-weight: bold; color: #000000">'.$turma.' </p></td><td style="text-align: right; font-family: Trebuchet MS,Verdana; font-size:18px; font-weight: bold; color: #000000">Número:&nbsp;'.$numero.'</td></tr>';
	$tab2	.= '<tr><td><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: bold; color: #000000">'.$nome.'</p></td></tr>';
	$tab2	.= '<tr><td><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: normal; color: #000000">Local: '.$local.'</p></td><td><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: normal; color: #000000">Data do Sorteio: '.$data.'</p></td></tr>';
	$tab2	.= '<tr><td><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: normal; color: #000000">Prêmio: '.$premio.'</p></td><td><p style="font-family: Trebuchet MS,Verdana; font-size:12px; font-weight: normal; color: #000000">Valor: '.\Zage\app\Util::to_money($valor).' </p></td></tr>';
	$tab2	.= "</table>";
	
	
	$table .= '<td style="text-align: left; height: 104px; width: 37%; border-right: 1px dotted #000000; padding-left: 12px;">'.$tab1.'</td>';
	$table .= '<td style="text-align: left; height: 104px; width: 63%;">'.$tab2.'</td>';
	
	
	$table .= '</tr></table>';
	
	
	
	$table .= '</td>';
	
	if ($i%2 != 0) {
		$table .= '</tr>';
	}
}
$table .= '</table>';

$html	= '<html>
<head>
<style>
@page {
  margin: 32px;
  margin-header: 2px;
  margin-footer: 2px;
}
</style>
</head>
<body class="no-skin">';
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
$relName	= "Rifa_".str_replace(" ", "_", $infoRifa->getNome()).".pdf";

$mpdf->WriteHTML($html);
$mpdf->Output($relName,'D');


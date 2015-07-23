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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_POST['mesFiltro']))		$mesFiltro			= \Zage\App\Util::antiInjection($_POST['mesFiltro']);

if (!$mesFiltro) exit;


#################################################################################
## Calcular as datas da pesquisa
#################################################################################
$mes			= substr($mesFiltro,0,2);
$ano			= substr($mesFiltro,3,4);
$dataIni		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes),1,$ano));
$dataFim		= date($system->config["data"]["dateFormat"], mktime (0,0,0,($mes+1),0,$ano));
$hoje			= date($system->config["data"]["dateFormat"]);
$dataBase		= $dataIni;

if (\DateTime::createFromFormat($system->config["data"]["dateFormat"],$hoje) < \DateTime::createFromFormat($system->config["data"]["dateFormat"],$dataFim)) {
	$dataSaldo		= $hoje;
}else{
	$dataSaldo		= $dataFim;
}

#################################################################################
## Resgata as contas
#################################################################################
try {
	$contas	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()), array('nome' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta o div com os resultados
#################################################################################
$htmlDiv		= "";
$totalCre		= 0;
$totalDeb		= 0;
$totalSaldo		= 0;
for ($i = 0; $i < sizeof($contas); $i++) {
	
	$resultados			= \Zage\Fin\Conta::getResultadoProjetado($contas[$i]->getCodigo(),$dataIni,$dataFim);
	
	$creditos			= 0;
	$debitos			= 0;
	
	//print_r($resultados);
	for ($j = 0; $j < sizeof($resultados); $j++) {
		if ($resultados[$j]["TIPO"] == "D") {
			$debitos += $resultados[$j]["VALOR"];
		}elseif ($resultados[$j]["TIPO"] == "C") {
			$creditos += $resultados[$j]["VALOR"];
		}
	}
	
	$saldo			= $creditos - $debitos;
	$totalCre		+= $creditos;
	$totalDeb		+= $debitos;
	$totalSaldo		+= $saldo;
	
	if ($saldo > 0) {
		$clSaldo		= "green";
	}else{
		$clSaldo		= "red";
	}
	
	
	$htmlDiv	.= '<div class="row small">
		<div class="col-sm-3">'.$contas[$i]->getNome().'</div>
		<div class="col-sm-3"><div class="pull-right green">'.\Zage\App\Util::to_money($creditos).'</div></div>
		<div class="col-sm-3"><div class="pull-right red">'.\Zage\App\Util::to_money($debitos).'</div></div>
		<div class="col-sm-3"><div class="pull-right '.$clSaldo.'" >'.\Zage\App\Util::to_money($saldo).'</div></div>
	</div>
	'; 
}

if (!empty($htmlDiv)) {
	if ($totalSaldo > 0) {
		$clSaldo		= "green";
	}else{
		$clSaldo		= "red";
	}
	$htmlDiv	.= '<div class="row small">
		<div class="col-sm-3"><div class="pull-right"><strong>Totais</strong></div></div>
		<div class="col-sm-3"><div class="pull-right green"><strong>'.\Zage\App\Util::to_money($totalCre).'</strong></div></div>
		<div class="col-sm-3"><div class="pull-right red"><strong>'.\Zage\App\Util::to_money($totalDeb).'</strong></div></div>
		<div class="col-sm-3"><div class="pull-right '.$clSaldo.'" ><strong>'.\Zage\App\Util::to_money($totalSaldo).'</strong></div></div>
	</div>
	';
}


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('RESULTADOS'		,$htmlDiv);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

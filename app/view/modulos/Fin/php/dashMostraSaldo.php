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
$ultDiaMesAnt	= date($system->config["data"]["dateFormat"], mktime (0,0,0,$mes,0,$ano));
$hoje			= date($system->config["data"]["dateFormat"]);

if (\DateTime::createFromFormat($system->config["data"]["dateFormat"],$hoje) < \DateTime::createFromFormat($system->config["data"]["dateFormat"],$dataFim)) {
	$dataSaldo		= $hoje;
}else{
	$dataSaldo		= $dataFim;
}




#################################################################################
## Resgata as contas
#################################################################################
try {
	$contas	= $em->getRepository('Entidades\ZgfinConta')->findBy(array('codFilial' => $system->getCodEmpresa()), array('nome' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Monta o div com os saldos
#################################################################################
$htmlDiv	= "";
$totalCon	= 0;
$totalProj	= 0;
for ($i = 0; $i < sizeof($contas); $i++) {
	
	$saldoAtual			= \Zage\Fin\Conta::getSaldoDia($contas[$i]->getCodigo(), $dataSaldo);
	
	if ($dataSaldo == $dataFim) {
		$saldoProjetado		= $saldoAtual;
	}else{
		$saldoProjetado		= \Zage\Fin\Conta::getSaldoProjetadoDia($contas[$i]->getCodigo(),$ultDiaMesAnt,$dataFim);
	}
	
	$totalCon	+= $saldoAtual;
	$totalProj	+= $saldoProjetado;
	
	if ($saldoAtual > 0) {
		$clSaldoAtual		= "green";
	}else{
		$clSaldoAtual		= "red";
	}
	
	if ($saldoProjetado > 0) {
		$clSaldoProjetado	= "green";
	}else{
		$clSaldoProjetado	= "red";
	}
	
	
	$htmlDiv	.= '<div class="row small">
	<div class="col-sm-4">'.$contas[$i]->getNome().'</div>
	<div class="col-sm-4"><div class="pull-right '.$clSaldoAtual.'">'.\Zage\App\Util::to_money($saldoAtual).'</div></div>
	<div class="col-sm-4"><div class="pull-right '.$clSaldoProjetado.'">'.\Zage\App\Util::to_money($saldoProjetado).'</div></div>
	</div>
	'; 
}

if (!empty($htmlDiv)) {
	
	$clSaldoCon 	= ($totalCon 	> 0) ? "green" : "red";
	$clSaldoProj 	= ($totalProj 	> 0) ? "green" : "red";

	$htmlDiv	.= '<div class="row small">
		<div class="col-sm-4"><div class="pull-right"><strong>Totais</strong></div></div>
		<div class="col-sm-4"><div class="pull-right '.$clSaldoCon.'" ><strong>'.\Zage\App\Util::to_money($totalCon).'</strong></div></div>
		<div class="col-sm-4"><div class="pull-right '.$clSaldoProj.'" ><strong>'.\Zage\App\Util::to_money($totalProj).'</strong></div></div>
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
$tpl->set('SALDOS'			,$htmlDiv);
$tpl->set('DATA_SALDO'		,$dataSaldo);
$tpl->set('DATA_PROJECAO'	,$dataFim);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

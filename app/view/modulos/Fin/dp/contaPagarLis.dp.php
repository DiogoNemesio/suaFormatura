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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_GET['sEcho'])) 			$sEcho			= \Zage\App\Util::antiInjection($_GET['sEcho']);
if (isset($_GET['iDisplayStart'])) 	$iDisplayStart	= \Zage\App\Util::antiInjection($_GET['iDisplayStart']);
if (isset($_GET['iDisplayLength']))	$iDisplayLength	= \Zage\App\Util::antiInjection($_GET['iDisplayLength']);
if (isset($_GET['sSearch'])) 		$sSearch		= \Zage\App\Util::antiInjection($_GET['sSearch']);
if (isset($_GET['id'])) 			$id				= \Zage\App\Util::antiInjection($_GET['id']);

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$contas	= \Zage\Fin\ContaPagar::lista();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Incluir o script de configuração do Grid
#################################################################################
include (MOD_PATH . '/Fin/php/contaPagarLisGrid.php');
$grid->importaDadosDoctrine($contas);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($contas); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url);
	$vid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConta='.$contas[$i]->getCodigo().'&url='.$url.'&view=1');
	
	/** Parcela / NumParcelas **/
	$grid->setValorCelula($i,$colParcela,$contas[$i]->getParcela() . " / ".$contas[$i]->getNumParcelas());
	
	/** Valor Total **/
	$grid->setValorCelula($i,$colValTot,( floatval($contas[$i]->getValor()) + floatval($contas[$i]->getValorJuros()) + floatval($contas[$i]->getValorMora()) - floatval($contas[$i]->getValorDesconto()) ));
	
	/** Resgatar o status para controlar as ações **/
	$status		= $contas[$i]->getCodStatus()->getCodigo();
	
	switch ($status) {
		
		case "A":
			$podeAlt	= true;
			$podeExc	= true;
			$podeCan	= true;
			$podeCon	= true;
			$podeHis	= false;
			$podePri	= true;
			break;
		case "C":
			$podeAlt	= false;
			$podeExc	= true;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= false;
			$podePri	= true;
			break;
		case "L":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= true;
			$podePri	= true;
			break;
		case "SC":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= true;
			$podePri	= true;
			break;
		case "S":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= false;
			$podePri	= true;
			break;
		case "P":
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= true;
			$podeCon	= true;
			$podeHis	= true;
			$podePri	= true;
			break;
		default:
			$podeAlt	= false;
			$podeExc	= false;
			$podeCan	= false;
			$podeCon	= false;
			$podeHis	= false;
			$podePri	= false;
			break;
	}
	
	$grid->setUrlCelula($i,$colVis,ROOT_URL.'/Fin/contaPagarAlt.php?id='.$vid);
	$grid->setUrlCelula($i,$colAlt,ROOT_URL.'/Fin/contaPagarAlt.php?id='.$uid);
	$grid->setUrlCelula($i,$colExc,"javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarExc.php?id=".$uid."');");
	$grid->setUrlCelula($i,$colCan,"javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarCan.php?id=".$uid."');");
	$grid->setUrlCelula($i,$colPag,"javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarPag.php?id=".$uid."');");
	$grid->setUrlCelula($i,$colHis,"javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarHis.php?id=".$uid."');");
	$grid->setUrlCelula($i,$colPri,"javascript:zgAbreModal('".ROOT_URL."/Fin/contaPagarPri.php?id=".$uid."');");
	
	if (!$podeAlt)	$grid->desabilitaCelula($i, $colAlt);
	if (!$podeExc)	$grid->desabilitaCelula($i, $colExc);
	if (!$podeCan)	$grid->desabilitaCelula($i, $colCan);
	if (!$podeCon)	$grid->desabilitaCelula($i, $colPag);
	if (!$podeHis)	$grid->desabilitaCelula($i, $colHis);
	if (!$podePri)	$grid->desabilitaCelula($i, $colPri);
	
}

#################################################################################
## Por fim retornar o código JSON
#################################################################################
$json = $grid->getJsonData(intval($sEcho),sizeof($contas),$iDisplayStart,$iDisplayLength);
echo $json;

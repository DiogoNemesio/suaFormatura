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
if (isset($_GET['pDescBT1'])) 	$pDescBT1	= \Zage\App\Util::antiInjection($_GET['pDescBT1']);
if (isset($_GET['pDescBT2'])) 	$pDescBT2	= \Zage\App\Util::antiInjection($_GET['pDescBT2']);
if (isset($_GET['pDescBT3'])) 	$pDescBT3	= \Zage\App\Util::antiInjection($_GET['pDescBT3']);
if (isset($_GET['pDescBT4'])) 	$pDescBT4	= \Zage\App\Util::antiInjection($_GET['pDescBT4']);

if (isset($_GET['pUrlBT2'])) 	$pUrlBT2	= \Zage\App\Util::antiInjection($_GET['pUrlBT2']);
if (isset($_GET['pUrlBT3'])) 	$pUrlBT3	= \Zage\App\Util::antiInjection($_GET['pUrlBT3']);
if (isset($_GET['pUrlBT4'])) 	$pUrlBT4	= \Zage\App\Util::antiInjection($_GET['pUrlBT4']);

#################################################################################
## Resgata os avisos
#################################################################################
$avisos	= "";
if ($system->avisos) {
	foreach ($system->avisos as $i => $aviso) {
		$avisos	.= '<div class="alert '.$aviso->getClasse().'">';
		$avisos	.= '<button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>';
		if ($aviso->getIcone()) {
			$avisos	.= '<i class="'.$aviso->getIcone().'"></i>';
		}
		$avisos	.= $tr->trans($aviso->getMensagem());
		$avisos	.= '</div>';
		$system->excluiAviso($i);
	}
}


#################################################################################
## Monta os botões
#################################################################################
$botoes	= '';
if (isset($pDescBT1) && !empty($pDescBT1)) {
	$bt1	= $tr->trans($pDescBT1);
}else{
	$bt1	= "OK";
}

if (isset($pDescBT2) && !empty($pDescBT2)) {
	$url = (!$pUrlBT2) ? "#" : $pUrlBT2;
	$botoes		.= '<button type="button" data-dismiss="modal" class="btn" onclick="javascript:zgLoadUrl(\''.$url.'\');">'.$pDescBT2.'</button>'; 
}
if (isset($pDescBT3) && !empty($pDescBT3)) {
	$url = (!$pUrlBT3) ? "#" : $pUrlBT3;
	$botoes		.= '<button type="button" data-dismiss="modal" class="btn" onclick="javascript:zgLoadUrl(\''.$url.'\');">'.$pDescBT3.'</button>';
}
if (isset($pDescBT4) && !empty($pDescBT4)) {
	$url = (!$pUrlBT4) ? "#" : $pUrlBT4;
	$botoes		.= '<button type="button" data-dismiss="modal" class="btn" onclick="javascript:zgLoadUrl(\''.$url.'\');">'.$pDescBT4.'</button>';
}



#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('TITULO'		,$tr->trans("Avisos"));
$tpl->set('BT1'			,$bt1);
$tpl->set('BOTOES'		,$botoes);
$tpl->set('AVISOS'		,$avisos);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


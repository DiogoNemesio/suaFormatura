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
## Resgata as notificações 
#################################################################################
$notificacoes	= array();
//$notificacoes	= array(0 => (object) array("MENSAGEM" => "Oi"));

#################################################################################
## Gera o código da notificação
#################################################################################
$numNot			= sizeof($notificacoes);
$html			= "";
$html			.= str_repeat(\Zage\App\ZWS::TAB,4).'<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-bell icon-animated-bell"></i><span class="badge badge-important">'.$numNot.'</span></a>'.\Zage\App\ZWS::NL;
if ($numNot > 0) {
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="pull-right dropdown-navbar navbar-red dropdown-menu dropdown-caret dropdown-close">'.\Zage\App\ZWS::NL;
	$html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="dropdown-header"><i class="ace-icon fa fa-warning-sign"></i>Notificações</li>'.\Zage\App\ZWS::NL;
	foreach ($notificacoes as $not) {
		$html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li><a href="#"><div class="clearfix"><span class="pull-left"><i class="btn btn-xs no-hover btn-pink fa fa-comment"></i>'.$not->MENSAGEM.'</span></div></a></li>'.\Zage\App\ZWS::NL;
	}
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
}
echo $html;
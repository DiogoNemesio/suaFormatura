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
## Resgata as mensagens
#################################################################################
$mensagens	= array();

#################################################################################
## Gera o código da notificação
#################################################################################
$numMen		= sizeof($mensagens);
$html		= "";
$html	.= str_repeat(\Zage\App\ZWS::TAB,4).'<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-envelope icon-animated-vertical"></i><span class="badge badge-success">'.$numMen.'</span></a>'.\Zage\App\ZWS::NL;
if ($numMen > 0) {
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="pull-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">'.\Zage\App\ZWS::NL;
	$html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="dropdown-header"><i class="ace-icon fa fa-envelope-alt"></i>Mensagens</li>'.\Zage\App\ZWS::NL;
	foreach ($mensagens as $men) {
		$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<a href="#"><img src="%PKG_URL%/ace/assets/avatars/avatar.png" class="msg-photo" alt="'.$men->REMETENTE.'" /><span class="msg-body"><span class="msg-title"><span class="blue">'.$men->REMETENTE.':</span>'.$men->MENSAGEM.'</span><span class="msg-time"><i class="fa fa-time"></i><span>'.$men->HORA.'</span></span></span></a>'.\Zage\App\ZWS::NL;
	}
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
}

echo $html;
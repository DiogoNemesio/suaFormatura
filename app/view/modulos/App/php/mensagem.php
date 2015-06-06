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
## Resgata os convites que existem para o usuário
#################################################################################
$convites1		= $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codUsuarioDestino' 	=> $_user->getCodigo()	,'indUtilizado' => '0'));
$convites2		= $em->getRepository('Entidades\ZgsegConvite')->findOneBy(array('codOrganizacaoDestino' => $_org->getCodigo()	,'indUtilizado' => '0'));



#################################################################################
## Inicializa o array de mensagens
#################################################################################
$mensagens	= array();

#################################################################################
## Monta as mensagens
#################################################################################
for ($i = 0; $i < sizeof($convites1); $i++) {
	$n	= sizeof($mensagens);
	$mensagens[$n]["DATA"]		= $convites1[$i]->getData()->format($system->config["data"]["datetimeFormat"]);
	$mensagens[$n]["REMETENTE"]	= $convites1[$i]->getCodUsuarioSolicitante()->getNome();
	$mensagens[$n]["MENSAGEM"]	= "Convite para participar da Organização: ".$convites1[$i]->getCodOrganizacaoOrigem()->getNome();
}

#################################################################################
## Monta as mensagens
#################################################################################
for ($i = 0; $i < sizeof($convites2); $i++) {
	$n	= sizeof($mensagens);
	$mensagens[$n]["DATA"]		= $convites2[$i]->getData()->format($system->config["data"]["datetimeFormat"]);
	$mensagens[$n]["REMETENTE"]	= $convites2[$i]->getCodUsuarioSolicitante()->getNome();
	$mensagens[$n]["MENSAGEM"]	= "Convite solicitado por : ".$convites2[$i]->getCodUsuarioOrigem()->getNome();
}

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
		$html	.= str_repeat(\Zage\App\ZWS::TAB,5).'<a href="#"><img src="%PKG_URL%/ace/assets/avatars/avatar.png" class="msg-photo" alt="'.$men["REMETENTE"].'" /><span class="msg-body"><span class="msg-title"><span class="blue">'.$men["REMETENTE"].':</span>'.$men["MENSAGEM"].'</span><span class="msg-time"><i class="fa fa-time"></i><span>'.$men["DATA"].'</span></span></span></a>'.\Zage\App\ZWS::NL;
	}
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
}

echo $html;
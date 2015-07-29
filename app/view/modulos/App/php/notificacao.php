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
global $em,$system,$tr,$log,$db;

#################################################################################
## Resgata as notificações 
#################################################################################
$notificacoes		= \Zage\App\Notificacao::listaPendentes($system->getCodUsuario());

#################################################################################
## Gera o código html da notificação
#################################################################################
$numNot			= sizeof($notificacoes);
$html			= "";
$html			.= str_repeat(\Zage\App\ZWS::TAB,4).'<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="ace-icon fa fa-bell icon-animated-bell"></i><span class="badge badge-important">'.$numNot.'</span></a>'.\Zage\App\ZWS::NL;
if ($numNot > 0) {
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'<ul class="pull-right dropdown-navbar navbar-red dropdown-menu dropdown-caret dropdown-close">'.\Zage\App\ZWS::NL;
	$html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li class="dropdown-header"><i class="ace-icon fa fa-warning-sign"></i>Notificações</li>'.\Zage\App\ZWS::NL;
	foreach ($notificacoes as $not) {
		if ($not->getCodRemetente()) {
			$avatar	= ($not->getCodRemetente()->getAvatar()) ? $not->getCodRemetente()->getAvatar()->getLink() : IMG_URL."/avatars/usuarioGenerico.png";
			$nome	= $not->getCodRemetente()->getApelido();
		}else{
			$avatar	= IMG_URL."/avatars/usuarioGenerico.png";
			$nome	= "Anônimo";
		}
		$temAnexo		= \Zage\App\Notificacao::temAnexo($not->getCodigo());
		
		if ($temAnexo)	{
			$data		= $not->getData()->format($system->config["data"]["datetimeFormat"]) . "&nbsp;<i class='fa fa-paperclip blue fa-lg'></i>";
		}else{
			$data		= $not->getData()->format($system->config["data"]["datetimeFormat"]);
		}
		
		$avatar	= str_replace("%IMG_URL%", IMG_URL, $avatar);
		$lid	= \Zage\App\Util::encodeUrl('codUsuario='.$system->getCodUsuario().'&codNotificacao='.$not->getCodigo());
		$html .= str_repeat(\Zage\App\ZWS::TAB,5).'<li>
			<a href="javascript:lerNotificacao(\''.$lid.'\');" class="clearfix">
			<img src="'.$avatar.'" class="msg-photo" alt="'.$nome.'" />
			<span class="msg-body">
			<span class="msg-title">
			<span class="blue">'.$nome.'</span>
			'.$not->getAssunto().'
			</span>
			<span class="msg-time">
			<i class="ace-icon fa fa-clock-o"></i>
			<span>'.$data.'</span>
			</span>
			</span>
			</a>
		</li>'.\Zage\App\ZWS::NL;
	}
	$html .= str_repeat(\Zage\App\ZWS::TAB,4).'</ul>'.\Zage\App\ZWS::NL;
}
echo $html;
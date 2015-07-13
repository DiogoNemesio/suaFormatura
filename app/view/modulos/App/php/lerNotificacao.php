<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

################################################################################
# Resgata a variável ID que está criptografada
################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

################################################################################
# Descompacta o ID
################################################################################
\Zage\App\Util::descompactaId ( $id );

#################################################################################
## Verifica se os parâmetros foram passados
#################################################################################
if (!isset($codUsuario)) exit;
if (!isset($codNotificacao)) exit;

#################################################################################
## Verifica o usuário
#################################################################################
if ($codUsuario != $system->getCodUsuario()) exit;

#################################################################################
## resgata as informações da notificação
#################################################################################
$notificacao		= $em->getRepository('\Entidades\ZgappNotificacao')->findOneBy(array('codigo' => $codNotificacao));
if (!$notificacao)	throw new \Exception('Notificação não encontrada !!!');

if ($notificacao->getCodUsuario()) {
	$avatar		= ($notificacao->getCodUsuario()->getAvatar()) ? $notificacao->getCodUsuario()->getAvatar()->getLink() : IMG_URL."/avatars/usuarioGenerico.png";
	$nome		= $notificacao->getCodUsuario()->getNome();
	$apelido	= $notificacao->getCodUsuario()->getApelido();
	$email		= $notificacao->getCodUsuario()->getUsuario();
}else{
	$avatar		= IMG_URL."/avatars/usuarioGenerico.png";
	$nome		= "Anônimo";
	$apelido	= "Anônimo";
	$email		= "contato@suaformatura.com";
}

$codTipoMens		= $notificacao->getCodTipoMensagem()->getCodigo();
$assunto			= $notificacao->getAssunto();
$data				= $notificacao->getData()->format($system->config["data"]["datetimeFormat"]);
$log->info("Data:".$data);

if ($codTipoMens	== "TX") {
	$mensagem		= $notificacao->getMensagem();
}elseif ($codTipoMens	== "H") {
	$mensagem		= $notificacao->getMensagem();
}elseif ($codTipoMens	== "TP") {
	
	throw new \Exception('Tipo de notificação não implementada !!!');
	#################################################################################
	## Carrega o template
	#################################################################################
	
}else{
	throw new \Exception('Tipo de notificação não implementada !!!');
}


#################################################################################
## Seta a flag de leitura
#################################################################################
//\Zage\App\Notificacao::ler($codNotificacao, $codUsuario);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ASSUNTO'		,$assunto);
$tpl->set('NOME'		,$nome);
$tpl->set('APELIDO'		,$apelido);
$tpl->set('DATA_NOT'	,$data);
$tpl->set('AVATAR'		,$avatar);
$tpl->set('EMAIL'		,$email);
$tpl->set('MENSAGEM'	,$mensagem);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();



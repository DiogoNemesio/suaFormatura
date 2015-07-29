<?php
################################################################################
# Includes
################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

#################################################################################
## Variáveis globais
#################################################################################
global $em,$system,$tr,$log,$db;


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

if ($notificacao->getCodRemetente()) {
	$avatar		= ($notificacao->getCodRemetente()->getAvatar()) ? $notificacao->getCodRemetente()->getAvatar()->getLink() : IMG_URL."/avatars/usuarioGenerico.png";
	$nome		= $notificacao->getCodRemetente()->getNome();
	$apelido	= $notificacao->getCodRemetente()->getApelido();
	$email		= $notificacao->getCodRemetente()->getUsuario();
}else{
	$avatar		= IMG_URL."/avatars/usuarioGenerico.png";
	$nome		= "Anônimo";
	$apelido	= "Anônimo";
	$email		= "contato@suaformatura.com";
}

$temAnexo			= \Zage\App\Notificacao::temAnexo($notificacao->getCodigo());
$codTipoMens		= $notificacao->getCodTipoMensagem()->getCodigo();
$assunto			= $notificacao->getAssunto();
$data				= $notificacao->getData()->format($system->config["data"]["datetimeFormat"]);

if ($codTipoMens	== "TX") {
	$mensagem		= '<div class="widget-body"><div class="widget-main"><div class="row"><p>'.$notificacao->getMensagem().'</p></div></div></div>';
}elseif ($codTipoMens	== "H") {
	$mensagem		= '<div class="widget-body"><div class="widget-main"><div class="row"><p>'.$notificacao->getMensagem().'</p></div></div></div>';
}elseif ($codTipoMens	== "TP") {
	
	#################################################################################
	## Verificar se o template foi informado
	#################################################################################
	if (!$notificacao->getCodTemplate()) throw new \Exception('Template não informado !!!');
	$tplId		= \Zage\App\Util::encodeUrl('codUsuario='.$system->getCodUsuario().'&codNotificacao='.$codNotificacao.'&codTemplate='.$notificacao->getCodTemplate()->getCodigo());
	$mensagem 	= '<iframe style="width: 100%;height: 100%; overflow:hidden; border: 0px;" id="zgWindowLerNotID" src="'.ROOT_URL . '/App/lerNotificacaoTpl.php?id='.$tplId.'"></iframe>';
	
}else{
	throw new \Exception('Tipo de notificação não implementada !!!');
}

#################################################################################
## Montar o array que gerencia os botões de pŕoximo e anterior
#################################################################################
if (!isset($arrayLoaded)) {
	unset($_SESSION['aNotProc']);
	$_SESSION['aNotProc']	= array();
	$notificacoes		= \Zage\App\Notificacao::listaPendentes($system->getCodUsuario());
	for ($i = 0; $i < sizeof($notificacoes); $i++) {
		$_SESSION['aNotProc'][$i]	= $notificacoes[$i]->getCodigo();
	}
}
if (!isset($_SESSION['aNotProc']))	{
	$_SESSION['aNotProc']	= array();
}

#################################################################################
## Monta os botões de anterior e posterior
#################################################################################
$indice		= array_search($notificacao->getCodigo(), $_SESSION['aNotProc']);
if ($indice === false) {
	$urlAnt		= "#";
	$urlDep		= "#";

	$disAnt		= "disabled";
	$disDep		= "disabled";
}else{

	$seqAnt		= isset($_SESSION['aNotProc'][$indice-1]) ? $_SESSION['aNotProc'][$indice-1] : null;
	$seqDep		= isset($_SESSION['aNotProc'][$indice+1]) ? $_SESSION['aNotProc'][$indice+1] : null;

	$antID		= \Zage\App\Util::encodeUrl('codUsuario='.$system->getCodUsuario().'&codNotificacao='.$seqAnt.'&arrayLoaded=1');
	$depID		= \Zage\App\Util::encodeUrl('codUsuario='.$system->getCodUsuario().'&codNotificacao='.$seqDep.'&arrayLoaded=1');

	$urlAnt		= ($seqAnt == null) ? "#" : "javascript:lerNotificacao('".$antID."');";
	$urlDep		= ($seqDep == null) ? "#" : "javascript:lerNotificacao('".$depID."');";

	$disAnt		= ($seqAnt == null) ? "disabled" : "";
	$disDep		= ($seqDep == null) ? "disabled" : "";

}

#################################################################################
## Monta o div de anexos
#################################################################################
if ($temAnexo) {
	$mostraAnexo	= "";
	$anexos			= $em->getRepository('\Entidades\ZgappNotificacaoAnexo')->findBy(array('codNotificacao' => $codNotificacao));
	$numAnexos		= sizeof($anexos);
	$htmlAnexos		= "";
	if ($numAnexos == 1) {
		$textoAnexos	= "Anexo";
	}else{
		$textoAnexos	= "Anexos";
	}
	for ($i = 0 ; $i < $numAnexos; $i++) {
		$did		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotificacao='.$codNotificacao.'&codAnexo='.$anexos[$i]->getCodigo());
		$urlDown	= ROOT_URL . "/App/notificacaoAnexoDown.php?id=".$did;;
		$htmlAnexos	.= '<li>
		<a href="javascript:zgDownloadUrl(\''.$urlDown.'\');" class="">
			<i class="ace-icon fa fa-download bigger-125 blue"></i>
			<span class="">'.$anexos[$i]->getNome().'</span>
		</a>
		</li>';
	}
}else{
	$mostraAnexo	= "hidden";
	$numAnexos		= 0;
	$htmlAnexos		= "";
	$textoAnexos	= "";
}

#################################################################################
## Seta a flag de leitura
#################################################################################
\Zage\App\Notificacao::ler($codNotificacao, $codUsuario);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ASSUNTO'			,$assunto);
$tpl->set('NOME'			,$nome);
$tpl->set('APELIDO'			,$apelido);
$tpl->set('DATA_NOT'		,$data);
$tpl->set('AVATAR'			,$avatar);
$tpl->set('EMAIL'			,$email);
$tpl->set('MENSAGEM'		,$mensagem);
$tpl->set('DIS_ANT'			,$disAnt);
$tpl->set('DIS_DEP'			,$disDep);
$tpl->set('URL_ANT'			,$urlAnt);
$tpl->set('URL_DEP'			,$urlDep);
$tpl->set('MOSTRA_ANEXO'	,$mostraAnexo);
$tpl->set('HTML_ANEXOS'		,$htmlAnexos);
$tpl->set('NUM_ANEXOS'		,$numAnexos);
$tpl->set('TEXTO_ANEXOS'	,$textoAnexos);



#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();



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
global $em,$tr,$system;

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
	\Zage\App\Erro::halt('FALTA PARÂMENTRO : ID');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\App\Util::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/App/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$logs	= $em->getRepository('Entidades\ZgappNotificacaoLog')->findBy(array('codNotificacao' => $codNotifLog), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GNotifLog");
$grid->adicionaTexto($tr->trans('FORMA DE ENVIO'),	15, $grid::CENTER	,'codFormaEnvio:nome');
$grid->adicionaTexto($tr->trans('STATUS'),	 		15, $grid::CENTER	,'indProcessada');
$grid->adicionaIcone(null,'fa fa-info-circle',$tr->trans('Detalhes'));
$grid->importaDadosDoctrine($logs);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($logs); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$codNotifLog.'&codLog='.$logs[$i]->getCodigo());

	if ($logs[$i]->getIndProcessada() == 1){
		$grid->setValorCelula($i, 1, "<span class=\"label label-success arrowed\">Processada</span>");
	}else{
		$grid->setValorCelula($i, 1, "<span class=\"label label-danger arrowed-in\">Não Processada</span>");
	}
	$grid->setUrlCelula($i,2,ROOT_URL.'/App/notificacaoLogDest.php?id='.$uid);
}

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/App/notificacaoLogLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/App/notificacaoLogEnv.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$codNotifLog);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Logs forma de envio"));
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('ASSUNTO'			,"Log envio");
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

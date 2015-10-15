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
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/App/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$notifLog	= $em->getRepository('Entidades\ZgappNotificacao')->findBy(array(), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GNotifLog");
$grid->adicionaTexto($tr->trans('ASSUNTO'),		 	18, $grid::CENTER	,'assunto');
$grid->adicionaTexto($tr->trans('FORMA ENVIO'),		18, $grid::CENTER	,'indViaEmail');
$grid->adicionaTexto($tr->trans('STATUS'),	 		15, $grid::CENTER	,'indProcessada');
$grid->adicionaTexto($tr->trans('DATA'),	 		15, $grid::CENTER	,'data');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($notifLog);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($notifLog); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$notifLog[$i]->getCodigo().'&url='.$url);
	
	if ($notifLog[$i]->getIndProcessada() == 1) {
		$grid->setValorCelula($i, 2, "PROCESSADA");
	}else{
		$grid->setValorCelula($i, 2, "NÂO PROCESSADA");
	}
	
	//$grid->setUrlCelula($i,4,ROOT_URL.'/App/notificacaoLogAlt.php?id='.$uid);
	$grid->setUrlCelula($i,4,"javascript:zgAbreModal('".ROOT_URL.'/App/notificacaoLogAlt.php?id='.$uid."');");
	
	//$grid->setUrlCelula($i,4,ROOT_URL.'/App/parametroExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/App/notificacaoLogAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Logs de Notificação'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

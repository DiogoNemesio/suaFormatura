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
	$logs	= $em->getRepository('Entidades\ZgappNotificacaoLogDest')->findBy(array('codLog' => $codLog), array());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GNotifLog");
$grid->adicionaTexto($tr->trans('USUÁRIO'),			15, $grid::CENTER	,'codUsuario:usuario');
$grid->adicionaDataHora($tr->trans('DATA ENVIO'),	15, $grid::CENTER	,'dataEnvio');
$grid->adicionaTexto($tr->trans('STATUS'),			15, $grid::CENTER	,'indErro');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO ERRO'),	30, $grid::CENTER	,'erro');
$grid->importaDadosDoctrine($logs);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($logs); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$codLog);

	if ($logs[$i]->getCodUsuario()){
		$grid->setValorCelula($i, 0, $logs[$i]->getCodUsuario()->getUsuario());
	}else if ($logs[$i]->getCodPessoa()){
		$grid->setValorCelula($i, 0, $logs[$i]->getCodPessoa()->getEmail());
	}else if ($logs[$i]->getEmail()){
		$grid->setValorCelula($i, 0, $logs[$i]->getEmail());
	}
	
	if ($logs[$i]->getIndErro() != 1){
		$grid->setValorCelula($i, 2, "<span class=\"label label-success arrowed\">Processada</span>");
	}else{
		$grid->setValorCelula($i, 2, "<span class=\"label label-danger arrowed-in\">Não Processada</span>");
	}
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
$urlInicio			= ROOT_URL.'/App/notificacaoLogLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlVoltar			= ROOT_URL.'/App/notificacaoLogEnv.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$codNotifLog);
$urlAtualizar		= ROOT_URL.'/App/notificacaoLogDest.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codNotifLog='.$codNotifLog.'&codLog='.$codLog);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Destinatário"));
$tpl->set('URLINICIO'		,$urlInicio);
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('ASSUNTO'			,"Destinatário");
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

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
$url		= ROOT_URL . '/Fmt/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$evento		= $em->getRepository('Entidades\ZgfmtEvento')->findBy(array('codFormatura' => $system->getCodOrganizacao()),array('nome' => 'ASC'));
	$eventoTipo	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findBy(array(),array('descricao' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GEventoA");
$grid->adicionaTexto($tr->trans('NOME'),				30, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('TIPO EVENTO'),			30, $grid::CENTER	,'codTipoEvento:descricao');
$grid->adicionaTexto($tr->trans('LOCAL'),				20, $grid::CENTER	,'nome');
$grid->adicionaDataHora($tr->trans('DATA'),				10, $grid::CENTER	,'data');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($evento);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($evento); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEvento='.$evento[$i]->getCodigo().'&url='.$url);
	
	$grid->setUrlCelula($i,4,ROOT_URL.'/Fmt/eventoAgendarAlt.php?id='.$uid);
	$grid->setUrlCelula($i,5,"javascript:zgAbreModal('".ROOT_URL."/Fmt/eventoAgendarExc.php?id=".$uid."');");
}

#################################################################################
## Gerar a url de adicão
#################################################################################
for ($i = 0; $i < sizeof($eventoTipo); $i++) {
	$urlAdd		   = ROOT_URL.'/Fmt/eventoAgendarAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipo='.$eventoTipo[$i]->getCodigo().'&codEvento=');
	$htmlButton	  .= "<li>
						<a href=\"javascript:zgLoadUrl('".$urlAdd."');\">".$eventoTipo[$i]->getDescricao()."</a>
				  	  </li>";
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
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));
//$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Agendar Evento'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('HTML_BUTTON'		,$htmlButton);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

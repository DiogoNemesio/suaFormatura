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
	$eventoTipo	= $em->getRepository('Entidades\ZgfmtEventoTipo')->findBy(array(),array('descricao' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GEventoT");
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			40, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('STATUS'),				20, $grid::CENTER	,'indAtivo');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($eventoTipo);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($eventoTipo); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEventoTipo='.$eventoTipo[$i]->getCodigo().'&url='.$url);
	
	if ($eventoTipo[$i]->getIndAtivo() == 1){ 
		$valor = "ATIVO";
	}else{
		$valor = "DESATIVADO";
	}
	$grid->setValorCelula($i,1,$valor);
	
	$grid->setUrlCelula($i,2,ROOT_URL.'/Fmt/eventoTipoAlt.php?id='.$uid);
	$grid->setUrlCelula($i,3,"javascript:zgAbreModal('".ROOT_URL."/Fmt/eventoTipoExc.php?id=".$uid."');");
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
$urlAdd			= ROOT_URL.'/Fmt/eventoTipoAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codEventoTipo=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Tipos de Evento'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

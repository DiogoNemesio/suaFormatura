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
## Resgata os parâmetros passados pelo formulario
#################################################################################
if (isset($_GET['codSubgrupo'])){
	$codSubgrupo		= \Zage\App\Util::antiInjection($_GET['codSubgrupo']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codSubgrupo')));
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Est/'. basename(__FILE__)."?id=".$id."&codSubgrupo=".$codSubgrupo;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$conf	= $em->getRepository('Entidades\ZgestSubgrupoConf')->findBy(array('codSubgrupo' => $codSubgrupo));
	$info	= $em->getRepository('Entidades\ZgestSubgrupo')->findOneBy(array('codigo' => $codSubgrupo));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GConf");
$grid->adicionaTexto($tr->trans('NOME'),		30, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('TIPO'),		20, $grid::CENTER	,'codTipo:descricao');

$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($conf);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($conf); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConf='.$conf[$i]->getCodigo().'&codSubgrupo='.$codSubgrupo);
	$grid->setUrlCelula($i,2,ROOT_URL.'/Est/subgrupoConfAlt.php?id='.$uid);
	$grid->setUrlCelula($i,3,'javascript:zgAbreModal(\''.ROOT_URL.'/Est/subgrupoConfExc.php?id='.$uid.'\');');
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
$urlAdd			= ROOT_URL.'/Est/subgrupoConfAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codConf='.'&codSubgrupo='.$codSubgrupo);

###################################ub##############################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Configuração'));
$tpl->set('NOME_TIPO'		,$info->getDescricao());
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,'fa fa-cogs');

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

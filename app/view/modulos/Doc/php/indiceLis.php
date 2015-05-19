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
if (isset($_GET['codTipoDoc'])) 		{
	$codTipoDoc		= \Zage\App\Util::antiInjection($_GET['codTipoDoc']);
}else{
	\Zage\App\Erro::halt($tr->trans('Falta de Parâmetros (%s) ',array('%s' => 'codTipoDoc')));
}

#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Doc/'. basename(__FILE__)."?id=".$id."&codTipoDoc=".$codTipoDoc;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$indices	= \Zage\Doc\Indice::lista($codTipoDoc);
	$info		= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipoDoc));
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
$grid->adicionaTexto($tr->trans('NOME'), 		15, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),	30, $grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('TIPO'),		10, $grid::CENTER	,'codTipo:nome');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($indices);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($indices); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codIndice='.$indices[$i]->getCodigo().'&codTipoDoc='.$codTipoDoc);
	$grid->setUrlCelula($i,3,ROOT_URL.'/Doc/indiceAlt.php?id='.$uid);
	$grid->setUrlCelula($i,4,'javascript:zgAbreModal(\''.ROOT_URL.'/Doc/indiceExc.php?id='.$uid.'\');');
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
$urlAdd			= ROOT_URL.'/Doc/indiceAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codIndice='.'&codTipoDoc='.$codTipoDoc);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Índices'));
$tpl->set('NOME_TIPO'		,$info->getNome());
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,'fa fa-tags');

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

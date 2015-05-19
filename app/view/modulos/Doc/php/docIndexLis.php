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
$url		= ROOT_URL . '/Doc/'. basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$info			= $em->getRepository('Entidades\ZgdocDocumentoTipo')->findOneBy(array('codigo' => $codTipoDoc));
	
	if (!$info) {
		\Zage\App\Erro::halt($tr->trans("Tipo de documento não existe"));
	}

	$documentos		= \Zage\Doc\Documento::listaNaoIndexados($codTipoDoc);
	
	
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
$grid->adicionaTexto($tr->trans('NOME'), 		25, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('DATA'), 		18, $grid::CENTER	,'dataCadastro');
$grid->adicionaTexto($tr->trans('TIPO'),		15, $grid::CENTER	,'codTipoArquivo:nome|Outros');
$grid->adicionaTexto($tr->trans('TAMANHO'),		10, $grid::CENTER	,'');
$grid->adicionaIcone(null,'fa fa-tag',$tr->trans('Indexar'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($documentos);


#################################################################################
## Popula os valores dos botões
#################################################################################
$disabled	= "disabled";
for ($i = 0; $i < sizeof($documentos); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codDocumento='.$documentos[$i]->getCodigo().'&url='.$url.'&codTipoDoc='.$codTipoDoc);
	$grid->setValorCelula($i,3,\Zage\App\Util::mostraTamanhoLegivel($documentos[$i]->getTamanho()));
	$grid->setUrlCelula($i,4,ROOT_URL.'/Doc/docIndexTipo.php?id='.$uid);
	$grid->setUrlCelula($i,5,ROOT_URL.'/Doc/documentoExc.php?id='.$uid);
	$disabled	= false;
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
$urlIndexar			= ROOT_URL.'/Doc/docIndexTipo.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoDoc='.$codTipoDoc);

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
$tpl->set('URL_INDEXAR'		,$urlIndexar);
$tpl->set('DISABLED'		,$disabled);
$tpl->set('IC'				,'fa fa-tags');


#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

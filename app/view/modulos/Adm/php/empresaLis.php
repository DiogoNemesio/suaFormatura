<?php
// ################################################################################
// # Includes
// ################################################################################
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}

// ################################################################################
// # Resgata a variável ID que está criptografada
// ################################################################################
if (isset ( $_GET ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_GET ["id"] );
} elseif (isset ( $_POST ['id'] )) {
	$id = \Zage\App\Util::antiInjection ( $_POST ["id"] );
} elseif (isset ( $id )) {
	$id = \Zage\App\Util::antiInjection ( $id );
} else {
	\Zage\App\Erro::halt ( 'Falta de Parâmetros' );
}

// ################################################################################
// # Descompacta o ID
// ################################################################################
\Zage\App\Util::descompactaId($id);

// ################################################################################
// # Verifica se o usuário tem permissão no menu
// ################################################################################
$system->checaPermissao($_codMenu_);

// ################################################################################
// # Resgata a url desse script
// ################################################################################
$url = ROOT_URL . "/Adm/" . basename (__FILE__) . "?id=" . $id;

// ################################################################################
// # Resgata os dados do grid
// ################################################################################
try {
	$empresa = $em->getRepository('Entidades\ZgadmEmpresa' )->findBy(array ('codOrganizacao' => $system->getCodOrganizacao()));
}catch ( \Exception $e ) {
	\Zage\App\Erro::halt ( $e->getMessage());
}

// ################################################################################
// # Cria o objeto do Grid (bootstrap)
// ################################################################################
$grid = \Zage\App\Grid::criar ( \Zage\App\Grid\Tipo::TP_BOOTSTRAP, "GEmpresa");
$grid->adicionaTexto($tr->trans('NOME')		, 30, $grid::CENTER, 'nome');
$grid->adicionaTexto($tr->trans('FANTASIA')	, 20, $grid::CENTER, 'fantasia');
$grid->adicionaTexto($tr->trans('CNPJ')		, 15, $grid::CENTER, 'cnpj');
$grid->adicionaTexto($tr->trans('MATRIZ')	, 20, $grid::CENTER, 'codMatriz:fantasia');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine ( $empresa );

// ################################################################################
// # Popula os valores dos botões
// ################################################################################
for($i = 0; $i < sizeof ( $empresa ); $i ++) {
	$uid = \Zage\App\Util::encodeUrl ( '_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codEmpresa=' . $empresa [$i]->getCodigo () . '&url=' . $url );
	$grid->setUrlCelula($i, 4, ROOT_URL . '/Adm/empresaAlt.php?id=' . $uid);
	$grid->setUrlCelula($i, 5, ROOT_URL . '/Adm/empresaExc.php?id=' . $uid);
}

// ################################################################################
// # Gerar o código html do grid
// ################################################################################
try {
	$htmlGrid = $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt ($e->getMessage ());
}

// ################################################################################
// # Gerar a url de adicão
// ################################################################################
$urlAdd = ROOT_URL . '/Adm/empresaAlt.php?id=' . \Zage\App\Util::encodeUrl('_codMenu_=' . $_codMenu_ . '&_icone_=' . $_icone_ . '&codEmpresa=');

// ################################################################################
// # Carregando o template html
// ################################################################################
$tpl = new \Zage\App\Template ();
$tpl->load (HTML_PATH . 'templateLis.html');

// ################################################################################
// # Define os valores das variáveis
// ################################################################################
$tpl->set('GRID', $htmlGrid);
$tpl->set('NOME', $tr->trans('Empresas'));
$tpl->set('URLADD', $urlAdd);
$tpl->set('IC', $_icone_);

// ################################################################################
// # Por fim exibir a página HTML
// ################################################################################
$tpl->show ();

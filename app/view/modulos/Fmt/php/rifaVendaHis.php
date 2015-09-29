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
$url		= ROOT_URL . "/Fmt/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata informações dO GRID
#################################################################################
try {
	$info 		= $em->getRepository('Entidades\ZgfmtRifa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao() , 'indRifaEletronica' => '1'), array ('dataCadastro' => DESC));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}


#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GVendas");
$grid->adicionaTexto($tr->trans('RIFA'),				20, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('QTDE OBRIGATÓRIA'),	10, $grid::CENTER	,'qtdeObrigatorio');
$grid->adicionaTexto($tr->trans('QTDE VENDIDA'),		10, $grid::CENTER	,'');
$grid->adicionaIcone('null','',$tr->trans('META'));
$grid->adicionaIcone(null,'fa fa-search-plus',$tr->trans('Detalhar vendas'));
$grid->importaDadosDoctrine($info);
//$grid->importaDadosArray($info);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($info); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$info[$i]->getCodigo().'&url='.$url);
	
	$infoVenda 		= $em->getRepository('Entidades\ZgfmtRifaNumero')->findBy(array('codRifa' => $info[$i]->getCodigo() , 'codFormando' => $system->getCodUsuario()));
	$qtdeVenda 		= sizeof($infoVenda);
	$grid->setValorCelula($i,2,$qtdeVenda);
	
	if ($qtdeVenda >= $info[$i]->getQtdeObrigatorio()){
		$grid->setIconeCelula($i, 3, "fa fa-thumbs-up green");
	}else{
		$grid->setIconeCelula($i, 3, "fa fa-thumbs-down red");
	}
	
	$grid->setUrlCelula($i,4,ROOT_URL.'/Fmt/rifaVendaHisDetalhe.php?id='.$uid);
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
## Gerar o código html do grid
#################################################################################
$msg .= '<div class="alert alert-info">';
$msg .= 'Acompanhe as vendas das suas rifas eletrônicas.';
$msg .= '</div>';

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlVoltar			= ROOT_URL.'/Fmt/rifaLis.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);
$urlAtualizar		= ROOT_URL.'/Fmt/rifaFin.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$codRifa);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Histórico de Vendas"));
$tpl->set('URLVOLTAR'		,$urlVoltar);
$tpl->set('URLATUALIZAR'	,$urlAtualizar);
$tpl->set('MSG'				,$msg);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

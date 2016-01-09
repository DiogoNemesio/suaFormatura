<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}
#################################################################################
## Variáveis globais
#################################################################################
global $system,$em,$tr,$log;

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
$url		= ROOT_URL . "/Fin/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$adiantamentos		= \Zage\Fin\Adiantamento::listaSaldoPorPessoa($system->getCodOrganizacao());
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GAdiantamentos");
$grid->adicionaTexto($tr->trans('CPF/CGC'),				20	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NOME'),				50	,$grid::CENTER	,'NOME');
$grid->adicionaMoeda($tr->trans('SALDO'),				20	,$grid::CENTER	,'SALDO');
$grid->adicionaIcone("#", "fa fa-search blue", "Detalhar os adiantamentos");
$grid->adicionaIcone("#", "fa fa-exchange green", "Usar o saldo");
$grid->importaDadosArray($adiantamentos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($adiantamentos); $i++) {
	$aid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codPessoa='.$adiantamentos[$i]["COD_PESSOA"].'&url='.$url);
	

	#################################################################################
	## Aplicar a máscara do CPF / CNPJ
	#################################################################################
	if (strlen($adiantamentos[$i]["CGC"]) > 11) {
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CNPJ)->aplicaMascara($adiantamentos[$i]["CGC"]);
	}else{
		$infoCgc	= \Zage\App\Mascara::tipo(\Zage\App\Mascara\Tipo::TP_CPF)->aplicaMascara($adiantamentos[$i]["CGC"]);
	}
	$grid->setValorCelula($i, 0, $infoCgc);
	

	#################################################################################
	## Ajustar o link dos botões
	#################################################################################
	$urlView		= ROOT_URL . "/Fin/adiantamentoDet.php?id=".$aid;
	$urlUsu			= ROOT_URL . "/Fin/adiantamentoCadConta.php?id=".$aid;
	
	$grid->setUrlCelula($i, 3, $urlView);
	$grid->setUrlCelula($i, 4, $urlUsu);
	
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
## Gerar a url de filtro
#################################################################################
$urlFiltroData		= ROOT_URL . "/Fin/adiantamentoLisFiltroData.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codTipoFiltro='.$_SESSION["_CRLIS_codTipoFiltro"].'&dataFiltro='.$_SESSION["_CRLIS_dataFiltro"].'&mesFiltro='.$_SESSION["_CRLIS_mesFiltro"].'&dataIniFiltro='.$_SESSION["_CRLIS_dataIniFiltro"].'&dataFimFiltro='.$_SESSION["_CRLIS_dataFimFiltro"]);
$urlFiltro			= ROOT_URL . "/Fin/adiantamentoLisFiltro.php?id=".\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Contas a Receber"));
$tpl->set('FILTER_DATE_URL'	,$urlFiltroData);
$tpl->set('URL_FILTRO'		,$urlFiltro);
$tpl->set('IC'				,$_icone_);
$tpl->set('FILTER_URL'		,$url);
$tpl->set('DIVCENTRAL'		,$system->getDivCentral());
$tpl->set('CHECK_NAME'		,$checkboxName);

$tpl->set('COD_TIPO_FILTRO'	,$_SESSION["_CRLIS_codTipoFiltro"]);
$tpl->set('DATA_TIPO'		,$_SESSION["_CRLIS_dataTipo"]);
$tpl->set('DATA_FILTRO'		,$_SESSION["_CRLIS_dataFiltro"]);
$tpl->set('MES_FILTRO'		,$_SESSION["_CRLIS_mesFiltro"]);
$tpl->set('DATA_INI_FILTRO'	,$_SESSION["_CRLIS_dataIniFiltro"]);
$tpl->set('DATA_FIM_FILTRO'	,$_SESSION["_CRLIS_dataFimFiltro"]);
$tpl->set('VALOR_INI'		,$valorIni);
$tpl->set('VALOR_FIM'		,$valorFim);
$tpl->set('DESCRICAO'		,$descricao);
$tpl->set('CLIENTE'			,$cliente);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

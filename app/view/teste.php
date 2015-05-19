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
## Criar o relatório 
#################################################################################
$rel	= new \Zage\App\Relatorio();
$rel->adicionaFiltro("Data Inicial","01/01/2015");
$rel->adicionaFiltro("Data Final","30/01/2015");
$rel->adicionaFiltro("Fornecedor","Daniel");
$rel->adicionaFiltro("Categoria","Alimentação");
$rel->adicionaFiltro("Centro de Custo","Diogo Nemésio");
$rel->adicionaFiltro("Filtro Longo 1","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 2","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 3","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 4","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 5","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 6","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 7","Valor médio para um filtro");
$rel->adicionaFiltro("Filtro Longo 8","Valor médio para um filtro");

$rel->adicionaCabecalho("Relatório de Teste");
$rel->adicionaRodape();



#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$contas	= \Zage\Fin\ContaPagar::busca("01/01/2015","30/12/2015","V",null,null,null,null,null,null,null,null,null);
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GContasPagar");
$grid->adicionaTexto($tr->trans('STATUS'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('NÚMERO'),				7	,$grid::CENTER	,'numero');
$grid->adicionaTexto($tr->trans('DESCRIÇÃO'),			15	,$grid::CENTER	,'descricao');
$grid->adicionaTexto($tr->trans('FORNECEDOR'),			15	,$grid::CENTER	,'codPessoa:nome');
$grid->adicionaTexto($tr->trans('PARC.'),				5	,$grid::CENTER	,'');
$grid->adicionaMoeda($tr->trans('VALOR TOTAL'),			10	,$grid::CENTER	,'');
$grid->adicionaData($tr->trans('EMISSÃO'),				7	,$grid::CENTER	,'dataEmissao');
$grid->adicionaData($tr->trans('VENCIMENTO'),			8	,$grid::CENTER	,'dataVencimento');
$grid->adicionaTexto($tr->trans('FORMA'),				5	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('C. CUSTO'),			7	,$grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('CATEGORIA'),			7	,$grid::CENTER	,'');
$grid->importaDadosDoctrine($contas);

#################################################################################
## Gerar o código html do grid
#################################################################################
try {
	$htmlGrid	= $grid->getHtmlCode();
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

$html	= '
<body class="no-skin">		
<div class="row">
	<div class="col-sm-12 widget-container-span">
		<div class="widget-body">
			<div class="box-content">
				'.$htmlGrid.'
			</div><!--/span-->
		</div>
	</div>
</div>
</body>';


$rel->WriteHTML($html);

$rel->Output();

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(dirname( __FILE__ ) . "/teste.html");

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('DIVCENTRAL'			,$system->getDivCentral());

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>
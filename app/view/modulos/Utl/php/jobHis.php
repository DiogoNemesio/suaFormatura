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
## Verificar parâmetro obrigatório
#################################################################################
if (!isset($codJob)) \Zage\App\Erro::halt('Falta de Parâmetros 2');


#################################################################################
## Resgata as informações do banco
#################################################################################
$oJob	= $em->getRepository('\Entidades\ZgutlJob')->findOneBy(array('codigo' => $codJob));

if (!$oJob) {
	\Zage\App\Erro::halt($tr->trans('Job %s não encontrado !!!',array('%s' => $codJob)));
}

#################################################################################
## Resgata o Histórico
#################################################################################
$hist		= $em->getRepository('\Entidades\ZgutlJobHistorico')->findBy(array('codJob' => $codJob));

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GJobHis");
$grid->setPagingType(\Zage\App\Grid\Tipo::PG_NONE);
$grid->setFiltro(0);
$grid->setMostraInfo(0);
$grid->adicionaDataHora($tr->trans('Data Início'),			10, $grid::CENTER	,'dataInicio');
$grid->adicionaDataHora($tr->trans('Data Fim'),				10, $grid::CENTER	,'dataFim');
$grid->adicionaTexto($tr->trans('Status'),					10, $grid::CENTER	,'codStatus:nome');
$grid->adicionaTexto($tr->trans('Retorno'),					50, $grid::CENTER	,'');
$grid->importaDadosDoctrine($hist);

for ($i = 0; $i < sizeof($hist); $i++) {
	$grid->setValorCelula($i, 3, str_replace(PHP_EOL,"<BR>",$hist[$i]->getRetorno()));
}


$gHtml	= $grid->getHtmlCode();

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(\Zage\App\Util::getCaminhoCorrespondente(__FILE__, \Zage\App\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ID'					,$id);
$tpl->set('TITULO'				,$tr->trans('Histórico de Execução'));
$tpl->set('COD_JOB'				,$codJob);
$tpl->set('GRID'				,$gHtml);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();


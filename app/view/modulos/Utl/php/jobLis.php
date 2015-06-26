<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('./include.php');
}

global $em;

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
$url		= ROOT_URL . "/Utl/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$jobs	= $em->getRepository('Entidades\ZgutlJob')->findAll(); 
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GJobs");
$grid->adicionaTexto('#'					, 4	,$grid::CENTER	,'codigo');
$grid->adicionaTexto('Módulo'				, 9	,$grid::CENTER	,'codModulo:nome');
$grid->adicionaTexto('Atividade'			, 7	,$grid::CENTER	,'codAtividade:identificacao');
$grid->adicionaStatus('Ativo'				, 'indAtivo');
$grid->adicionaDataHora('Ultima Execução'	,10	,$grid::CENTER	,'dataUltimaExecucao');
$grid->adicionaTexto('Intervalo'			,10	,$grid::CENTER	,'intervalo');
$grid->adicionaDataHora('Proxima Execução'	,10	,$grid::CENTER	,'dataProximaExecucao');
$grid->adicionaTexto('Comando'				,16	,$grid::CENTER	,'comando');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($jobs);

for ($i = 0; $i < sizeof($jobs); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codJob='.$jobs[$i]->getCodigo().'&url='.$url);
	$grid->setUrlCelula($i,8,ROOT_URL.'/Utl/jobAlt.php?id='.$uid);
	$grid->setUrlCelula($i,9,ROOT_URL.'/Utl/jobExc.php?id='.$uid);
	
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
$urlAdd			= ROOT_URL.'/Utl/jobAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codJob=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Jobs (Agendamentos)"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
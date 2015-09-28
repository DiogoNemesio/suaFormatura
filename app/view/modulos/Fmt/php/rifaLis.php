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
$url		= ROOT_URL . '/Rhu/'. basename(__FILE__);

#################################################################################
## Resgata os dados do grid
#################################################################################
try {	
	$rifa	= $em->getRepository('Entidades\ZgfmtRifa')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()),array('codigo' => 'DESC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	
#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GCargo");
$grid->adicionaTexto($tr->trans('NOME'),	 		15, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('PRÊMIO'),			20, $grid::CENTER	,'premio');
$grid->adicionaDataHora($tr->trans('DATA SORTEIO'),	15, $grid::CENTER	,'dataSorteio');
$grid->adicionaTexto($tr->trans('QTD POR FORMANDO'),15, $grid::CENTER	,'qtdeObrigatorio');
$grid->adicionaMoeda($tr->trans('VALOR'),			15, $grid::CENTER	,'valorUnitario');
$grid->adicionaIcone(null,'fa fa-cog red',$tr->trans('Geração das rifas'));
$grid->adicionaIcone(null,'fa fa-history orange',$tr->trans('Histórico'));
$grid->adicionaIcone(null,'fa fa-usd green',$tr->trans('Financeiro'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($rifa);

#################################################################################
## Criar o objeto da data de hoje
#################################################################################
$hoje				= new \DateTime();


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($rifa); $i++) {
	$rid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa='.$rifa[$i]->getCodigo().'&url='.$url);
	
	
	#################################################################################
	## Criar o objeto da data do sorteio
	#################################################################################
	$dataSorteio		= $rifa[$i]->getDataSorteio();
	$podeGerar			= ($dataSorteio > $hoje)	? true : false;
	
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fmt/rifaGera.php?id='.$rid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Fmt/rifaResumo.php?id='.$rid);
	$grid->setUrlCelula($i,7,ROOT_URL.'/Fmt/rifaFin.php?id='.$rid);
	$grid->setUrlCelula($i,8,ROOT_URL.'/Fmt/rifaAlt.php?id='.$rid);
	$grid->setUrlCelula($i,9,"javascript:zgAbreModal('".ROOT_URL.'/Fmt/rifaExc.php?id='.$rid."');");
	
	if ($rifa[$i]->getIndRifaEletronica() == 1 || ($podeGerar == false)) {
		$grid->desabilitaCelula($i, 5);
	}
	
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
$urlAdd			= ROOT_URL.'/Fmt/rifaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codRifa=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Cadastro de rifas'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

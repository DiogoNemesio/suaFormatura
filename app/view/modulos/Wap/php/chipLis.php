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
global $em,$system,$tr;


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
$url		= ROOT_URL . "/Wap/". basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$chips	= $em->getRepository('Entidades\ZgwapChip')->findBy(array('codOrganizacao' => $system->getCodOrganizacao()));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GChips");
$grid->adicionaTexto('#'							,2	,$grid::CENTER	,'codigo');
$grid->adicionaTexto('Número'						,20	,$grid::CENTER	,'','fone');
$grid->adicionaTexto('Identificação'				,30	,$grid::CENTER	,'identificacao');
$grid->adicionaTexto('Status'						,20	,$grid::CENTER	,'codStatus:nome');
$grid->adicionaIcone(null,'fa fa-unlock green',$tr->trans('Registrar'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosDoctrine($chips);

for ($i = 0; $i < sizeof($chips); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codChip='.$chips[$i]->getCodigo().'&url='.$url);
	
	#################################################################################
	## Juntar o DDD e o Número para formatar correto a máscara
	#################################################################################
	$grid->setValorCelula($i, 1, $chips[$i]->getDdd() . $chips[$i]->getNumero());
	
	#################################################################################
	## Definir o endereço da url e o ícone de acordo com o status
	#################################################################################
	$codStatus	= $chips[$i]->getCodStatus()->getCodigo();
	$colAcao	= 4;
	if ($codStatus == "A") {
		$grid->setIconeCelula($i,$colAcao,'fa fa-lock red');
		$grid->setUrlCelula($i,$colAcao,ROOT_URL.'/Wap/chipBlo.php?id='.$uid);
		$grid->setDescricaoCelula($i, $colAcao, $tr->trans('Bloquear'));
	}elseif($codStatus	== "R") {
		$grid->setIconeCelula($i,$colAcao,'fa fa-tag grey');
		$grid->setUrlCelula($i,$colAcao,ROOT_URL.'/Wap/chipReg.php?id='.$uid);
		$grid->setDescricaoCelula($i, $colAcao, $tr->trans('Registrar'));
	}else{
		$grid->setIconeCelula($i,$colAcao,'fa fa-unlock green');
		$grid->setUrlCelula($i,$colAcao,ROOT_URL.'/Wap/chipDes.php?id='.$uid);
		$grid->setDescricaoCelula($i, $colAcao, $tr->trans('Desbloquear'));
	}
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/Wap/chipAlt.php?id='.$uid);
	$grid->setUrlCelula($i,6,ROOT_URL.'/Wap/chipExc.php?id='.$uid);
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
$urlAdd			= ROOT_URL.'/Wap/chipAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codChip=');

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans("Chips"));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

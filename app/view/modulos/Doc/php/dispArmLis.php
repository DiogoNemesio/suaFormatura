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
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . '/Doc/'. basename(__FILE__)."?id=".$id;

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	$dispositivos	= $em->getRepository('Entidades\ZgdocDispositivoArm')->findBy(array('codEmpresa' => $system->getCodEmpresa()), array('identificacao' => 'ASC'));
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}
	

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GDisp");
$grid->adicionaTexto($tr->trans('IDENTIFICACAO')		,10, $grid::CENTER	,'identificacao');
$grid->adicionaTexto($tr->trans('TIPO')					,15, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('LOCAL ATUAL')			,15, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('ENDERECO ATUAL')		,15, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('STATUS')				,10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DATA CADASTRO')		,10, $grid::CENTER	,'');
$grid->adicionaTexto($tr->trans('DATA ELIMINAÇÃO')		,10, $grid::CENTER	,'');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->adicionaIcone(null,'fa fa-barcode',$tr->trans('Imprimir etiqueta de código de barras'));
$grid->importaDadosDoctrine($dispositivos);

#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($dispositivos); $i++) {
	$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codDisp='.$dispositivos[$i]->getCodigo().'&url='.$url);
	$grid->setValorCelula($i,1,$dispositivos[$i]->getCodTipo()->getNome());
	
	if (is_object($dispositivos[$i]->getCodLocalAtual())) {
		$grid->setValorCelula($i,2,$dispositivos[$i]->getCodLocalAtual()->getNome());
	}
	if (is_object($dispositivos[$i]->getCodEnderecoAtual())) {
		$grid->setValorCelula($i,3,$dispositivos[$i]->getCodEnderecoAtual()->getNome());
	}
	
	$grid->setValorCelula($i,4,$dispositivos[$i]->getCodStatus()->getNome());
	
	if (is_object($dispositivos[$i]->getDataCadastro())) {
		$grid->setValorCelula($i,5,$dispositivos[$i]->getDataCadastro()->format($system->config["data"]["datetimeFormat"]));
	}
	
	if (is_object($dispositivos[$i]->getDataEliminacao())) {
		$grid->setValorCelula($i,6,$dispositivos[$i]->getDataEliminacao()->format($system->config["data"]["datetimeFormat"]));
	}
	if ($dispositivos[$i]->getCodStatus()->getCodigo() != 'E') {
		$grid->setUrlCelula($i,7,ROOT_URL.'/Doc/dispArmAlt.php?id='.$uid);
		$grid->setUrlCelula($i,8,ROOT_URL.'/Doc/dispArmExc.php?id='.$uid);
		$grid->setUrlCelula($i,9,'javascript:zgDownloadUrl(\''.ROOT_URL.'/Doc/dispArmBarCode.php?id='.$uid.'\');');
	}else{
		$grid->desabilitaCelula($i, 7);
		$grid->desabilitaCelula($i, 8);
		$grid->desabilitaCelula($i, 9);
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
$urlAdd			= ROOT_URL.'/Doc/dispArmAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codDisp=&url='.$url);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load(HTML_PATH . 'templateLis.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Dispositivo de Armazenamento'));
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

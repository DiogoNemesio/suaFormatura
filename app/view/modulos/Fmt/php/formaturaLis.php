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
global $em,$system,$tr;$log;

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
$url		= ROOT_URL . '/Fmt/'. basename(__FILE__);

#################################################################################
## Resgata as informações da organização que está cadastrando a Formatura
#################################################################################
$orgCad 		= $em->getRepository('Entidades\ZgadmOrganizacao')->findOneBy(array('codigo' => $system->getCodOrganizacao()));

#################################################################################
## Resgata os parâmetros passados pelo filtro
#################################################################################
if (isset($_POST['indFiltrado']))		$indFiltrado		= $_POST['indFiltrado'];
if (isset($_POST['codStatus']))			$codStatus			= $_POST['codStatus'];
if (isset($_POST['codUsuarioCad']))		$codUsuarioCad		= $_POST['codUsuarioCad'];
if (isset($_POST['dataCadIni']))		$dataCadIni			= $_POST['dataCadIni'];
if (isset($_POST['dataCadFim']))		$dataCadFim			= $_POST['dataCadFim'];
if (isset($_POST['dataPrevIni']))		$dataPrevIni		= $_POST['dataPrevIni'];
if (isset($_POST['dataPrevFim']))		$dataPrevFim		= $_POST['dataPrevFim'];
if (isset($_POST['instituicao']))		$instituicao		= $_POST['instituicao'];
if (isset($_POST['curso']))				$curso				= $_POST['curso'];
if (isset($_POST['cidade']))			$cidade				= $_POST['cidade'];

#################################################################################
## Ajustar valores dos arrays
#################################################################################
$codStatus			= (isset($codStatus)) 		? $codStatus		: array();
$codUsuarioCad		= (isset($codUsuarioCad)) 	? $codUsuarioCad	: array();

#################################################################################
## Verificar se está sendo filtrado
#################################################################################
$urlAtualizar	= ROOT_URL."/Fmt/formaturaLis.php?id=".$id;
if ($indFiltrado == 1){
	$btnLimpar = "<button id='a01ID' class='btn btn-xs btn-primary btn-white btn-default btn-round' title='Limpar filtro' onclick="."zgLoadUrl('".$urlAtualizar."');"."><i class='fa fa-times red bigger-140'></i> Limpar</button>";
}else{
	$btnLimpar = '';
}

#################################################################################
## Resgata os dados do grid
#################################################################################
try {
	if ($orgCad->getCodTipo()->getCodigo() == "ADM") {
		$formaturas	= \Zage\Fmt\Organizacao::listaFormaturas();
	}else{
		$formaturas	= \Zage\Fmt\Organizacao::listaFormaturaOrganizacao($system->getCodOrganizacao(),$codStatus,$codUsuarioCad,$dataCadIni,$dataCadFim,$dataPrevIni,$dataPrevFim,$instituicao,$curso,$cidade);
	}
} catch (\Exception $e) {
	\Zage\App\Erro::halt($e->getMessage());
}

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GFormatura");
$grid->adicionaTexto($tr->trans('NOME DA TURMA'),		20, $grid::CENTER	,'codOrganizacao:nome');
$grid->adicionaTexto($tr->trans('INSTITUIÇÃO'),			10, $grid::CENTER	,'codInstituicao:sigla');
$grid->adicionaTexto($tr->trans('CURSO'),				20, $grid::CENTER	,'codCurso:nome');
$grid->adicionaData($tr->trans('CONCLUSÃO')	,			8, $grid::CENTER	,'dataConclusao');
$grid->adicionaTexto($tr->trans('STATUS')	,			20, $grid::CENTER	,'codOrganizacao:codStatus:descricao');
//$grid->adicionaIcone(null,'fa fa-share-square-o green',$tr->trans('Acessar'));
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaIcone(null,'fa fa-ban red',$tr->trans('Cancelar'));
$grid->importaDadosDoctrine($formaturas);


#################################################################################
## Popula os valores dos botões
#################################################################################
for ($i = 0; $i < sizeof($formaturas); $i++) {
	$uid	= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao='.$formaturas[$i]->getCodOrganizacao()->getCodigo().'&url='.$url);
	$zid	= \Zage\App\Util::encodeUrl('_codOrganizacao='.$formaturas[$i]->getCodOrganizacao()->getCodigo());
	
	//Verificar se pode acessar
	if ($formaturas[$i]->getCodOrganizacao()->getCodStatus()->getCodigo() == "A" || $formaturas[$i]->getCodOrganizacao()->getCodStatus()->getCodigo() == "AA"){
		$linkOrg = 'javascript:zgWindowOpen(\''.ROOT_URL.'index.php?zid='.$zid.'\');';
		$grid->setValorCelula($i,0,'<a href="'.$linkOrg.'">'.$formaturas[$i]->getCodOrganizacao()->getNome().'</a>');
	}
	
	$grid->setUrlCelula($i,5,ROOT_URL.'/Fmt/formaturaAlt.php?id='.$uid);
	
	//Verificar se a formatura está cancelada
	if ($formaturas[$i]->getCodOrganizacao()->getCodStatus()->getCodigo() == "C"){
		$grid->desabilitaCelula($i,6);
	}else{
		$grid->setUrlCelula($i,6,"javascript:zgAbreModal('".ROOT_URL."/Fmt/formaturaExc.php?id=".$uid."');");
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
$urlAdd			= ROOT_URL.'/Fmt/formaturaAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codOrganizacao=');
$urlFiltro		= ROOT_URL.'/Fmt/formaturaLisFiltro.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\App\Template();
$tpl->load ( \Zage\App\Util::getCaminhoCorrespondente ( __FILE__, \Zage\App\ZWS::EXT_HTML ) );

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('GRID'			,$htmlGrid);
$tpl->set('NOME'			,$tr->trans('Formaturas'));
$tpl->set('BTN_LIMPAR'		,$btnLimpar);
$tpl->set('URLADD'			,$urlAdd);
$tpl->set('URLFILTRO'		,$urlFiltro);
$tpl->set('IC'				,$_icone_);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

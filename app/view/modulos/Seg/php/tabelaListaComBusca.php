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
## Resgata os parâmetros passados
#################################################################################
$sEcho		= (isset($_GET['sEcho'])) 			? \Zage\App\Util::antiInjection($_GET['sEcho']) 			: null; 
$sStart		= (isset($_GET['iDisplayStart']))	? \Zage\App\Util::antiInjection($_GET['iDisplayStart']) 	: null;
$sLimit		= (isset($_GET['iDisplayLength']))	? \Zage\App\Util::antiInjection($_GET['iDisplayLength']) 	: null;
$sSearch	= (isset($_GET['sSearch']))			? \Zage\App\Util::antiInjection($_GET['sSearch']) 			: null;
$iSortCol	= (isset($_GET['iSortCol_0']))		? \Zage\App\Util::antiInjection($_GET['iSortCol_0']) 		: null;
$sSortDir	= (isset($_GET['sSortDir_0']))		? \Zage\App\Util::antiInjection($_GET['sSortDir_0']) 		: null;


#################################################################################
## Resgata a url desse script
#################################################################################
$url		= ROOT_URL . "/Seg/".basename(__FILE__)."?id=".$id;

#################################################################################
## Cria o objeto do Grid (bootstrap)
#################################################################################
$grid			= \Zage\App\Grid::criar(\Zage\App\Grid\Tipo::TP_BOOTSTRAP,"GUsuario");
//$grid->setServerSideUrl($url);
$grid->adicionaTexto($tr->trans('USUÁRIO'),	12, $grid::CENTER	,'usuario');
$grid->adicionaTexto($tr->trans('NOME'), 	25, $grid::CENTER	,'nome');
$grid->adicionaTexto($tr->trans('STATUS'), 	10, $grid::CENTER	,'codRegiao:nome'	, 'r.nome');
$grid->adicionaTexto($tr->trans('EMAIL'), 	30, $grid::CENTER	,'email');
$grid->adicionaTexto($tr->trans('SEXO'), 	10, $grid::CENTER	,'sexo:descricao');
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\App\Grid\Coluna\Botao::MOD_REMOVE);
$grid->adicionaIcone(null,'icon-building',$tr->trans('Configurar acesso as empresas'));


#################################################################################
## Testar se é para criar o grid, ou para popular
## se a variável $sEcho for nula, criar o grid
#################################################################################
if ($sEcho === null) {
	
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
	$urlAdd			= ROOT_URL.'/Seg/usuarioAlt.php?id='.\Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&codUsuario=');
	
	#################################################################################
	## Carregando o template html
	#################################################################################
	$tpl	= new \Zage\App\Template();
	$tpl->load(HTML_PATH . 'templateLis.html');
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$tpl->set('GRID'			,$htmlGrid);
	$tpl->set('NOME'			,$tr->trans('Usuários'));
	$tpl->set('URLADD'			,$urlAdd);
	$tpl->set('IC'				,$_icone_);
	
	#################################################################################
	## Por fim exibir a página HTML
	#################################################################################
	$tpl->show();

}else{
	#################################################################################
	## Resgata os dados do grid
	#################################################################################
	try {
		
		if (isset($iSortCol) && ($iSortCol !== '')) {
			$orderBy	= $grid->getNomeCampo($iSortCol);
		}else{
			$orderBy	= null;
		}
		
		
		$usuarios		= \Zage\Seg\Usuario::busca($sSearch,$sStart,$sLimit,$orderBy,$sSortDir);
		$grid->importaDadosDoctrine($usuarios);
		
		for ($i = 0; $i < sizeof($usuarios); $i++) {
			$uid		= \Zage\App\Util::encodeUrl('_codMenu_='.$_codMenu_.'&_icone_='.$_icone_.'&usuario='.$usuarios[$i]->getUsuario().'&url='.$url);
			$grid->setUrlCelula($i,5,ROOT_URL.'/Seg/usuarioAlt.php?id='.$uid);
			$grid->setUrlCelula($i,6,ROOT_URL.'/Seg/usuarioExc.php?id='.$uid);
			$grid->setUrlCelula($i,7,ROOT_URL.'/Seg/usuarioEmpresaAlt.php?id='.$uid);
		}
	
		$html = $grid->getHtmlCode();
		$json = $grid->getJsonData(intval($sEcho),\Zage\Seg\Usuario::getTotalbusca($sSearch),$sStart,$sLimit);
		
		echo $json;

	} catch (\Exception $e) {
		\Zage\App\Erro::halt($e->getMessage());
	}
	
}
